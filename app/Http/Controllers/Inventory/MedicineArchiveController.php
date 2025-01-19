<?php

namespace App\Http\Controllers\Inventory;


use Carbon\Carbon;
use App\Models\Brands;
use App\Models\Batches;
use App\Models\Medicines;
use Illuminate\Http\Request;
use App\Models\Generic_names;
use App\Exports\MedicineExport;
use App\Models\Medical_categories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Pagination\LengthAwarePaginator;


class MedicineArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortField = $request->input('sort', 'expiration_date'); // Default sort field to expiration_date
        $sortDirection = $request->input('direction', 'asc'); // Default sort direction
        // Get grouped medicines by generic name with sorting and filtering applied
        $groupedMedicines = Medicines::with(['batch', 'category', 'user', 'generic_name'])
        ->onlyTrashed()
        ->when($request->input('search'), function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                // Search by generic name
                $query->whereHas('generic_name', function ($q) use ($request) {
                    $q->where('generic_name', 'like', '%' . $request->input('search') . '%');
                })
                // Search by brand
                ->orWhere('brand', 'like', '%' . $request->input('search') . '%')
                // Search by category
                ->orWhereHas('category', function ($q) use ($request) {
                    $q->where('category', 'like', '%' . $request->input('search') . '%');
                });
            })
            // Apply barangay_id filter
            ->where('barangay_id', Auth::user()->barangay_id);
        })

            ->where('barangay_id', Auth::user()->barangay_id)
            ->when($request->input('category') && $request->input('category') !== 'all', function ($query) use ($request) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('category', $request->input('category'));
                });
            })
            ->when($request->input('status'), function ($query) use ($request) {
                $status = explode(',', $request->input('status'));

                $query->where(function ($query) use ($status) {
                    if (in_array('onStock', $status)) {
                        $query->orWhere('status', 'On stock');
                    }
                    if (in_array('lowOnStock', $status)) {
                        $query->orWhere('status', 'LIKE', '%Low on stock%');
                    }
                    if (in_array('outOfStock', $status)) {
                        $query->orWhere('status', 'LIKE', '%Out of stock%');
                    }
                    if (in_array('expiring', $status)) {
                        $query->orWhere('status', 'LIKE', '%Expiring%');
                    }
                    if (in_array('expired', $status)) {
                        $query->orWhere('status', 'Expired');
                    }
                });
            })
            ->when($sortField, function ($query) use ($sortField, $sortDirection) {
                // Apply sorting based on the specified field
                switch ($sortField) {
                    case 'generic_name':
                        $query->orderByRaw("(SELECT generic_names.generic_name FROM generic_names WHERE generic_names.id = medicines.generic_name_id) $sortDirection");
                        break;
                    case 'status':
                        $query->orderByRaw("FIELD(medicines.status, 'On stock', 'On stock, Expiring', 'Low on stock', 'Low on stock, Expiring', 'Out of stock', 'Out of stock, Expiring', 'Expired') $sortDirection");
                        break;
                    case 'quantity':
                        $query->orderBy('quantity', $sortDirection);
                        break;
                    case 'date_acquired':
                        $query->orderBy('date_acquired', $sortDirection);
                        break;
                    case 'expiration_date':
                        $query->orderBy('expiration_date', $sortDirection);
                        break;
                    case 'batch_id':
                        $query->orderBy('batch_id', $sortDirection);
                        break;
                }
            })
            ->orderBy('updated_at', 'desc')
            ->orderBy('expiration_date', 'asc')
            ->select(['*', 'date_acquired as rawDateAcquired'])
            ->get()
            ->groupBy('generic_name_id'); // Group by generic name

        $groupedMedicines = collect($groupedMedicines); // Ensure it's a collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage(); // Get current page
        $perPage = 5; // Define number of generic names per page
        $currentItems = $groupedMedicines->slice(($currentPage - 1) * $perPage, $perPage)->all(); // Get items for the current page
        $paginatedMedicines = new LengthAwarePaginator(
            $currentItems,
            $groupedMedicines->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $allMedicines = Medicines::select('id')->get();

        $brandNames = Medicines::selectRaw('MIN(id) as id, brand, generic_name_id')
        ->groupBy('brand', 'generic_name_id')
        ->get();

        $batches = Batches::where('barangay_id', Auth::user()->barangay_id)
        ->orderBy('created_at', 'desc')
        ->get();

        return inertia('Inventory/archiveMedicine', [
            'groupedMedicines' => $paginatedMedicines,
            'allMedicines' => $allMedicines,
            'genericNames' => Generic_names::all(),
            'categoryNames' => Medical_categories::all(),
            'batches' => $batches,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'brandNames' => $brandNames,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $medicine = Medicines::findOrFail($id);

        $medicine->archived = true;
        $medicine->save();

        // Delete the medicine
        $medicine->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($medicine)
            ->withProperties(['type' => 'Delete'])
            ->log('Deleted the medicine');

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'message' => 'Medicine deleted successfully',
            'type' => 'success'
        ]);
    }

    public function restore($id)
{
    $medicine = Medicines::withTrashed()->findOrFail($id);
    $medicine->restore();

    activity()
        ->causedBy(Auth::user())
        ->performedOn($medicine)
        ->withProperties(['type' => 'Restore'])
        ->log('Restored the archived medicine');

    return redirect()->back()->with('toast', [
        'message' => 'Medicine restored successfully',
        'type' => 'success'
    ]);
}


    public function export($medicines)
    {
        $medicinesArray = explode(',', $medicines);

        $date = now()->setTimezone('Asia/Manila')->format('j-F-Y');

        $filename = 'MEDICINE-' . strtoupper($date) . '.xlsx';

        return Excel::download(new MedicineExport($medicinesArray), $filename);
    }
}
