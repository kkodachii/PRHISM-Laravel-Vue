<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use App\Models\Batches;
use App\Models\Medicines;
use Illuminate\Http\Request;
use App\Models\Generic_names;
use App\Models\Medical_categories;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BatchMedicineController extends Controller
{
    public function index()
    {
        $brandNames = Medicines::selectRaw('MIN(id) as id, brand')
            ->groupBy('brand')
            ->get();
        $batches = Batches::where('barangay_id', Auth::user()->barangay_id)
        ->orderBy('created_at', 'desc')
        ->get();
        // Return the data to the Inertia component
        return inertia('Inventory/MedicineBatch', [
            'batches' => $batches,
            'genericNames' => Generic_names::all(),
            'medicine' => Medicines::all(),
            'brandNames' => $brandNames,
            'categoryNames' => Medical_categories::all(),
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'medicine.*.user_id' => 'nullable|exists:users,id',
            'medicine.*.generic_name_id' => 'required|exists:generic_names,id',
            'medicine.*.brand' => 'nullable|string|max:255',
            'medicine.*.category_id' => 'required|exists:medical_categories,id',
            'medicine.*.batch_id' => 'required',
            'medicine.*.barangay_id' => 'required|exists:barangays,id',
            'medicine.*.expiration_date' => 'required|date',
            'medicine.*.quantity' => 'required|integer',
            'medicine.*.provider' => 'required|string|max:255',
            'medicine.*.date_acquired' => 'nullable|date',
        ]);

        try {
            $medicineDataArray = [];

            // Handle batch creation if needed
            $batchIds = []; // Array to hold the batch IDs for each medicine
            foreach ($validatedData['medicine'] as &$medicineData) {
                if ($medicineData['batch_id'] === 'New') {

                    $now = now();

                    $batchName = $now->format('m-d-Y_H-i-s');
                    $barangayId = Auth::user()->barangay_id;

                    // Create a new batch with the generated batch number
                    $newBatch = new Batches([
                        'batch_number' => $batchName,
                        'barangay_id' => $barangayId
                    ]);
                    $newBatch->save();

                    // Store the new batch ID for this medicine
                    $batchIds[] = $newBatch->id;
                    $medicineData['batch_id'] = $newBatch->id;
                }

                // Convert dates to 'YYYY-MM-DD' format
                if (!empty($medicineData['date_acquired'])) {
                    $medicineData['date_acquired'] = Carbon::createFromFormat('d M Y', $medicineData['date_acquired'])->format('Y-m-d');
                }
                if (!empty($medicineData['expiration_date'])) {
                    $medicineData['expiration_date'] = Carbon::createFromFormat('d M Y', $medicineData['expiration_date'])->format('Y-m-d');
                }

                $medicine = new Medicines($medicineData);
                $medicine->computeStatus();
                $medicine->save();

                $medicineDataArray[] = $medicine->toArray();
            }

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn(new Medicines)
                ->withProperties(['medicines' => $medicineDataArray, 'type' => 'Create'])
                ->log('Added multiple medicines in a batch');

            return redirect('/medicines')->with('toast', [
                'message' => 'Medicines added successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect('/medicines')->with('toast', [
                'message' => 'Failed to add medicines',
                'type' => 'error'
            ]);
        }
    }

}
