<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Rhu;
use Inertia\Inertia;
use App\Models\Barangays;
use Illuminate\Http\Request;
use App\Models\Medical_supplies;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicalSupplyUsageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(7)->startOfDay();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $searchTerm = $request->input('search');
        $selectedRhu = $request->input('rhu');
        $selectedBarangay = $request->input('barangay');

        $medicalSupplyLogs = $this->getMedicalSupplyLogs($startDate, $endDate, $searchTerm, $user, $selectedRhu, $selectedBarangay);
        $medicalSupplyUsage = $this->processMedicalSupplyLogs($medicalSupplyLogs);

        // Pagination
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $medicalSupplyUsage->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedLogs = new LengthAwarePaginator(
            $currentItems,
            $medicalSupplyUsage->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $medicalSupplyUsageChart = $this->getChartData($startDate, $endDate, $searchTerm, $user, $selectedRhu, $selectedBarangay);
        $barangays = Barangays::all();
        $rhus = Rhu::all();

        return Inertia::render('DashboardPages/MedicalSupplyPage', [
            'medicalsupplylog' => $paginatedLogs,
            'medicalSupplyUsageChart' => $medicalSupplyUsageChart,
            'userRole' => $user->role->name,
            'barangays' => $barangays,
            'rhus' => $rhus,
        ]);
    }

    private function getMedicalSupplyLogs($startDate, $endDate, $searchTerm = null, $user, $selectedRhu = null, $selectedBarangay = null)
    {
        $medicalSupplyLogs = Activity::where(function ($query) {
            $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                ->orWhere('subject_type', 'App\\Models\\Requests');
        })
        ->whereBetween('created_at', [$startDate, $endDate]);

        if ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $medicalSupplyLogs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $medicalSupplyLogs->where(function ($query) use ($user) {
                $query->where('properties->old->user_id', $user->id)
                      ->orWhere('properties->new->user_id', $user->id)
                      ->orWhere('causer_id', $user->id);
            });
        }

        if ($searchTerm) {
            $medicalSupplyLogs->where(function ($query) use ($searchTerm) {
                $query->where('properties->old->name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('properties->new->name', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($selectedRhu && $selectedBarangay) {
            $medicalSupplyLogs->where(function ($query) use ($selectedRhu, $selectedBarangay) {
                $query->where(function ($q) use ($selectedRhu) {
                    $q->where('properties->old->rhu_id', $selectedRhu)
                      ->orWhere('properties->new->rhu_id', $selectedRhu);
                })->where(function ($q) use ($selectedBarangay) {
                    $q->where('properties->old->barangay_id', $selectedBarangay)
                      ->orWhere('properties->new->barangay_id', $selectedBarangay);
                });
            });
        } elseif ($selectedRhu) {
            $medicalSupplyLogs->where(function ($query) use ($selectedRhu) {
                $query->where('properties->old->rhu_id', $selectedRhu)
                      ->orWhere('properties->new->rhu_id', $selectedRhu);
            });
        } elseif ($selectedBarangay) {
            $medicalSupplyLogs->where(function ($query) use ($selectedBarangay) {
                $query->where('properties->old->barangay_id', $selectedBarangay)
                      ->orWhere('properties->new->barangay_id', $selectedBarangay);
            });
        }

        $medicalSupplyLogs->where(function ($query) {
            $query->where('properties->old->type', 'medical_supply')
                  ->orWhere('properties->new->type', 'medical_supply');
        });

        return $medicalSupplyLogs->get();
    }

    private function processMedicalSupplyLogs($medicalSupplyLogs)
    {
        return $medicalSupplyLogs->map(function ($log) {
            $properties = json_decode($log->properties, true);
            $quantityConsumed = 0;
            $supplyName = 'Unknown';
            $rhu = null;
            $barangay = null;

            if ($log->subject_type === 'App\\Models\\Midwife_inventories' && isset($properties['old'])) {
                $oldData = $properties['old'];
                $newData = $properties['new'] ?? [];
                $quantityConsumed = ($oldData['quantity'] ?? 0) - ($newData['quantity'] ?? 0);
                $supplyName = $oldData['name'] ?? 'Unknown';

                // Fetching RHU and Barangay names
                $rhu = Rhu::find($oldData['rhu_id']);
                $barangay = Barangays::find($oldData['barangay_id']);
            } elseif ($log->subject_type === 'App\\Models\\Requests' && isset($properties['old'])) {
                $oldData = $properties['old'];
                $newData = $properties['new'] ?? [];
                $quantityConsumed = ($oldData['quantity'] ?? 0) - ($newData['quantity'] ?? 0);
                $supplyName = $newData['name'] ?? 'Unknown';

                // Fetching RHU and Barangay names
                $rhu = Rhu::find($newData['rhu_id']);
                $barangay = Barangays::find($oldData['barangay_id']);
            }

            return [
                'med_sup_name' => $supplyName,
                'quantity_difference' => $quantityConsumed > 0 ? $quantityConsumed : 0,
                'date_updated' => Carbon::parse($log->created_at)->format('F j, Y g:i A'),
                'rhu_name' => isset($rhu) ? $rhu->rhu_name : 'Unknown',
                'barangay_name' => isset($barangay) ? $barangay->barangay_name : 'Unknown',
            ];
        })->filter();
    }

    private function getChartData($startDate, $endDate, $searchTerm = null, $user, $selectedRhu = null, $selectedBarangay = null)
    {
        $medicalSupplyUsageChart = [];
        $currentDate = clone $startDate;
    
        while ($currentDate <= $endDate) {
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();
    
            $dailyLogs = $this->getMedicalSupplyLogs($dayStart, $dayEnd, $searchTerm, $user, $selectedRhu, $selectedBarangay);
            $processedLogs = $this->processMedicalSupplyLogs($dailyLogs);
    
            $dailyTotal = 0;
            $supplyData = [];
    
            foreach ($processedLogs as $log) {
                $dailyTotal += $log['quantity_difference'];
    
                if (!isset($supplyData[$log['med_sup_name']])) {
                    $supplyData[$log['med_sup_name']] = 0;
                }
                $supplyData[$log['med_sup_name']] += $log['quantity_difference'];
            }
    
            $medicalSupplyUsageChart[] = [
                'date' => $dayStart->format('F j, Y'),
                'quantity' => $dailyTotal,
                'supplies' => $supplyData,
            ];
    
            $currentDate->addDay();
        }
    
        return $medicalSupplyUsageChart;
    }
    

    public function searchMedicalSupply(Request $request)
    {
        $searchTerm = $request->input('search');
        $medicalSupplies = Medical_supplies::where('med_sup_name', 'like', '%' . $searchTerm . '%')
            ->select('med_sup_name')
            ->distinct()
            ->limit(10)
            ->get()
            ->pluck('med_sup_name');

        return response()->json($medicalSupplies);
    }
}
