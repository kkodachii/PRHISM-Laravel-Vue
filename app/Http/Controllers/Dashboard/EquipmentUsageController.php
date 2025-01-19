<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Rhu;
use Inertia\Inertia;
use App\Models\Barangays;
use App\Models\Equipments;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

class EquipmentUsageController extends Controller
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

        // Get and filter equipment logs based on roles, RHU, barangay, and search term
        $equipmentLogs = $this->getEquipmentLogs($startDate, $endDate, $searchTerm, $user, $selectedRhu, $selectedBarangay);
        $equipmentUsage = $this->processEquipmentLogs($equipmentLogs);

        // Pagination
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $equipmentUsage->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedLogs = new LengthAwarePaginator(
            $currentItems,
            $equipmentUsage->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $rhus = Rhu::all();

        // Generate chart data
        $equipmentUsageChart = $this->getChartData($startDate, $endDate, $searchTerm, $user, $selectedRhu, $selectedBarangay);
        $barangays = Barangays::all();

        return Inertia::render('DashboardPages/EquipmentUsagePage', [
            'equipmentlog' => $paginatedLogs,
            'equipmentUsageChart' => $equipmentUsageChart,
            'userRole' => $user->role->name,
            'rhus' => $rhus,
            'barangays' => $barangays,
        ]);
    }

    private function getEquipmentLogs($startDate, $endDate, $searchTerm = null, $user, $selectedRhu = null, $selectedBarangay = null)
    {
        $equipmentLogs = Activity::where(function ($query) {
            $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                ->orWhere('subject_type', 'App\\Models\\Requests');
        })->whereBetween('created_at', [$startDate, $endDate]);

        if ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $equipmentLogs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $equipmentLogs->where(function ($query) use ($user) {
                $query->where('properties->old->user_id', $user->id)
                      ->orWhere('properties->new->user_id', $user->id)
                      ->orWhere('causer_id', $user->id);
            });
        }

        if ($searchTerm) {
            $equipmentLogs->where(function ($query) use ($searchTerm) {
                $query->where('properties->old->name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('properties->new->name', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($selectedRhu && $selectedBarangay) {
            $equipmentLogs->where(function ($query) use ($selectedRhu, $selectedBarangay) {
                $query->where(function ($q) use ($selectedRhu) {
                    $q->where('properties->old->rhu_id', $selectedRhu)
                      ->orWhere('properties->new->rhu_id', $selectedRhu);
                })->where(function ($q) use ($selectedBarangay) {
                    $q->where('properties->old->barangay_id', $selectedBarangay)
                      ->orWhere('properties->new->barangay_id', $selectedBarangay);
                });
            });
        } elseif ($selectedRhu) {
            $equipmentLogs->where(function ($query) use ($selectedRhu) {
                $query->where('properties->old->rhu_id', $selectedRhu)
                      ->orWhere('properties->new->rhu_id', $selectedRhu);
            });
        } elseif ($selectedBarangay) {
            $equipmentLogs->where(function ($query) use ($selectedBarangay) {
                $query->where('properties->old->barangay_id', $selectedBarangay)
                      ->orWhere('properties->new->barangay_id', $selectedBarangay);
            });
        }

        $equipmentLogs->where(function ($query) {
            $query->where('properties->old->type', 'equipment')
                  ->orWhere('properties->new->type', 'equipment');
        });

        return $equipmentLogs->get();
    }

    private function processEquipmentLogs($equipmentLogs)
    {
        return $equipmentLogs->map(function ($log) {
            $properties = json_decode($log->properties, true);
            $quantityDifference = 0;
            $equipmentName = 'Unknown';
            $rhu = null;
            $barangay = null;

            // Handle Midwife_inventories logs
            if ($log->subject_type === 'App\\Models\\Midwife_inventories' && isset($properties['old'])) {
                $oldData = $properties['old'];
                $newData = $properties['new'] ?? [];

                // Calculate the quantity difference
                $quantityDifference = ($oldData['quantity'] ?? 0) - ($newData['quantity'] ?? 0);
                $equipmentName = $oldData['name'] ?? 'Unknown';

                // Fetching RHU and Barangay names
                $rhu = Rhu::find($oldData['rhu_id']);
                $barangay = Barangays::find($oldData['barangay_id']);
            }

            // Handle Requests logs
            elseif ($log->subject_type === 'App\\Models\\Requests' && isset($properties['old'])) {
                $oldData = $properties['old'];
                $newData = $properties['new'] ?? [];

                // Calculate the quantity difference
                $quantityDifference = ($oldData['quantity'] ?? 0) - ($newData['quantity'] ?? 0);
                $equipmentName = $newData['name'] ?? $oldData['equipment_name'] ?? 'Unknown';
                // Fetching RHU and Barangay names
                $rhu = Rhu::find($newData['rhu_id']);
                $barangay = Barangays::find($oldData['barangay_id']);
            }

            if ($quantityDifference > 0) {
                return [
                    'equipment_name' => $equipmentName,
                    'quantity_difference' => $quantityDifference,
                    'date_updated' => Carbon::parse($log->updated_at)->format('F j, Y g:i A'),
                    'rhu_name' => isset($rhu) ? $rhu->rhu_name : 'Unknown',
                    'barangay_name' => isset($barangay) ? $barangay->barangay_name : 'Unknown',
                ];
            }

            return null;
        })->filter();
    }

    private function getChartData($startDate, $endDate, $searchTerm = null, $user, $selectedRhu = null, $selectedBarangay = null)
    {
        $equipmentUsageChart = [];
        $startDay = clone $startDate;
    
        while ($startDay <= $endDate) {
            $dayStart = $startDay->copy()->startOfDay();
            $dayEnd = $startDay->copy()->endOfDay();
    
            $dailyLogs = $this->getEquipmentLogs($dayStart, $dayEnd, $searchTerm, $user, $selectedRhu, $selectedBarangay);
            $processedLogs = $this->processEquipmentLogs($dailyLogs);
    
            $equipmentData = [];
            foreach ($processedLogs as $log) {
                if (!isset($equipmentData[$log['equipment_name']])) {
                    $equipmentData[$log['equipment_name']] = 0;
                }
                $equipmentData[$log['equipment_name']] += $log['quantity_difference'];
            }
    
            $equipmentUsageChart[] = [
                'date' => $dayStart->format('F j, Y'),
                'quantity' => $processedLogs->sum('quantity_difference'),
                'equipments' => $equipmentData
            ];
    
            $startDay->addDay();
        }
    
        return $equipmentUsageChart;
    }

    public function searchEquipment(Request $request)
    {
        $searchTerm = $request->input('search');
        $equipments = Equipments::where('equipment_name', 'like', '%' . $searchTerm . '%')
            ->select('equipment_name')
            ->distinct()
            ->limit(10)
            ->get()
            ->pluck('equipment_name');

        return response()->json($equipments);
    }
}
