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

class MedicalSupplyController extends Controller
{

    public function index(Request $request)
    {
        $sortField = $request->input('sort', 'id'); // Default sort field
        $sortDirection = $request->input('direction', 'asc'); // Default sort direction

        $medsup = Medical_supplies::with('user', 'batch')
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
                // Sort by medical supply_name alphabetically
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

        return inertia('Inventory/MedSupplies', [
            'medical_supplies' => $medsup,
            'batches' => $batches,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'allMedsup' => $allMedsup,
            'MsupplyNames' => $MsupplyNames
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'barangay_id' => 'required|exists:barangays,id',
            'med_sup_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'batch_id' => 'required|exists:batches,id',
            'quantity' => 'required|integer',
            'provider' => 'required|string',
            'date_acquired' => 'nullable|date',
        ]);

        // Convert 'date_acquired' to 'YYYY-MM-DD' format if provided
        if (!empty($validatedData['date_acquired'])) {
            $validatedData['date_acquired'] = Carbon::createFromFormat('d M Y', $validatedData['date_acquired'])->format('Y-m-d');
        }

        $existingMedicalSupply = Medical_supplies::where([
            'med_sup_name' => $validatedData['med_sup_name'],
            'provider' => $validatedData['provider'],
            'barangay_id' => $validatedData['barangay_id'],
            'batch_id' => $validatedData['batch_id'],
        ])->first();

        $toastMessage = 'Medical supply successfully added';

        if ($existingMedicalSupply) {
            // Update the quantity of the existing Medical supply entry
            $existingMedicalSupply->quantity += $validatedData['quantity'];
            $existingMedicalSupply->computeStatus();
            $existingMedicalSupply->save();

            // Log the update activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($existingMedicalSupply)
                ->withProperties(['attributes' => $existingMedicalSupply->toArray(), 'type' => 'Update'])
                ->log('Updated existing Medical supply quantity');

           $toastMessage = 'Existing Medical supply updated with additional quantity';
        } else {
            // Create a new Medical supply entry if no matching one exists
            $medical_supply = new Medical_supplies($validatedData);
            $medical_supply->computeStatus();
            $medical_supply->save();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($medical_supply)
                ->withProperties(['attributes' => $medical_supply->toArray(), 'type' => 'Create'])
                ->log('Added a new Medical supply');
        }

        return redirect()->back()->with('toast', [
            'message' => $toastMessage,
            'type' => 'success'
        ]);
    }
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'barangay_id' => 'required|exists:barangays,id',
            'med_sup_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'batch_id' => 'required|exists:batches,id',
            'quantity' => 'required|integer',
            'provider' => 'required|string',
            'date_acquired' => 'nullable|date',
        ]);

        // Convert 'date_acquired' to 'YYYY-MM-DD' format if provided
        if (!empty($validatedData['date_acquired'])) {
            $validatedData['date_acquired'] = Carbon::createFromFormat('d M Y', $validatedData['date_acquired'])->format('Y-m-d');
        }

        // Find the medical supply by ID
        $med_sup = Medical_supplies::findOrFail($id);
        $originalData = $med_sup->getOriginal();

        // Update the medical supply with validated data
        $med_sup->fill($validatedData);

        // Recompute the status before saving changes
        $med_sup->computeStatus();

        // Save the updated medical supply with recomputed status
        $med_sup->save();

        // Log the activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($med_sup)
            ->withProperties(['old' => $originalData, 'new' => $med_sup->toArray(), 'type' => 'Update'])
            ->log('Updated the medical supply');

        return redirect()->back()->with('toast', [
            'message' => 'Medical Supply Updated Successfully',
            'type' => 'success'
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
