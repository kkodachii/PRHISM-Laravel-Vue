<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Dispensations;
use App\Models\Dispense_inventory;
use Illuminate\Support\Facades\Auth;

class DispenselogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // Filter by barangay_id using Auth::user()->barangay_id
        $dispense_inventory = Dispense_inventory::with('dispensations')
            ->whereHas('dispensations', function($query) use ($search) {
                $query->where('barangay_id', Auth::user()->barangay_id)
                    ->where(function($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%") 
                              ->orWhere('type', 'like', "%{$search}%")
                              ->orWhere('recipients_name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        return Inertia::render('Midwife/DispenseLog', [
            'dispenseLogs' => $dispense_inventory,
        ]);
    }
}




