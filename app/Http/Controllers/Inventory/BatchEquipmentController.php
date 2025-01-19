<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use App\Models\Batches;
use App\Models\Equipments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BatchEquipmentController extends Controller
{
    public function index()
    {
        $batches = Batches::where('barangay_id', Auth::user()->barangay_id)
        ->orderBy('created_at', 'desc')
        ->get();
        $equipmentNames = Equipments::selectRaw('MIN(id) as id, equipment_name')
            ->groupBy('equipment_name')
            ->get();
        return inertia('Inventory/EquipmentBatch', [
            'batches' => $batches,
            'equipNames' => $equipmentNames,
        ]);
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'equip.*.user_id' => 'nullable|exists:users,id',
            'equip.*.barangay_id' => 'nullable|exists:barangays,id',
            'equip.*.equipment_name' => 'required|string|max:255',
            'equip.*.description' => 'nullable|string',
            'equip.*.batch_id' => 'required',
            'equip.*.quantity' => 'required|integer',
            'equip.*.provider' => 'required|string',
            'equip.*.status' => 'required|string',
            'equip.*.date_acquired' => 'nullable|date',
            'equip.*.accountable' => 'nullable|string',
        ]);

        try {
            $equipmentDataArray = [];

            // Iterate over the validated equipment data
            foreach ($validatedData['equip'] as &$equipmentData) {
                if ($equipmentData['batch_id'] === 'New') {
                    $now = now();

                    $batchName = $now->format('m-d-Y_H-i-s');
                    $barangayId = Auth::user()->barangay_id;

                    // Create a new batch with the generated batch number
                    $newBatch = new Batches([
                        'batch_number' => $batchName,
                        'barangay_id' => $barangayId
                    ]);
                    $newBatch->save();

                    // Update equipment data with new batch ID
                    $equipmentData['batch_id'] = $newBatch->id;
                }

                // Convert 'date_acquired' to 'YYYY-MM-DD' format if provided
                if (!empty($equipmentData['date_acquired'])) {
                    $equipmentData['date_acquired'] = Carbon::createFromFormat('d M Y', $equipmentData['date_acquired'])->format('Y-m-d');
                }

                $equipment = Equipments::create($equipmentData);
                $equipmentDataArray[] = $equipment->toArray(); // Collect the data
            }

            // Log the activity with all equipment data
            activity()
                ->causedBy(Auth::user())
                ->performedOn(new Equipments)
                ->withProperties(['equipments' => $equipmentDataArray, 'type' => 'Create'])
                ->log('Added multiple equipment items');

            return redirect('/equipments')->with('toast', [
                'message' => 'Equipments added successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error($e->getMessage());

            // Redirect to a specific route with an error message
            return redirect('/equipments')->with('toast', [
                'message' => 'Failed to add equipment',
                'type' => 'error'
            ]);
        }
    }

}
