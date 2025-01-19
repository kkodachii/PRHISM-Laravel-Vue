<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Rhu;
use Inertia\Inertia;
use App\Models\Barangays;
use App\Models\Medicines;
use Illuminate\Http\Request;
use App\Models\Generic_names;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicineUsageController extends Controller
{
    public function index(Request $request)
    {
        // Get the authenticated user
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

        // Get and filter medicine logs based on roles, search term, and filters
        $medicineLogs = $this->getMedicineLogs($startDate, $endDate, $searchTerm, $user, $selectedRhu, $selectedBarangay);
        $medicineUsage = $this->processMedicineLogs($medicineLogs);

        // Pagination
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $medicineUsage->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedLogs = new LengthAwarePaginator(
            $currentItems,
            $medicineUsage->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Generate chart data
        $medicineUsageChart = $this->getChartData($startDate, $endDate, $searchTerm, $user, $selectedRhu, $selectedBarangay);
        $barangays = Barangays::all();
        $rhus = Rhu::all();

        return Inertia::render('DashboardPages/MedicineUsagePage', [
            'medicineusagelog' => $paginatedLogs,
            'medicineUsageChart' => $medicineUsageChart,
            'userRole' => $user->role->name,
            'barangays' => $barangays,
            'rhus' => $rhus,
        ]);
    }

    private function getMedicineLogs($startDate, $endDate, $searchTerm = null, $user, $selectedRhu = null, $selectedBarangay = null)
    {
        $medicineLogs = Activity::where(function ($query) {
            $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                ->orWhere('subject_type', 'App\\Models\\Requests');
        })
            ->whereBetween('created_at', [$startDate, $endDate]);
    
        // Role-based filtering
        if ($user->role->name === 'Super Admin') {
            // No additional filtering needed
        } elseif ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $medicineLogs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $medicineLogs->where(function ($query) use ($user) {
                $query->where('properties->old->user_id', $user->id)
                      ->orWhere('properties->new->user_id', $user->id)
                      ->orWhere('causer_id', $user->id);
            });
        }
    
        // Apply search functionality to both old and new properties
        if ($searchTerm) {
            $medicineLogs->where(function ($query) use ($searchTerm) {
                $query->where('properties->old->name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('properties->new->name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('properties->old->brand', 'like', '%' . $searchTerm . '%')
                    ->orWhere('properties->new->brand', 'like', '%' . $searchTerm . '%');
            });
        }
    
        // Filter by RHU and Barangay
        if ($selectedRhu && $selectedBarangay) {
            $medicineLogs->where(function ($query) use ($selectedRhu, $selectedBarangay) {
                $query->where(function ($q) use ($selectedRhu) {
                    $q->where('properties->old->rhu_id', $selectedRhu)
                        ->orWhere('properties->new->rhu_id', $selectedRhu);
                })->where(function ($q) use ($selectedBarangay) {
                    $q->where('properties->old->barangay_id', $selectedBarangay)
                        ->orWhere('properties->new->barangay_id', $selectedBarangay);
                });
            });
        } elseif ($selectedRhu) {
            $medicineLogs->where(function ($query) use ($selectedRhu) {
                $query->where('properties->old->rhu_id', $selectedRhu)
                    ->orWhere('properties->new->rhu_id', $selectedRhu);
            });
        } elseif ($selectedBarangay) {
            $medicineLogs->where(function ($query) use ($selectedBarangay) {
                $query->where('properties->old->barangay_id', $selectedBarangay)
                    ->orWhere('properties->new->barangay_id', $selectedBarangay);
            });
        }
    
        // Filter out only logs related to medicines
        $medicineLogs->where(function ($query) {
            $query->where('properties->old->type', 'medicine')
                ->orWhere('properties->new->type', 'medicine');
        });
    
        return $medicineLogs->get();
    }


    private function processMedicineLogs($medicineLogs)
    {
        return $medicineLogs->map(function ($log) {
            $properties = json_decode($log->properties, true);
            $quantityConsumed = 0;
            $medicineName = 'Unknown';
            $rhu = null;
            $barangay = null;

            if ($log->subject_type === 'App\\Models\\Midwife_inventories' && isset($properties['old'])) {
                $oldData = $properties['old'];
                $newData = $properties['new'] ?? [];
                $quantityConsumed = ($oldData['quantity'] ?? 0) - ($newData['quantity'] ?? 0);
                $medicineName = $oldData['name'] ?? 'Unknown';

                $rhu = Rhu::find($oldData['rhu_id']);
                $barangay = Barangays::find($oldData['barangay_id']);
            } elseif ($log->subject_type === 'App\\Models\\Requests' && isset($properties['old'])) {
                $oldData = $properties['old'];
                $newData = $properties['new'] ?? [];
                $quantityConsumed = ($oldData['quantity'] ?? 0) - ($newData['quantity'] ?? 0);
                $medicineName = $newData['name'] ?? 'Unknown';

                $rhu = Rhu::find($newData['rhu_id']);
                $barangay = Barangays::find($oldData['barangay_id']);
            }

            return [
                'medicine_name' => $medicineName,
                'quantity_difference' => $quantityConsumed > 0 ? $quantityConsumed : 0,
                'date_updated' => Carbon::parse($log->created_at)->format('F j, Y g:i A'),
                'rhu_name' => isset($rhu) ? $rhu->rhu_name : 'Unknown',
                'barangay_name' => isset($barangay) ? $barangay->barangay_name : 'Unknown',
            ];
        })->filter();
    }

    private function getChartData($startDate, $endDate, $searchTerm = null, $user, $selectedRhu = null, $selectedBarangay = null)
    {
        $medicineUsageChart = [];
        $currentDate = clone $startDate;
    
        while ($currentDate <= $endDate) {
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();
    
            $dailyLogs = $this->getMedicineLogs($dayStart, $dayEnd, $searchTerm, $user, $selectedRhu, $selectedBarangay);
            $processedLogs = $this->processMedicineLogs($dailyLogs);
    
            $medicineData = [];
            foreach ($processedLogs as $log) {
                if (!isset($medicineData[$log['medicine_name']])) {
                    $medicineData[$log['medicine_name']] = 0;
                }
                $medicineData[$log['medicine_name']] += $log['quantity_difference'];
            }
    
            $medicineUsageChart[] = [
                'date' => $dayStart->format('F j, Y'),
                'quantity' => $processedLogs->sum('quantity_difference'),
                'medicines' => $medicineData
            ];
    
            $currentDate->addDay();
        }
    
        return $medicineUsageChart;
    }
    

    public function searchMedicine(Request $request)
    {
        $searchTerm = $request->input('search');
        $genericNames = Generic_names::where('generic_name', 'like', '%' . $searchTerm . '%')
            ->select('generic_name')
            ->distinct()
            ->limit(10)
            ->get()
            ->pluck('generic_name');

        return response()->json($genericNames);
    }
}
