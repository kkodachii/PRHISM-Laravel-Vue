<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Barangays;
use App\Models\Medicines;
use App\Models\Equipments;
use Illuminate\Http\Request;
use App\Models\Medical_supplies;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

class PredictionController extends Controller
{
    public function index(Request $request)
    {
        $duration = $request->input('duration', '7_days');
        $user = Auth::user();

        $medicines = Medicines::with('generic_name')->get()->pluck('generic_name.generic_name')->unique()->values()->all();
        $equipments = Equipments::distinct()->pluck('equipment_name')->all();
        $medicalSupplies = Medical_supplies::distinct()->pluck('med_sup_name')->all();

        $medicineRestock = $this->processPredictions($medicines, 'medicines', $duration, $user);
        $equipmentRestock = $this->processPredictions($equipments, 'equipments', $duration, $user);
        $medicalSupplyRestock = $this->processPredictions($medicalSupplies, 'medical_supplies', $duration, $user);

        $allRestockItems = array_merge($medicineRestock, $equipmentRestock, $medicalSupplyRestock);
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($allRestockItems, ($currentPage - 1) * $perPage, $perPage);
        $paginatedItems = new LengthAwarePaginator(
            $currentItems,
            count($allRestockItems),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        Log::info("Final Restock Data to Vue:", $allRestockItems);

        return Inertia::render('DashboardPages/PredictionsPage', [
            'itemsRestock' => $paginatedItems,
            'duration' => $duration,
        ]);
    }

    private function processPredictions($items, $type, $duration, $user)
    {
        Log::info("Processing predictions for type: {$type} with duration: {$duration}");

        $restockData = [];
        foreach ($items as $item) {
            $itemData = $this->getConsumptionHistory($item, $type, $duration, $user);

            if (count($itemData) < 2) continue; // Ensure there are enough data points

            $predictedUsage = max(0, $this->calculateLinearRegression($itemData, $duration));
            $currentStock = $this->getCurrentStock($item, $type);

            Log::info("Item: {$item}, Predicted Usage: {$predictedUsage}, Current Stock: {$currentStock}");

            // Calculate required restock
            $requiredRestock = max(0, $predictedUsage - $currentStock);
            Log::info("Required Restock for {$item}: {$requiredRestock}");

            // Add to restock data
            $restockData[] = [
                'name' => $item,
                'current_stock' => $currentStock,
                'predicted_usage' => $predictedUsage,
                'required_restock' => $requiredRestock,
                'type' => $type
            ];
        }

        Log::info("Restock data generated for type: {$type}: ", $restockData);
        return $restockData;
    }

    private function getCurrentStock($item, $type)
    {
        if ($type === 'medicines') {
            return Medicines::whereHas('generic_name', function ($query) use ($item) {
                $query->where('generic_name', $item);
            })->pluck('quantity')->first();
        } elseif ($type === 'equipments') {
            return Equipments::where('equipment_name', $item)->pluck('quantity')->first();
        } elseif ($type === 'medical_supplies') {
            return Medical_supplies::where('med_sup_name', $item)->pluck('quantity')->first();
        }
        return null;
    }

    private function calculateLinearRegression($itemData, $duration)
    {
        $n = count($itemData);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
    
        // Apply Exponential Moving Average (EMA) weighting
        $alpha = 2 / ($n + 1); // Smoothing factor for EMA
        $emaY = $itemData[0]['y']; // Initialize EMA with the first data point
    
        foreach ($itemData as $i => $data) {
            $x = $i + 1;
            $y = $data['y'];
    
            // Update EMA for each data point
            $emaY = ($y * $alpha) + ($emaY * (1 - $alpha));
    
            // Perform linear regression calculations with the EMA
            $sumX += $x;
            $sumY += $emaY;
            $sumXY += $x * $emaY;
            $sumX2 += $x * $x;
        }
    
        // Calculate slope (m) and intercept (b)
        $m = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $b = ($sumY - $m * $sumX) / $n;
    
        // Predict future usage based on the weighted EMA
        $futureX = $this->getDaysFromDuration($duration);
        $predictedY = $m * $futureX + $b;
    
        return $predictedY;
    }

    private function getDaysFromDuration($duration)
    {
        switch ($duration) {
            case '15_days':
                return 15;
            case '30_days':
                return 30;
            case '6_months':
                return 180;
            case '1_year':
                return 365;
            default:
                return 7;
        }
    }

    private function getConsumptionHistory($item, $type, $duration, $user)
    {
        $startDate = $this->getStartDateFromDuration($duration);
    
        if ($type === 'medicines') {
            $history = $this->getMedicineConsumptionHistory($item, $startDate, $user);
            Log::info("Medicine consumption history for {$item}: ", $history);
            return $history;
        } elseif ($type === 'equipments') {
            $history = $this->getEquipmentConsumptionHistory($item, $startDate, $user);
            Log::info("Equipment consumption history for {$item}: ", $history);
            return $history;
        } elseif ($type === 'medical_supplies') {
            $history = $this->getMedicalSupplyConsumptionHistory($item, $startDate, $user);
            Log::info("Medical supply consumption history for {$item}: ", $history);
            return $history;
        }
        
        return [];
    }
    
    private function getStartDateFromDuration($duration)
    {
        switch ($duration) {
            case '15_days':
                return now()->subDays(15);
            case '30_days':
                return now()->subDays(30);
            case '6_months':
                return now()->subMonths(6);
            case '1_year':
                return now()->subYear();
            default:
                return now()->subDays(7);
        }
    }

    private function getMedicineConsumptionHistory($medicine, $startDate, $user)
    {
        $medicineLogs = Activity::where(function ($query) {
                $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                      ->orWhere('subject_type', 'App\\Models\\Requests');
            })
            ->whereBetween('created_at', [$startDate, now()])
            ->where(function ($query) use ($medicine) {
                $query->where('properties->old->name', $medicine)
                      ->orWhere('properties->new->name', $medicine);
            });

        // Access level filtering
        if ($user->role->name === 'Super Admin') {
            // No additional filtering needed
        } elseif ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $medicineLogs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $medicineLogs->where('properties->old->user_id', $user->id);
        }

        $medicineLogs = $medicineLogs->get();
        return $this->processMedicineLogs($medicineLogs);
    }

