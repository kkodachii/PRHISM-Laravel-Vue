<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;

use App\Models\Batches;
use App\Models\Equipments;
use Illuminate\Http\Request;
use App\Exports\EquipmentExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        // Check and update the status of equipments based on date_acquired
        $this->updateEquipmentStatus();

        $sortField = $request->input('sort', 'id'); // Default sort field
        $sortDirection = $request->input('direction', 'asc'); // Default sort direction

        $equipments = Equipments::with('user', 'batch')
        ->where('barangay_id', Auth::user()->barangay_id)
        ->when($request->search, function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('equipment_name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->where('barangay_id', Auth::user()->barangay_id); // Make sure barangay_id filter is applied during search
        })

            ->when($request->status, function ($query) use ($request) {
                $status = explode(',', $request->status);

                $query->whereIn('status', array_map(function ($item) {
                    return match ($item) {
                        'goodCondition' => 'Good condition',
                        'new' => 'New',
                        'fairCondition' => 'Fair condition',
                        'poorCondition' => 'Poor condition',
                        'condemned' => 'Condemned',
                        default => null,
                    };
                }, $status));
            })
            ->when($sortField == 'equipment_name', function ($query) use ($sortDirection) {
                // Sort by equipment_name alphabetically
                $query->orderBy('equipment_name', $sortDirection);
            })
            ->when($sortField == 'status', function ($query) use ($sortDirection) {
                // Sort by status
                $query->orderByRaw("FIELD(equipments.status, 'New', 'Good condition', 'Fair condition', 'Poor condition', 'Condemned') $sortDirection");
            })
            ->when($sortField == 'quantity', function ($query) use ($sortDirection) {
                // Sort by quantity
                $query->orderBy('quantity', $sortDirection);
            })
            ->when($sortField == 'date_acquired', function ($query) use ($sortDirection) {
                // Sort by date_acquired
                $query->orderBy('date_acquired', $sortDirection);
            })
            ->when($sortField == 'expiration_date', function ($query) use ($sortDirection) {
                // Sort by expiration_date
                $query->orderBy('expiration_date', $sortDirection);
            })
            ->when($sortField == 'batch_id', function ($query) use ($sortDirection) {
                // Sort by batch_id
                $query->orderBy('batch_id', $sortDirection);
            })
            ->orderBy('updated_at', 'desc')
            ->select(['*', 'date_acquired as rawDateAcquired'])
            ->paginate(5);

        $allequip = Equipments::pluck('id');

        $equipmentNames = Equipments::selectRaw('MIN(id) as id, equipment_name')
            ->groupBy('equipment_name')
            ->get();

        $batches = Batches::where('barangay_id', Auth::user()->barangay_id)
        ->orderBy('created_at', 'desc')
        ->get();

        return inertia('Inventory/Equipments', [
            'equipments' => $equipments,
            'sortField' => $sortField,
            'batches' => $batches,
            'sortDirection' => $sortDirection,
            'allequip' => $allequip,
            'equipNames' => $equipmentNames,
        ]);
    }

    /**
     * Update the status of equipment based on the date acquired.
     */
    protected function updateEquipmentStatus()
    {
        $equipments = Equipments::where('status', 'New')
            ->whereNotNull('date_acquired')
            ->get();

        foreach ($equipments as $equipment) {
            $threeMonthsLater = \Carbon\Carbon::parse($equipment->date_acquired)->addMonths(3);

            if (now()->greaterThanOrEqualTo($threeMonthsLater)) {
                $equipment->update(['status' => 'Good condition']);
            }
        }
    }
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'barangay_id' => 'required|exists:barangays,id',
            'equipment_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer',
            'provider' => 'nullable|string',
            'status' => 'required|string',
            'date_acquired' => 'nullable|date',
            'accountable' => 'nullable|string',
        ]);

        $validatedData['user_id'] = Auth::user()->id;

        // Convert 'date_acquired' to 'YYYY-MM-DD' format if provided
        if (!empty($validatedData['date_acquired'])) {
            $validatedData['date_acquired'] = Carbon::createFromFormat('d M Y', $validatedData['date_acquired'])->format('Y-m-d');
        }

        $existingEquipment = Equipments::where([
            'equipment_name' => $validatedData['equipment_name'],
            'provider' => $validatedData['provider'],
            'barangay_id' => $validatedData['barangay_id'],
            'batch_id' => $validatedData['batch_id'],
        ])->first();

        $toastMessage = 'Equipment successfully added';

        if ($existingEquipment) {
            // Update the quantity of the existing equipment entry
            $existingEquipment->quantity += $validatedData['quantity'];
            $existingEquipment->save();

            // Log the update activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($existingEquipment)
                ->withProperties(['attributes' => $existingEquipment->toArray(), 'type' => 'Update'])
                ->log('Updated existing equipment quantity');

           $toastMessage = 'Existing equipment updated with additional quantity';
        } else {
            // Create a new equipment entry if no matching one exists
            $equipment = new Equipments($validatedData);
            $equipment->save();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($equipment)
                ->withProperties(['attributes' => $equipment->toArray(), 'type' => 'Create'])
                ->log('Added a new equipment');
        }

        // Redirect with success message
        return redirect()->back()->with('toast', [
            'message' => $toastMessage,
            'type' => 'success'
        ]);
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipments::findOrFail($id);
        $originalData = $equipment->getOriginal();

        $validatedData = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'batch_id' => 'nullable|exists:batches,id',
            'equipment_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'provider' => 'nullable|string',
            'status' => 'nullable|string',
            'date_acquired' => 'nullable|date',
            'accountable' => 'nullable|string',

        ]);

        if (!empty($validatedData['date_acquired'])) {
            $validatedData['date_acquired'] = Carbon::createFromFormat('d M Y', $validatedData['date_acquired'])->format('Y-m-d');
        }

        $equipment->update($validatedData);


        activity()
            ->causedBy(Auth::user())
            ->performedOn($equipment)
            ->withProperties(['old' => $originalData, 'new' => $equipment->toArray(), 'type' => 'Update'])
            ->log('Updated the equipment');


        return redirect()->back()->with('toast', [
            'message' => 'Equipment Updated Successfully',
            'type' => 'success'
        ]);
    }

    public function destroy(string $id)
    {
        $equipment = Equipments::findOrFail($id);

        $equipment->archived = true;
        $equipment->save();
        // Delete the equipment
        $equipment->delete();


        activity()
            ->causedBy(Auth::user())
            ->performedOn($equipment)
            ->withProperties(['type' => 'Delete'])
            ->log('Deleted the equipment');

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'message' => 'Equipment deleted successfully',
            'type' => 'success'
        ]);
    }

    public function restore($id)
{
    $Equipment = Equipments::withTrashed()->findOrFail($id);
    $Equipment->restore();

    activity()
        ->causedBy(Auth::user())
        ->performedOn($Equipment)
        ->withProperties(['type' => 'Restore'])
        ->log('Restored the archived equipment');

    return redirect()->back()->with('toast', [
        'message' => 'Equipment restored successfully',
        'type' => 'success'
    ]);
}

    public function export($equipments)
    {
        $equipArray = explode(',', $equipments);

        $date = now()->setTimezone('Asia/Manila')->format('j-F-Y');

        $filename = 'EQUIPMENTS-' . strtoupper($date) . '.xlsx';

        return Excel::download(new EquipmentExport($equipArray), $filename);
    }


    public function updateStatus(Request $request, $id)
{
    // Validate the request to ensure the status is provided
    $request->validate([
        'status' => 'required|string',
    ]);

    // Retrieve the equipment by ID
    $equipment = Equipments::findOrFail($id);

    // Check if the status is "Condemned"
    if ($request->status === 'Condemned') {
        $equipment->update(['status' => 'Condemned']);
        $equipment->archived = true;
        $equipment->save();
        $equipment->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($equipment)
            ->withProperties(['new' => ['status' => 'Condemned'], 'type' => 'Update'])
            ->log('Marked equipment as Condemned');

        // Return with a success message
        return redirect()->back()->with('toast', [
            'message' => 'Equipment status updated to Condemned',
            'type' => 'success'
        ]);
    }

    return redirect()->back()->with('toast', [
        'message' => 'Invalid status update',
        'type' => 'error'
    ]);
}

}
