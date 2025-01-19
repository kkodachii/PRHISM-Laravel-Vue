<?php

namespace App\Http\Controllers;

use App\Models\Equipments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EquipSummaryController extends Controller
{
    public function index(Request $request)
    {
        $authBarangayId = Auth::user()->barangay_id;
    
        $search = $request->input('search');
        $status = $request->input('status'); // Retrieve the status from the request
    
        // Retrieve low stock items from Equipments with pagination, including soft-deleted items
        $equipmentStatus = Equipments::where('barangay_id', $authBarangayId)
    ->when($status === 'Condemned', function ($query) {
        $query->withTrashed(); // Include soft-deleted items only if status is "Condemned"
    })
    ->when($status, function ($query) use ($status) {
        $query->where('status', 'like', "{$status}%");
    })
    ->when($request->search, function ($query) use ($request) {
        $query->where('equipment_name', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
    })
    ->paginate(5)
    ->through(function ($equipment) {
        return [
            'id' => $equipment->id,
            'name' => $equipment->equipment_name,
            'quantity' => $equipment->quantity,
            'description' => $equipment->description,
            'status' => $equipment->status,
            'type' => "Equipment",
            'trashed' => $equipment->trashed(), // Add trashed status
        ];
    });

    
        return inertia('Inventory/EquipSummary', [
            'equipmentStatus' => $equipmentStatus,
        ]);
    }
    
}

