<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;

use App\Models\Batches;
use Illuminate\Http\Request;
use App\Exports\MedsupExport;
use App\Models\Medical_supplies;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MedSupplyArchiveController extends Controller
{

    public function index(Request $request)
    {
        $sortField = $request->input('sort', 'id'); // Default sort field
        $sortDirection = $request->input('direction', 'asc'); // Default sort direction

        $medsup = Medical_supplies::with('user', 'batch')
        ->onlyTrashed()
        ->when($request->search, function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                // Search by med_sup_name or description
                $query->where('med_sup_name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            // Apply barangay_id filter
            ->where('barangay_id', Auth::user()->barangay_id);
        })

            ->where('barangay_id', Auth::user()->barangay_id)
            ->when($request->input('status'), function ($query) use ($request) {
                $status = explode(',', $request->input('status'));
                $query->whereIn('status', array_map(function ($item) {
                    return match ($item) {
                        'onStock' => 'On stock',
                        'lowOnStock' => 'Low on stock',
                        'outOfStock' => 'Out of stock',
                        default => null,
                    };
                }, $status));
            })
            ->when($sortField == 'med_sup_name', function ($query) use ($sortDirection) {
                // Sort by equipment_name alphabetically
                $query->orderBy('med_sup_name', $sortDirection);
            })
            ->when($sortField == 'status', function ($query) use ($sortDirection) {
                // Sort by status
                $query->orderByRaw("FIELD(medical_supplies.status, 'On stock', 'On stock, Expiring', 'Low on stock', 'Low on stock, Expiring', 'Out of stock', 'Out of stock, Expiring', 'Expired') $sortDirection");
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



        $allMedsup = Medical_supplies::select('id')->get();
        $MsupplyNames = Medical_supplies::selectRaw('MIN(id) as id, med_sup_name')
            ->groupBy('med_sup_name')
            ->get();
        $batches = Batches::where('barangay_id', Auth::user()->barangay_id)
        ->orderBy('created_at', 'desc')
        ->get();

        return inertia('Inventory/archiveSupply', [
            'medical_supplies' => $medsup,
            'batches' => $batches,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'allMedsup' => $allMedsup,
            'MsupplyNames' => $MsupplyNames
        ]);
    }

    public function destroy(string $id)
    {
        $med_sup = Medical_supplies::findOrFail($id);
        $med_sup->archived = true;
        $med_sup->save();

        $med_sup->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($med_sup)
            ->withProperties(['type' => 'Delete'])
            ->log('Deleted the medical supply');

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'message' => 'Medical Supply deleted successfully',
            'type' => 'success'
        ]);
    }

    public function restore($id)
    {
        $med_sup = Medical_supplies::withTrashed()->findOrFail($id);
        $med_sup->restore();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($med_sup)
            ->withProperties(['type' => 'Restore'])
            ->log('Restored the archived medical supply');

        return redirect()->back()->with('toast', [
            'message' => 'Medical Supply restored successfully',
            'type' => 'success'
        ]);
    }


    public function export($medical_supplies)
    {
        $medsupArray = explode(',', $medical_supplies);

        $date = now()->setTimezone('Asia/Manila')->format('j-F-Y');

        $filename = 'MEDICAL-SUPPLY-' . strtoupper($date) . '.xlsx';

        return Excel::download(new MedsupExport($medsupArray), $filename);
    }
}
