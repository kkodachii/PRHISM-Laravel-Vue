<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Barangays;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class BarangayListController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');
        $perPage = 5; // Number of items per page

        // Get the authenticated user
        $user = Auth::user();

        // Fetch barangays and associated midwife inventories
        $barangays = Barangays::with(['midwifeInventories'])
            ->when($user->role_id == 2, function ($query) use ($user) {
                // If the user is an RHU admin, filter by the user's RHU ID
                return $query->where('rhu_id', $user->rhu_id);
            })
            ->when($search, function ($query, $search) {
                return $query->where('barangay_name', 'like', '%' . $search . '%');
            })
            ->where('barangay_name', 'not like', '%RHU%')
            ->orderBy($sortField === 'total_quantity' ? 'id' : 'barangay_name', $sortDirection) // Alphabetical sort for barangay_name
            ->get() // Fetch all for sorting and processing
            ->map(function ($barangay) {
                $midwifeInventory = $barangay->midwifeInventories;

                $medicineItems = $midwifeInventory->where('type', 'medicine');
                $equipmentItems = $midwifeInventory->where('type', 'equipment');
                $medicalSuppliesItems = $midwifeInventory->where('type', 'medical_supply');

                // Count total entries of each type
                $medicineCount = $medicineItems->count();
                $equipmentCount = $equipmentItems->count();
                $medicalSuppliesCount = $medicalSuppliesItems->count();

                $medicineTotal = $medicineItems->sum('quantity');
                $equipmentTotal = $equipmentItems->sum('quantity');
                $medicalSuppliesTotal = $medicalSuppliesItems->sum('quantity');

                // Total entries count across all types
                $totalItems = $medicineCount + $equipmentCount + $medicalSuppliesCount;

                return [
                    'id' => $barangay->id,
                    'barangay_name' => $barangay->barangay_name,
                    'medicine_quantity' => $medicineTotal,
                    'equipment_quantity' => $equipmentTotal,
                    'medical_supplies_quantity' => $medicalSuppliesTotal,
                    'total_quantity' => $totalItems,
                    'created_at' => $barangay->created_at,
                ];
            });

        // Sort based on total_quantity if needed
        if ($sortField === 'total_quantity') {
            $barangays = $barangays->sortBy('total_quantity', SORT_REGULAR, $sortDirection === 'desc');
        }

        // Manually paginate the collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paginatedBarangays = new LengthAwarePaginator(
            $barangays->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $barangays->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return Inertia::render('MwInventory/BarangayList', [
            'barangays' => $paginatedBarangays,
        ]);
    }




    // Other methods (create, store, show, edit, update, destroy) remain unchanged for now
}
