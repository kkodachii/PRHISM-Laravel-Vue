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

class EquipmentArchiveController extends Controller
{
    public function index(Request $request)
    {
        // Check and update the status of equipments based on date_acquired
        $this->updateEquipmentStatus();

        $sortField = $request->input('sort', 'id'); // Default sort field
        $sortDirection = $request->input('direction', 'asc'); // Default sort direction

        $equipments = Equipments::with('user', 'batch')
        ->onlyTrashed()
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

        $allequip = Equipments::select('id')->get();

        $equipmentNames = Equipments::selectRaw('MIN(id) as id, equipment_name')
            ->groupBy('equipment_name')
            ->get();

        $batches = Batches::where('barangay_id', Auth::user()->barangay_id)
        ->orderBy('created_at', 'desc')
        ->get();

        return inertia('Inventory/archiveEquipment', [
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
