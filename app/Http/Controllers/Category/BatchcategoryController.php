<?php

namespace App\Http\Controllers\Category;

use App\Models\Batches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BatchcategoryController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $search = $request->input('search');
        $unusedBatches = DB::table('batches')
            ->leftJoin('medicines', 'batches.id', '=', 'medicines.batch_id')
            ->leftJoin('medical_supplies', 'batches.id', '=', 'medical_supplies.batch_id')
            ->leftJoin('equipments', 'batches.id', '=', 'equipments.batch_id')
            ->whereNull('medicines.batch_id')
            ->whereNull('medical_supplies.batch_id')
            ->whereNull('equipments.batch_id')
            ->select('batches.id', 'batches.batch_number')
            ->get();

        $batches = Batches::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('batch_number', 'like', "%{$search}%");
            })
            ->where('barangay_id', Auth::user()->barangay_id)
            ->paginate(5)
            ->appends(['search' => $search]);

        return Inertia::render('SuperAdmin/Category/Batch', [
            'batches' => $batches,
            'unusedBatches' => $unusedBatches,
        ]);
    }

    // Show the form for creating a new resource.
    public function create()
    {
        $unusedBatches = DB::table('batches')
            ->leftJoin('medicines', 'batches.id', '=', 'medicines.batch_id')
            ->leftJoin('medical_supplies', 'batches.id', '=', 'medical_supplies.batch_id')
            ->leftJoin('equipments', 'batches.id', '=', 'equipments.batch_id')
            ->whereNull('medicines.batch_id')
            ->whereNull('medical_supplies.batch_id')
            ->whereNull('equipments.batch_id')
            ->select('batches.id', 'batches.batch_number')
            ->get();

        return Inertia::render('SuperAdmin/Category/Batch/Create', [
            'unusedBatches' => $unusedBatches,
        ]);
    }


    public function store()
    {
        $now = now();

            $batchName = $now->format('m-d-Y_H-i-s');
            $barangayId = Auth::user()->barangay_id;

            // Create a new batch with the generated batch number
            $newBatch = new Batches([
                'batch_number' => $batchName,
                'barangay_id' => $barangayId
            ]);
            $newBatch->save();

            return redirect()->back()->with('toast', [
                'message' => 'Batch successfully added',
                'type' => 'success'
            ]);
    }





    // Display the specified resource.
    public function show(Batches $batch)
    {
        return Inertia::render('SuperAdmin/Category/Batch/Show', [
            'batch' => $batch,
        ]);
    }

    // Show the form for editing the specified resource.
    public function edit(Batches $batch)
    {
        return Inertia::render('SuperAdmin/Category/Batch/Edit', [
            'batch' => $batch,
        ]);
    }

    // Update the specified resource in storage.
    public function update(Request $request, Batches $batch)
    {
        try {
            $validated = $request->validate([
                'batch_number' => 'required|string|max:255|unique:batches,batch_number,' . $batch->id . ',id,barangay_id,' . Auth::user()->barangay_id,
            ]);

            // Update the batch if validation passes
            $updated = $batch->update($validated);

            if ($updated) {
                return redirect()->back()->with('toast', [
                    'message' => 'Batch successfully updated',
                    'type' => 'success'
                ]);
            } else {
                return redirect()->back()->with('toast', [
                    'message' => 'Failed to update batch',
                    'type' => 'error'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput()->with('toast', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('toast', [
                'message' => 'Failed to update batch: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }



    // Remove the specified resource from storage.
    public function destroy(Batches $batch)
    {
        $deleted = $batch->delete();

        if ($deleted) {
            return redirect()->back()->with('toast', [
                'message' => 'Batch deleted successfully',
                'type' => 'success'
            ]);
        } else {
            return redirect()->back()->with('toast', [
                'message' => 'Failed to delete batch',
                'type' => 'error'
            ]);
        }
    }
}



