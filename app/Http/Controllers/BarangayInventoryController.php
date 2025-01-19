<?php

namespace App\Http\Controllers;


use App\Models\Midwife_inventories;
use Illuminate\Http\Request;
use App\Models\Barangays;

use Illuminate\Support\Facades\Auth;

class BarangayInventoryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $type = $request->input('type');
        $sortField = $request->input('sort', 'name'); 
        $sortDirection = $request->input('direction', 'asc');
        $barangayName = $request->input('barangay'); // Retrieve barangay name from query
    
        $statusArray = $status ? explode(',', $status) : [];
    
        // Get the barangay_id based on the provided barangay_name
        $barangay = Barangays::where('barangay_name', $barangayName)->first();
        $barangayId = $barangay ? $barangay->id : null;
    
        // Base query for midwife inventories
        $mw_inventory = Midwife_inventories::with(['category', 'user'])
            ->when($barangayId, function ($query) use ($barangayId) {
                $query->where('barangay_id', $barangayId);
            })
            ->where('status', '!=', 'Out of Stock')
            ->when($request->search, function ($query) use ($request, $barangayId) {
                // Ensure the search respects the barangay filter
                $query->where(function ($q) use ($request, $barangayId) {
                    if ($barangayId) {
                        $q->where('barangay_id', $barangayId);
                    }
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('brand', 'like', '%' . $request->search . '%');
                });
            })
            // Filter by type if selected
            ->when($type && $type !== 'all', function ($query) use ($type) {
                $query->where('type', '=', $type);
            })
            // Filter by status
            ->when(!empty($statusArray), function ($query) use ($statusArray) {
                $query->where(function ($query) use ($statusArray) {
                    if (in_array('onStock', $statusArray)) {
                        $query->orWhere('status', 'On stock');
                    }
                    if (in_array('lowOnStock', $statusArray)) {
                        $query->orWhere('status', 'LIKE', '%Low on stock%');
                    }
                    if (in_array('outOfStock', $statusArray)) {
                        $query->orWhere('status', 'LIKE', '%Out of stock%');
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
                    // Keep expiring and expired filters
                    if (in_array('expiring', $statusArray)) {
                        $query->orWhere('status', 'LIKE', '%Expiring%');
                    }
                    if (in_array('expired', $statusArray)) {
                        $query->orWhere('status', 'Expired');
                    }
                });
            })
            // Apply sorting
            ->when($sortField === 'status', function ($query) use ($sortDirection) {
                $query->orderByRaw("FIELD(status, 'On stock', 'On stock, Expiring', 'Low on stock', 'Low on stock, Expiring', 'Out of stock', 'Out of stock, Expiring', 'Expired', 'New', 'Good condition', 'Fair condition', 'Poor condition') $sortDirection");
            })
            ->when($sortField === 'quantity', function ($query) use ($sortDirection) {
                $query->orderBy('quantity', $sortDirection);
            })
            ->when($sortField === 'date_acquired', function ($query) use ($sortDirection) {
                $query->orderBy('created_at', $sortDirection);
            })
            ->orderBy('name', 'asc')
            ->paginate(5);
    
        return inertia('MwInventory/BarangayInventory', [
            'mw_inventory' => $mw_inventory,
        ]);
    }
    
    
    
    
}