    private function processMedicineLogs($medicineLogs)
    {
        return $medicineLogs->map(function ($log) {
            $properties = json_decode($log->properties, true);
            
            if (is_null($properties['old']) || !isset($properties['new']['quantity'])) {
                return null;
            }
    
            $quantityConsumed = ($properties['old']['quantity'] ?? 0) - ($properties['new']['quantity'] ?? 0);
            
            return [
                'ds' => $log->created_at->format('Y-m-d'), 
                'y' => $quantityConsumed
            ];
        })->filter()->toArray();
    }

    private function getEquipmentConsumptionHistory($equipment, $startDate, $user)
    {
        $equipmentLogs = Activity::where(function ($query) {
                $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                      ->orWhere('subject_type', 'App\\Models\\Requests');
            })
            ->whereBetween('created_at', [$startDate, now()])
            ->where(function ($query) use ($equipment) {
                $query->where('properties->old->name', $equipment)
                      ->orWhere('properties->new->name', $equipment);
            });

        // Access level filtering
        if ($user->role->name === 'Super Admin') {
            // No additional filtering needed
        } elseif ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $equipmentLogs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $equipmentLogs->where('properties->old->user_id', $user->id);
        }

        $equipmentLogs = $equipmentLogs->get();
        return $this->processEquipmentLogs($equipmentLogs);
    }

    private function processEquipmentLogs($equipmentLogs)
    {
        return $equipmentLogs->map(function ($log) {
            $properties = json_decode($log->properties, true);
            
            if (is_null($properties['old']) || !isset($properties['new']['quantity'])) {
                return null;
            }
    
            $quantityConsumed = ($properties['old']['quantity'] ?? 0) - ($properties['new']['quantity'] ?? 0);
    
            return [
                'ds' => $log->created_at->format('Y-m-d'), 
                'y' => $quantityConsumed
            ];
        })->filter()->toArray();
    }
    private function getMedicalSupplyConsumptionHistory($supply, $startDate, $user)
    {
        $medicalSupplyLogs = Activity::where(function ($query) {
                $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                      ->orWhere('subject_type', 'App\\Models\\Requests');
            })
            ->whereBetween('created_at', [$startDate, now()])
            ->where(function ($query) use ($supply) {
                $query->where('properties->old->name', $supply)
                      ->orWhere('properties->new->name', $supply);
            });

        // Access level filtering
        if ($user->role->name === 'Super Admin') {
            // No additional filtering needed
        } elseif ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $medicalSupplyLogs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $medicalSupplyLogs->where('properties->old->user_id', $user->id);
        }

        $medicalSupplyLogs = $medicalSupplyLogs->get();
        return $this->processMedicalSupplyLogs($medicalSupplyLogs);
    }

    private function processMedicalSupplyLogs($medicalSupplyLogs)
    {
        return $medicalSupplyLogs->map(function ($log) {
            $properties = json_decode($log->properties, true);
            
            if (is_null($properties['old']) || !isset($properties['new']['quantity'])) {
                return null;
            }
    
            $quantityConsumed = ($properties['old']['quantity'] ?? 0) - ($properties['new']['quantity'] ?? 0);
    
            return [
                'ds' => $log->created_at->format('Y-m-d'), 
                'y' => $quantityConsumed
            ];
        })->filter()->toArray();
    }

    public function store(Request $request)
{
    // Validate the request
    $validated = $request->validate([
        'duration' => 'required|string|in:7_days,15_days,30_days,6_months,1_year',
        // Add other validation rules as needed
    ]);

    try {
        // Process the prediction request
        $user = Auth::user();
        
        // Get the items based on the type
        $medicines = Medicines::with('generic_name')->get()->pluck('generic_name.generic_name')->unique()->values()->all();
        $equipments = Equipments::distinct()->pluck('equipment_name')->all();
        $medicalSupplies = Medical_supplies::distinct()->pluck('med_sup_name')->all();

        // Process predictions for each type
        $medicineRestock = $this->processPredictions($medicines, 'medicines', $validated['duration'], $user);
        $equipmentRestock = $this->processPredictions($equipments, 'equipments', $validated['duration'], $user);
        $medicalSupplyRestock = $this->processPredictions($medicalSupplies, 'medical_supplies', $validated['duration'], $user);

        // Combine all restock data
        $allRestockItems = array_merge($medicineRestock, $equipmentRestock, $medicalSupplyRestock);

        // Create pagination for the items
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($allRestockItems, ($currentPage - 1) * $perPage, $perPage);
        $paginatedItems = new LengthAwarePaginator(
            $currentItems,
            count($allRestockItems),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Return Inertia response
        return Inertia::render('DashboardPages/PredictionsPage', [
            'itemsRestock' => $paginatedItems,
            'duration' => $validated['duration'],
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in prediction store method: ' . $e->getMessage());
        
        return back()->withErrors([
            'error' => 'Error generating predictions: ' . $e->getMessage()
        ]);
    }
}
}