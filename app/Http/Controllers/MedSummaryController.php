<?php

namespace App\Http\Controllers;

use App\Models\Medicines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MedSummaryController extends Controller
{
    public function index(Request $request)
    {

        $authBarangayId = Auth::user()->barangay_id;

        $search = $request->input('search');
        $status = $request->input('status'); // Retrieve the status from the request

        // Retrieve low stock items from Medicines with pagination
        $MedStatus = Medicines::where('barangay_id', $authBarangayId)
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status === 'Expiring' ? 'LIKE' : 'LIKE', $status === 'Expiring' ? "%{$status}%" : "{$status}%");
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('generic_name', function ($query) use ($search) {
                    $query->where('generic_name', 'like', "%{$search}%");
                });
            })
            ->with('generic_name:id,generic_name')
            ->paginate(5)
            ->through(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->generic_name->generic_name,
                    'brand' => $medicine->brand,
                    'quantity' => $medicine->quantity,
                    'status' => $medicine->status,
                    'type' => "Medicine"
                ];
            });

        return inertia('Inventory/MedSummary', [
            'Medstatus' => $MedStatus,
        ]);
    }
}

