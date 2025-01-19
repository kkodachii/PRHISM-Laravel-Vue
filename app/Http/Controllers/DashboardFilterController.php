<?php

namespace App\Http\Controllers;

use App\Models\Medicines;
use App\Models\Medical_supplies;
use App\Models\Midwife_inventories;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardFilterController extends Controller
{
    public function index(Request $request)
    {

        $authBarangayId = Auth::user()->barangay_id;

        $search = $request->input('search');
        $status = $request->input('status');

        // Retrieve low stock items from Medicines
        $lowStockMedicines = collect(Medicines::where('barangay_id', $authBarangayId)
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status === 'Expiring' ? 'LIKE' : 'LIKE', $status === 'Expiring' ? "%{$status}%" : "{$status}%");
        })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('generic_name', function ($query) use ($search) {
                    $query->where('generic_name', 'like', "%{$search}%");
                });
            })
            ->with('generic_name:id,generic_name') // Make sure to adjust the actual column name
            ->get()
            ->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->generic_name->generic_name, // Adjust if needed
                    'brand' => $medicine->brand,
                    'quantity' => $medicine->quantity,
                    'status' => $medicine->status,
                    'type' => "Medicine"
                ];
            }));
      
        // Retrieve low stock items from Medical_supplies
        $lowStockSupplies = collect(Medical_supplies::where('barangay_id', $authBarangayId)
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status === 'Expiring' ? 'LIKE' : 'LIKE', $status === 'Expiring' ? "%{$status}%" : "{$status}%");
        })
            ->when($search, function ($query) use ($search) {
                return $query->where('med_sup_name', 'like', '%' . $search . '%');
            })
            ->get()
            ->map(function ($supply) {
                return [
                    'id' => $supply->id,
                    'name' => $supply->med_sup_name,
                    'description' => $supply->description,
                    'quantity' => $supply->quantity,
                    'status' => $supply->status,
                    'type' => "Medical Supply"
                ];
            }));

        // Retrieve low stock items from Midwife_inventories
        $lowStockMidwifeInventories = collect(Midwife_inventories::where('barangay_id', $authBarangayId)
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status === 'Expiring' ? 'LIKE' : 'LIKE', $status === 'Expiring' ? "%{$status}%" : "{$status}%");
        })
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->get()
        ->map(function ($inventory) {
            return [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'brand' => $inventory->brand,
                'description' => $inventory->description,
                'quantity' => $inventory->quantity,
                'status' => $inventory->status,
                'type' => match ($inventory->type) {
                    'medicine' => 'Medicine',
                    'equipment' => 'Equipment',
                    'medical_supply' => 'Medical Supply',
                    default => 'Unknown'
                },
            ];
        }));

        // Merge all low stock items into one collection
        $lowStockItems = $lowStockMedicines->merge($lowStockSupplies)->merge($lowStockMidwifeInventories);

        // Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;

        // Slice the collection for the current page
        $currentPageItems = $lowStockItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Create a paginator instance
        $paginatedItems = new LengthAwarePaginator(
            $currentPageItems,
            $lowStockItems->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return inertia('Inventory/DashboardFilter', [
            'lowStockItems' => $paginatedItems,
        ]);
    }
}
