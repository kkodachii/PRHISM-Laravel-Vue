<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Medicines;
use App\Models\Equipments;
use App\Models\Medical_supplies;
use App\Models\Midwife_inventories;
use App\Models\Medical_categories;
use App\Models\Barangays;
use App\Models\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {

        // Get the authenticated user
        $user = Auth::user();
        $authBarangayId = $user->barangay_id;
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $currentDate = Carbon::now()->endOfMonth();

        // Correctly passing $type parameter for each category
        $equipmentLogs = $this->getFilteredEquipmentLogs($sixMonthsAgo, $currentDate, $user);
        $medicineLogs = $this->getFilteredMedicineLogs($sixMonthsAgo, $currentDate, $user);
        $medicalSupplyLogs = $this->getFilteredMedicalSupplyLogs($sixMonthsAgo, $currentDate, $user);

        $equipmentUsage = $this->calculateUsage($equipmentLogs);
        $medicineUsage = $this->calculateUsage($medicineLogs);
        $medicalSupplyUsage = $this->calculateUsage($medicalSupplyLogs);


        $totalInventory = Medicines::where('barangay_id', $authBarangayId)->sum('quantity')
            + Medical_supplies::where('barangay_id', $authBarangayId)->sum('quantity')
            + Equipments::where('barangay_id', $authBarangayId)->sum('quantity')
            + Midwife_inventories::where('barangay_id', $authBarangayId)->sum('quantity');

        $lowStock = Medicines::where('barangay_id', $authBarangayId)->where('status', 'like', "Low on stock%")->count()
            + Medical_supplies::where('barangay_id', $authBarangayId)->where('status', 'like', "Low on stock%")->count()
            + Midwife_inventories::where('barangay_id', $authBarangayId)
            ->where('status', 'like', "Low on stock%")
            ->where('type', '!=', 'equipment')
            ->count();

        $expiresoon = Medicines::where('barangay_id', $authBarangayId)->where('status', 'LIKE', '%Expiring')->count()
            + Medical_supplies::where('barangay_id', $authBarangayId)->where('status', 'LIKE', '%Expiring')->count()
            + Midwife_inventories::where('barangay_id', $authBarangayId)->where('status', 'LIKE', '%Expiring')->count();

        $totalCategory = Medical_categories::count();

        $Stock = Medicines::where('barangay_id', $authBarangayId)->where('status', 'LIKE', 'On Stock%')->count();
        $low = Medicines::where('barangay_id', $authBarangayId)->where('status', 'LIKE', 'Low on Stock%')->count();
        $expiring = Medicines::where('barangay_id', $authBarangayId)->where('status', 'LIKE', '%Expiring%')->count();
        $expired = Medicines::where('barangay_id', $authBarangayId)->where('status', 'LIKE', 'Expired')->count();

        if ($user->role_id === 3) {
            // Super admin can see all barangays excluding those with '%RHU%'
            $totalBarangay = Barangays::where('barangay_name', 'not like', '%RHU%')->count();
        } elseif ($user->role_id === 2) {
            // RHU admin sees barangays associated with their rhu_id and excludes those with '%RHU%'
            $totalBarangay = Barangays::where('rhu_id', $user->rhu_id)
                ->where('barangay_name', 'not like', '%RHU%')
                ->count();
        } else {
            // Role 1 (assuming it's for regular users), do not show any barangays
            $totalBarangay = 0;
        }


        $requestCount = 0;

        if ($user->role_id == 3) {
            // For super admin, include all barangays
            $requestCount = Requests::where('status', 'LIKE', 'Pending%')->count();
        } elseif ($user->role_id == 2) {
            // For RHU admin, include requests based on rhu_id
            $requestCount = Requests::where('rhu_id', $user->rhu_id)
                ->where('status', 'LIKE', 'Pending%')
                ->count();
        }



        $activities = Activity::with('causer')
            ->when($user->role_id === 3, function ($query) {
                // Super admin: show all activities
                return $query;
            })
            ->when($user->role_id === 2, function ($query) use ($user) {

                return $query->whereHas('causer', function ($query) use ($user) {
                    $query->where('rhu_id', $user->rhu_id)
                        ->where('role_id', '<>', 3);
                });
            })
            ->when($user->role_id === 1, function ($query) use ($user) {
                // Regular user: show activities for own barangay
                return $query->whereHas('causer', function ($query) use ($user) {
                    $query->where('barangay_id', $user->barangay_id);
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($activity) {
                $properties = json_decode($activity->properties, true);

                return [
                    'name' => $activity->causer ? $activity->causer->name : 'Unknown User',
                    'description' => $activity->description,
                    'updated_at' => $activity->updated_at->format('M j, Y g:i a'),
                    'type' => $properties['type'] ?? 'N/A',
                ];
            });


        if ($user->role_id == 3) {
            // For super admin, sum quantities from all barangays
            $barangayInventory = (int) Midwife_inventories::sum('quantity');
        } elseif ($user->role_id == 2) {
            // For RHU admin, sum quantities based on rhu_id
            $barangayInventory = (int) Midwife_inventories::whereHas('barangay', function ($query) use ($user) {
                $query->where('rhu_id', $user->rhu_id);
            })->sum('quantity');
        } else {
            // For regular users, sum quantities based on their own barangay_id
            $barangayInventory = (int) Midwife_inventories::where('barangay_id', $user->barangay_id)->sum('quantity');
        }

        $recentlyAddedItems = $this->getRecentlyAddedItems();

        $itemsRestock = $this->getRestockData('7_days', $user);

        $equipStatus = [
            'goodCondition' => Equipments::where('status', 'Good condition')
                ->where('barangay_id', $authBarangayId)
                ->count(),
            'poorCondition' => Equipments::where('status', 'Poor condition')
                ->where('barangay_id', $authBarangayId)
                ->count(),
            'condemned' => Equipments::where('status', 'Condemned')
                ->where('barangay_id', $authBarangayId)
                ->withTrashed()
                ->count(),
        ];

        $supplyStatus = [
            'stock' => Medical_supplies::where('status', 'On stock')
                ->where('barangay_id', $authBarangayId)
                ->count(),
            'low' => Medical_supplies::where('status', 'Low on stock')
                ->where('barangay_id', $authBarangayId)
                ->count(),
            'out' => Medical_supplies::where('status', 'Out of stock')
                ->where('barangay_id', $authBarangayId)
                ->count(),
        ];


        $dispenseCount = Activity::whereJsonContains('properties->type', 'Dispense')
            ->whereHas('causer', function ($query) use ($authBarangayId) {
                $query->where('barangay_id', $authBarangayId);
            })
            ->count();

        $notifications = Auth::user()->unreadNotifications;


        return inertia('Dashboard', [
            'equipStatus' => $equipStatus,
            'supplyStatus' => $supplyStatus,
            'totalQuantity' => $totalInventory,
            'lowStock' => $lowStock,
            'stock' => $Stock,
            'low' => $low,
            'expiring' => $expired,
            'expired' => $expiring,
            'expiresoon' => $expiresoon,
            'totalCategory' => $totalCategory,
            'requestCount' => $requestCount,
            'barangayInventory' => $barangayInventory,
            'totalBarangay' => $totalBarangay,
            'activities' => $activities,
            'equipmentUsage' => $equipmentUsage,
            'medicineUsage' => $medicineUsage,
            'medicalSupplyUsage' => $medicalSupplyUsage,
            'recentlyAddedItems' => $recentlyAddedItems,
            'itemsRestock' => $itemsRestock,
            'dispenseCount' => $dispenseCount,
            'notifications' => $notifications,
        ]);
    }

    private function getFilteredEquipmentLogs($startDate, $endDate, $user)
    {
        $logs = Activity::where(function ($query) {
            $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                ->orWhere('subject_type', 'App\\Models\\Requests');
        })
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($user->role->name === 'Super Admin') {
            // No additional filtering needed
        } elseif ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $logs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $logs->where(function ($query) use ($user) {
                $query->where('properties->old->user_id', $user->id)
                      ->orWhere('properties->new->user_id', $user->id)
                      ->orWhere('causer_id', $user->id);
            });
        }

        $logs->where(function ($query) {
            $query->where('properties->old->type', 'equipment')
                ->orWhere('properties->new->type', 'equipment');
        });

        return $logs->get();
    }
    private function getFilteredMedicineLogs($startDate, $endDate, $user)
    {
        $logs = Activity::where(function ($query) {
            $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                  ->orWhere('subject_type', 'App\\Models\\Requests');
        })->whereBetween('created_at', [$startDate, $endDate]);

        // Role-based filtering
        if ($user->role->name === 'Super Admin') {
        } elseif ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $logs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $logs->where(function ($query) use ($user) {
                $query->where('properties->old->user_id', $user->id)
                      ->orWhere('properties->new->user_id', $user->id)
                      ->orWhere('causer_id', $user->id);
            });
        }

        $logs->where(function ($query) {
            $query->where('properties->old->type', 'medicine')
                  ->orWhere('properties->new->type', 'medicine');
        });

        return $logs->get();
    }
    private function getFilteredMedicalSupplyLogs($startDate, $endDate, $user)
    {
        $logs = Activity::where(function ($query) {
            $query->where('subject_type', 'App\\Models\\Midwife_inventories')
                ->orWhere('subject_type', 'App\\Models\\Requests');
        })
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($user->role->name === 'Super Admin') {
            // No additional filtering needed
        } elseif ($user->role->name === 'Admin') {
            $barangays = Barangays::where('rhu_id', $user->rhu_id)->pluck('id');
            $logs->whereIn('properties->old->barangay_id', $barangays);
        } elseif ($user->role->name === 'Midwife') {
            $logs->where(function ($query) use ($user) {
                $query->where('properties->old->user_id', $user->id)
                      ->orWhere('properties->new->user_id', $user->id)
                      ->orWhere('causer_id', $user->id);
            });
        }

        $logs->where(function ($query) {
            $query->where('properties->old->type', 'medical_supply')
                ->orWhere('properties->new->type', 'medical_supply');
        });

        return $logs->get();
    }
    private function calculateUsage($logs)
    {
        $usage = [];
        foreach (range(0, 5) as $i) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $monthlyLogs = $logs->filter(function ($log) use ($monthStart, $monthEnd) {
                return $log->updated_at->between($monthStart, $monthEnd);
            });

            $monthlyUsage = $monthlyLogs->reduce(function ($carry, $log) {
                $properties = json_decode($log->properties, true);
                if (isset($properties['old']['quantity']) && isset($properties['new']['quantity'])) {
                    $oldQuantity = $properties['old']['quantity'];
                    $newQuantity = $properties['new']['quantity'];
                    $quantityDifference = $oldQuantity - $newQuantity;

                    return $carry + ($quantityDifference > 0 ? $quantityDifference : 0);
                }
                return $carry;
            }, 0);

            $usage[$monthStart->format('F Y')] = $monthlyUsage;
        }

        return $usage;
    }
    private function getRecentlyAddedItems()
    {
        $barangayId = Auth::user()->barangay_id;

        // Fetch Medicine logs including soft-deleted records and filter by barangay_id
        $medicineLogs = Activity::where('subject_type', 'App\\Models\\Medicines')
            ->where('description', 'like', '%added a new medicine%')
            ->whereHas('causer', function ($query) use ($barangayId) {
                $query->where('barangay_id', $barangayId);  // Limit by barangay_id of the authenticated user
            })
            ->with(['subject' => function($query) {
                $query->withTrashed();  // Include soft-deleted items in related subjects
            }])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch Equipment logs including soft-deleted records and filter by barangay_id
        $equipmentLogs = Activity::where('subject_type', 'App\\Models\\Equipments')
            ->where('description', 'like', '%added a new equipment%')
            ->whereHas('causer', function ($query) use ($barangayId) {
                $query->where('barangay_id', $barangayId);  // Limit by barangay_id of the authenticated user
            })
            ->with(['subject' => function($query) {
                $query->withTrashed();  // Include soft-deleted items in related subjects
            }])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch Medical Supply logs including soft-deleted records and filter by barangay_id
        $medicalSupplyLogs = Activity::where('subject_type', 'App\\Models\\Medical_supplies')
            ->where('description', 'like', '%added a new medical supply%')
            ->whereHas('causer', function ($query) use ($barangayId) {
                $query->where('barangay_id', $barangayId);  // Limit by barangay_id of the authenticated user
            })
            ->with(['subject' => function($query) {
                $query->withTrashed();  // Include soft-deleted items in related subjects
            }])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Merge all logs into one collection
        $combinedLogs = $medicineLogs->merge($equipmentLogs)->merge($medicalSupplyLogs);

        // Map the logs to a simpler array
        $mappedLogs = $combinedLogs->map(function ($activity) {
            $subject = $activity->subject;
            $type = '';
            $name = '';

            // Determine the type and name based on the subject type
            if ($activity->subject_type === 'App\\Models\\Medicines') {
                $type = 'Medicine';
                $name = $subject->generic_name->generic_name ?? 'Unknown';
            } elseif ($activity->subject_type === 'App\\Models\\Equipments') {
                $type = 'Equipment';
                $name = $subject->equipment_name ?? 'Unknown';
            } elseif ($activity->subject_type === 'App\\Models\\Medical_supplies') {
                $type = 'Medical Supply';
                $name = $subject->med_sup_name ?? 'Unknown';
            }

            return [
                'type' => $type,
                'name' => $name,
                'date_added' => $activity->created_at->format('M j, Y g:i a'),
            ];
        });

        // Sort by created_at and return the first 5 items
        return $mappedLogs->sortByDesc('date_added')->values()->take(5);
    }
    private function getRestockData($duration, $user)
    {

        $medicines = Medicines::with('generic_name')->get()->pluck('generic_name.generic_name')->unique()->values()->all();
        $equipments = Equipments::distinct()->pluck('equipment_name')->all();
        $medicalSupplies = Medical_supplies::distinct()->pluck('med_sup_name')->all();

        $medicineRestock = $this->processPredictions($medicines, 'medicines', $duration, $user);
        $equipmentRestock = $this->processPredictions($equipments, 'equipments', $duration, $user);
        $medicalSupplyRestock = $this->processPredictions($medicalSupplies, 'medical_supplies', $duration, $user);

        return array_merge($medicineRestock, $equipmentRestock, $medicalSupplyRestock);
    }
    private function processPredictions($items, $type, $duration, $user)
    {
        $restockData = [];
        foreach ($items as $item) {
            $itemData = $this->getConsumptionHistory($item, $type, $duration, $user);

            if (count($itemData) < 2) continue;

            $predictedUsage = $this->calculateLinearRegression($itemData, $duration);

            $currentStock = $this->getCurrentStock($item, $type);

            if ($currentStock && $predictedUsage > $currentStock) {
                $requiredRestock = $predictedUsage - $currentStock;
                $restockData[] = [
                    'name' => $item,
                    'current_stock' => $currentStock,
                    'predicted_usage' => $predictedUsage,
                    'required_restock' => $requiredRestock,
                    'type' => $type
                ];
            }
        }

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
    private function getConsumptionHistory($item, $type, $duration, $user)
    {
        $startDate = $this->getStartDateFromDuration($duration);

        if ($type === 'medicines') {
            return $this->getMedicineConsumptionHistory($item, $startDate, $user);
        } elseif ($type === 'equipments') {
            return $this->getEquipmentConsumptionHistory($item, $startDate, $user);
        } elseif ($type === 'medical_supplies') {
            return $this->getMedicalSupplyConsumptionHistory($item, $startDate, $user);
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
                return now()->subDays(7); // Default to 7 days
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
            $quantityConsumed = ($properties['old']['quantity'] ?? 0) - ($properties['new']['quantity'] ?? 0);
            return ['ds' => $log->created_at->format('Y-m-d'), 'y' => $quantityConsumed];
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
            $quantityConsumed = ($properties['old']['quantity'] ?? 0) - ($properties['new']['quantity'] ?? 0);
            return ['ds' => $log->created_at->format('Y-m-d'), 'y' => $quantityConsumed];
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
            $quantityConsumed = ($properties['old']['quantity'] ?? 0) - ($properties['new']['quantity'] ?? 0);
            return ['ds' => $log->created_at->format('Y-m-d'), 'y' => $quantityConsumed];
        })->filter()->toArray();
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
}
