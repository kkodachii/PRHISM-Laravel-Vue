<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;

use App\Models\Batches;
use Illuminate\Http\Request;
use App\Models\Medical_supplies;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BatchSupplyController extends Controller
{
    public function index()
    {
        $batches = Batches::where('barangay_id', Auth::user()->barangay_id)
        ->orderBy('created_at', 'desc')
        ->get();

        $MsupplyNames = Medical_supplies::selectRaw('MIN(id) as id, med_sup_name')
            ->groupBy('med_sup_name')
            ->get();

        return inertia('Inventory/MedSuppliesBatch', [
            'batches' => $batches,
            'MsupplyNames' => $MsupplyNames
        ]);

    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'medicinesupply.*.user_id' => 'nullable|exists:users,id',
            'medicinesupply.*.barangay_id' => 'required|exists:barangays,id',
            'medicinesupply.*.med_sup_name' => 'required|string|max:255',
            'medicinesupply.*.description' => 'nullable|string',
            'medicinesupply.*.batch_id' => 'required',
            'medicinesupply.*.quantity' => 'required|integer',
            'medicinesupply.*.provider' => 'required|string',
            'medicinesupply.*.date_acquired' => 'nullable|date',
        ]);

        try {
            $medicalSuppliesDataArray = [];

            // Iterate over the validated medical supplies data
            foreach ($validatedData['medicinesupply'] as &$supplyData) {
                if ($supplyData['batch_id'] === 'New') {
                    $now = now();

                    $batchName = $now->format('m-d-Y_H-i-s');
                    $barangayId = Auth::user()->barangay_id;

                    // Create a new batch with the generated batch number
                    $newBatch = new Batches([
                        'batch_number' => $batchName,
                        'barangay_id' => $barangayId
                    ]);
                    $newBatch->save();

                    // Update supply data with new batch ID
                    $supplyData['batch_id'] = $newBatch->id;
                }

                // Convert 'date_acquired' to 'YYYY-MM-DD' format if provided
                if (!empty($supplyData['date_acquired'])) {
                    $supplyData['date_acquired'] = Carbon::createFromFormat('d M Y', $supplyData['date_acquired'])->format('Y-m-d');
                }

                $medicalSupply = new Medical_supplies($supplyData);

                // Compute status based on the logic defined in the model
                $medicalSupply->computeStatus();

                // Save the medical supply with computed status
                $medicalSupply->save();

                $medicalSuppliesDataArray[] = $medicalSupply->toArray(); // Collect the data
            }

            // Log the activity with all medical supplies data
            activity()
                ->causedBy(Auth::user())
                ->performedOn(new Medical_supplies())
                ->withProperties(['medical_supplies' => $medicalSuppliesDataArray, 'type' => 'Create'])
                ->log('Added multiple medical supply items');

            return redirect('/medical_supplies')->with('toast', [
                'message' => 'Medical supplies added successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error($e->getMessage());

            // Redirect to a specific route with an error message
            return redirect('/medical_supplies')->with('toast', [
                'message' => 'Failed to add medical supplies',
                'type' => 'error'
            ]);
        }
    }




}
