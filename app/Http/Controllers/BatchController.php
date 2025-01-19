<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Batches;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $search = $request->input('search');
    $sortField = $request->input('sort', 'id'); // Default sort field
    $sortDirection = $request->input('direction', 'asc'); // Default sort direction

    $batches = Batches::with(['medicines', 'equipment', 'medicalSupplies'])

        ->when($search, function ($query, $search) {
            return $query->where('batch_number', 'like', '%' . $search . '%');
        })
        ->where('barangay_id', Auth::user()->barangay_id)
        ->orderBy($sortField, $sortDirection) // Apply sorting
        ->paginate(5)
        ->through(function ($batch) {
            $medicines = $batch->medicines->where('barangay_id', Auth::user()->barangay_id);
            $equipment = $batch->equipment->where('barangay_id', Auth::user()->barangay_id);
            $medicalSupplies = $batch->medicalSupplies->where('barangay_id', Auth::user()->barangay_id);
            
        
            $medtotal = $medicines->sum('quantity');
            $eqtotal = $equipment->sum('quantity');
            $suptotal = $medicalSupplies->sum('quantity');
            $totalEquipment = $equipment->count();
            $totalMedicines = $medicines->count();
            $totalMedicalSupplies = $medicalSupplies->count();
            $total = $totalMedicines + $totalEquipment + $totalMedicalSupplies;


            return [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'medicine_quantity' => $medtotal,
                'equipment_quantity' => $eqtotal,
                'medical_supplies_quantity' => $suptotal,
                'quantity' => $total,
                'created_at' => $batch->created_at,
            ];
        });

    return Inertia::render('Inventory/Batches', [
        'batches' => $batches,
    ]);
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
