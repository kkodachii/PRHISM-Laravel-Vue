<?php

namespace App\Http\Controllers\Category;

use App\Models\Barangays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BarangayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $unused = DB::table('barangays')
    ->leftJoin('users', 'barangays.id', '=', 'users.barangay_id')
    ->whereNull('users.barangay_id')
    ->select('barangays.id')
    ->get();

        return inertia('SuperAdmin/Category/Barangay', [
            'unusedBarangays' => $unused,
            'barangays' => Barangays::with('rhu')->when($request->search, function ($query) use ($request) {
                $query->where('barangay_name', 'like', '%' . $request->search . '%');
            })->paginate(5),
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


        try {
            // Validate the request
            $validatedData = $request->validate([
                'barangay_name' => 'required|string|max:255|unique:barangays,barangay_name',
                'rhu_id' => 'required|string|max:255',
            ],);

            // Create the new barangay
            $barangay = Barangays::create($validatedData);

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($barangay)
                ->withProperties(['attributes' => $barangay->toArray(),'type' => 'Create'])
                ->log('Added a new barangay');

            // Redirect with success message
            return redirect()->back()->with('toast', [
                'message' => 'Barangay successfully added',
                'type' => 'success'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exception
            $errors = $e->validator->errors()->all();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->withErrors($e->validator->errors())->withInput()->with('toast', [
                'message' =>$errorMessage,
                'type' => 'error'
            ]);
        } catch (\Exception $e) {
            // Handle other exceptions
            return redirect()->back()->with('toast', [
                'message' => 'Failed to add barangay: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
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
    public function update(Request $request, $id)
    {
        $barangay = Barangays::findOrFail($id);



        try {
            // Validate the request
            $validatedData = $request->validate([
                'barangay_name' => 'required|string|max:255|unique:barangays,barangay_name,' . $id,
                'rhu_id' => 'required|string|max:255',
            ], );

            // Capture original data for logging
            $originalData = $barangay->getOriginal();

            // Update the barangay
            $barangay->update($validatedData);

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($barangay)
                ->withProperties(['old' => $originalData, 'new' => $barangay->toArray(), 'type' => 'Update'])
                ->log('Updated the barangay');

            // Redirect with success message
            return redirect()->back()->with('toast', [
                'message' => 'Barangay updated successfully',
                'type' => 'success'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exception
            $errors = $e->validator->errors();
            $errorMessage = $errors->first(); // Get the first error message
            return redirect()->back()->withErrors($errors)->withInput()->with('toast', [
                'message' => $errorMessage,
                'type' => 'error'
            ]);
        } catch (\Exception $e) {
            // Handle other exceptions
            return redirect()->back()->with('toast', [
                'message' => 'Failed to update barangay: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }




    public function destroy(string $id)
    {
        $barangay = Barangays::findOrFail($id);

        $barangay->delete();

        activity()
        ->causedBy(Auth::user())
        ->performedOn($barangay)
        ->withProperties(['type' => 'Delete'])
        ->log('Deleted the barangay');

        return redirect()->back()->with('toast', [
            'message' => 'Barangay deleted successfully',
            'type' => 'success'
        ]);
    }
}
