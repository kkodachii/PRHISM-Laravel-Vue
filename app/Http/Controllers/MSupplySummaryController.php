<?php

namespace App\Http\Controllers;

use App\Models\Medical_supplies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MSupplySummaryController extends Controller
{
    public function index(Request $request)
    {

        $authBarangayId = Auth::user()->barangay_id;

        $search = $request->input('search');
        $status = $request->input('status'); // Retrieve the status from the request

        // Retrieve low stock items from Medicines with pagination
        $supplyStatus = Medical_supplies::where('barangay_id', $authBarangayId)
            ->when($status, function ($query) use ($status) {
                $query->where('status', 'like', "{$status}%");
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('med_sup_name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->paginate(5)
            ->through(function ($supply) {
                return [
                    'id' => $supply->id,
                    'name' => $supply->med_sup_name,
                    'quantity' => $supply->quantity,
                    'description' => $supply->description,
                    'status' => $supply->status,
                    'type' => "Medical Supply"
                ];
            });

        return inertia('Inventory/SupplySummary', [
            'supplyStatus' => $supplyStatus,
        ]);
    }
}

