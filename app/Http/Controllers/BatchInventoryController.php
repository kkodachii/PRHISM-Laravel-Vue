<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Medicines;
use App\Models\Equipments;
use App\Models\Medical_supplies;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class BatchInventoryController extends Controller
{
    public function index(Request $request)
    {
        $batchId = $request->input('batchId', 1); 
        $search = $request->input('search'); 
        $type = $request->input('type', 'all'); 
        $status = $request->input('status'); 
        

       
        $sortField = $request->input('sort', 'type'); 
        $sortDirection = $request->input('direction', 'asc'); 

        // Initialize an empty query collection
        $inventoryQuery = collect([]);

        // Convert status parameter to array if it's not empty
        $statusArray = $status ? explode(',', $status) : [];

        // Fetch and map medicines
        if ($type === 'all' || $type === 'medicine') {
            $medicinesQuery = Medicines::where('batch_id', $batchId)
                ->with('user')
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('generic_name', function ($query) use ($search) {
                        $query->where('generic_name', 'like', "%{$search}%");
                    });
                })
                ->where('barangay_id', Auth::user()->barangay_id)
                ->when(!empty($statusArray), function ($query) use ($statusArray) {
                    $query->where(function ($query) use ($statusArray) {
                        $query->where(function ($query) use ($statusArray) {
                            if (in_array('onStock', $statusArray)) {
                        $query->orWhere('status', 'On stock');
                    }
                    if (in_array('lowOnStock', $statusArray)) {
                        $query->orWhere('status','LIKE', '%Low on stock%');
                    }
                    if (in_array('outOfStock', $statusArray)) {
                        $query->orWhere('status','LIKE', '%Out of stock%');
                    }
                            if (in_array('new', $statusArray)) {
                                $query->orWhere('status', 'New');
                            }
                            if (in_array('goodCondition', $statusArray)) {
                                $query->orWhere('status', 'Good condition');
                            }
                            if (in_array('fairCondition', $statusArray)) {
                                $query->orWhere('status', 'Fair condition');
                            }
                            if (in_array('poorCondition', $statusArray)) {
                                $query->orWhere('status', 'Poor condition');
                            }
                            if (in_array('condemned', $statusArray)) {
                                $query->orWhere('status', 'Condemned');
                            }
                        });
                    
        
                        if (in_array('expiring', $statusArray)) {
                            $query->orWhere('status', 'LIKE', '%Expiring%');
                        }
                        if (in_array('expired', $statusArray)) {
                            $query->orWhere('status', 'Expired');
                        }
                    });
                })
                ->when($sortField == 'generic_name', function ($query) use ($sortDirection) {
                    $query->orderByRaw("(SELECT generic_names.generic_name FROM generic_names WHERE generic_names.id = medicines.generic_name_id) $sortDirection");
                })
                ->when($sortField == 'status', function ($query) use ($sortDirection) {
                    $query->orderByRaw("FIELD(status, 'On stock', 'On stock, Expiring', 'Low on stock', 'Low on stock, Expiring', 'Out of stock', 'Out of stock, Expiring', 'Expired') $sortDirection");
                })
                ->when($sortField == 'quantity', function ($query) use ($sortDirection) {
                    $query->orderBy('quantity', $sortDirection);
                })
                ->when($sortField == 'date_acquired', function ($query) use ($sortDirection) {
                    $query->orderBy('date_acquired', $sortDirection);
                })
                ->when($sortField == 'expiration_date', function ($query) use ($sortDirection) {
                    $query->orderBy('expiration_date', $sortDirection);
                })
                ->when($sortField == 'batch_id', function ($query) use ($sortDirection) {
                    $query->orderBy('batch_id', $sortDirection);
                });

            $medicines = $medicinesQuery->get()->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->generic_name->generic_name,
                    'batch_id' => $medicine->batch_id,
                    'type' => 'Medicine',
                    'quantity' => $medicine->quantity,
                    'date_acquired' => $medicine->date_acquired,
                    'status' => $medicine->status,
                    'provider' => $medicine->provider,
                    'user' => $medicine->user ? $medicine->user->name : null,
                ];
            });
            $inventoryQuery = $inventoryQuery->merge($medicines);
        }

        // Fetch and map equipments
        if ($type === 'all' || $type === 'equipment') {
            $equipmentsQuery = Equipments::where('batch_id', $batchId)
                ->with('user')
                ->when($search, function ($query) use ($search) {
                    $query->where('equipment_name', 'like', "%{$search}%");
                })
                ->when(!empty($statusArray), function ($query) use ($statusArray) {
                    $query->where(function ($query) use ($statusArray) {
                        $query->where(function ($query) use ($statusArray) {
                            if (in_array('onStock', $statusArray)) {
                        $query->orWhere('status', 'On stock');
                    }
                    if (in_array('lowOnStock', $statusArray)) {
                        $query->orWhere('status','LIKE', '%Low on stock%');
                    }
                    if (in_array('outOfStock', $statusArray)) {
                        $query->orWhere('status','LIKE', '%Out of stock%');
                    }
                            if (in_array('new', $statusArray)) {
                                $query->orWhere('status', 'New');
                            }
                            if (in_array('goodCondition', $statusArray)) {
                                $query->orWhere('status', 'Good condition');
                            }
                            if (in_array('fairCondition', $statusArray)) {
                                $query->orWhere('status', 'Fair condition');
                            }
                            if (in_array('poorCondition', $statusArray)) {
                                $query->orWhere('status', 'Poor condition');
                            }
                            if (in_array('condemned', $statusArray)) {
                                $query->orWhere('status', 'Condemned');
                            }
                        });
                    
        
                        if (in_array('expiring', $statusArray)) {
                            $query->orWhere('status', 'LIKE', '%Expiring%');
                        }
                        if (in_array('expired', $statusArray)) {
                            $query->orWhere('status', 'Expired');
                        }
                    });
                })
                ->when($sortField == 'equipment_name', function ($query) use ($sortDirection) {
                    $query->orderBy('equipment_name', $sortDirection);
                })
                ->when($sortField == 'status', function ($query) use ($sortDirection) {
                    $query->orderByRaw("FIELD(status, 'New', 'Good condition', 'Fair condition', 'Poor condition') $sortDirection");
                })
                ->when($sortField == 'quantity', function ($query) use ($sortDirection) {
                    $query->orderBy('quantity', $sortDirection);
                })
                ->when($sortField == 'date_acquired', function ($query) use ($sortDirection) {
                    $query->orderBy('date_acquired', $sortDirection);
                })
                ->when($sortField == 'batch_id', function ($query) use ($sortDirection) {
                    $query->orderBy('batch_id', $sortDirection);
                });

            $equipments = $equipmentsQuery->get()->map(function ($equipment) {
                return [
                    'id' => $equipment->id,
                    'name' => $equipment->equipment_name,
                    'type' => 'Equipment',
                    'quantity' => $equipment->quantity,
                    'batch_id' => $equipment->batch_id,
                    'date_acquired' => $equipment->date_acquired,
                    'status' => $equipment->status,
                    'provider' => $equipment->provider,
                    'user' => $equipment->user ? $equipment->user->name : null,
                ];
            });
            $inventoryQuery = $inventoryQuery->merge($equipments);
        }

        // Fetch and map medical supplies
        if ($type === 'all' || $type === 'medicalSupply') {
            $medicalSuppliesQuery = Medical_Supplies::where('batch_id', $batchId)
                ->with('user')
                ->when($search, function ($query) use ($search) {
                    $query->where('med_sup_name', 'like', "%{$search}%");
                })
                ->when(!empty($statusArray), function ($query) use ($statusArray) {
                    $query->where(function ($query) use ($statusArray) {
                        $query->where(function ($query) use ($statusArray) {
                            if (in_array('onStock', $statusArray)) {
                        $query->orWhere('status', 'On stock');
                    }
                    if (in_array('lowOnStock', $statusArray)) {
                        $query->orWhere('status','LIKE', '%Low on stock%');
                    }
                    if (in_array('outOfStock', $statusArray)) {
                        $query->orWhere('status','LIKE', '%Out of stock%');
                    }
                            if (in_array('new', $statusArray)) {
                                $query->orWhere('status', 'New');
                            }
                            if (in_array('goodCondition', $statusArray)) {
                                $query->orWhere('status', 'Good condition');
                            }
                            if (in_array('fairCondition', $statusArray)) {
                                $query->orWhere('status', 'Fair condition');
                            }
                            if (in_array('poorCondition', $statusArray)) {
                                $query->orWhere('status', 'Poor condition');
                            }
                            if (in_array('condemned', $statusArray)) {
                                $query->orWhere('status', 'Condemned');
                            }
                        });
                    
        
                        if (in_array('expiring', $statusArray)) {
                            $query->orWhere('status', 'LIKE', '%Expiring%');
                        }
                        if (in_array('expired', $statusArray)) {
                            $query->orWhere('status', 'Expired');
                        }
                    });
                })
                ->when($sortField == 'med_sup_name', function ($query) use ($sortDirection) {
                    $query->orderBy('med_sup_name', $sortDirection);
                })
                ->when($sortField == 'status', function ($query) use ($sortDirection) {
                    $query->orderByRaw("FIELD(status, 'On stock', 'Low on stock', 'Out of stock', 'Expired') $sortDirection");
                })
                ->when($sortField == 'quantity', function ($query) use ($sortDirection) {
                    $query->orderBy('quantity', $sortDirection);
                })
                ->when($sortField == 'date_acquired', function ($query) use ($sortDirection) {
                    $query->orderBy('date_acquired', $sortDirection);
                })
                ->when($sortField == 'batch_id', function ($query) use ($sortDirection) {
                    $query->orderBy('batch_id', $sortDirection);
                });

            $medicalSupplies = $medicalSuppliesQuery->get()->map(function ($supply) {
                return [
                    'id' => $supply->id,
                    'batch_id' => $supply->batch_id,
                    'name' => $supply->med_sup_name,
                    'type' => 'Medical Supply',
                    'quantity' => $supply->quantity,
                    'date_acquired' => $supply->date_acquired,
                    'status' => $supply->status,
                    'provider' => $supply->provider,
                    'user' => $supply->user ? $supply->user->name : null,
                ];
            });
            $inventoryQuery = $inventoryQuery->merge($medicalSupplies);
        }

        // Apply sorting to the merged collection
        if ($sortField === 'type') {
            $inventory = $inventoryQuery->sortBy('type', SORT_REGULAR, $sortDirection === 'desc');
        } else {
            $inventory = $inventoryQuery->sortBy(function ($item) use ($sortField) {
                return $item[$sortField];
            }, SORT_REGULAR, $sortDirection === 'desc');
        }

        // Get current page from request
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a paginator instance
        $perPage = 5;
        $currentPageItems = $inventory->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedInventory = new LengthAwarePaginator(
            $currentPageItems,
            $inventory->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return Inertia::render('Inventory/BatchInventory', [
            'inventory' => $paginatedInventory,
            'batchId' => $batchId,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'search' => $search,
            'type' => $type,
            'status' => $status 
        ]);
    }
}