<?php

namespace App\Http\Controllers\Category;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Generic_names;
use App\Events\GenericNameAdded;
use App\Models\Medical_categories;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GenericNameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $unusedGenericNames = DB::table('generic_names')
            ->leftJoin('medicines', 'generic_names.id', '=', 'medicines.generic_name_id')
            ->whereNull('medicines.generic_name_id')
            ->select('generic_names.id')
            ->get();

        $genericNames =  Generic_names::with('category')->when($request->search, function ($query) use ($request) {
            $query->where('generic_name', 'like', '%' . $request->search . '%');
        })->paginate(5);

        $categories = Medical_categories::all();

        return inertia('SuperAdmin/Category/GenericNames', [
            'unusedGenericNames' => $unusedGenericNames,
            'generic_names' => $genericNames,
            'categories' => $categories,
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
        // Define custom validation messages


        try {
            // Validate the request
            $validatedData = $request->validate([
                'category_id' => 'required|exists:medical_categories,id',
                'generic_name' => 'required|string|max:255|unique:generic_names,generic_name',
            ],);

            // Create the new generic name
            $generic_name = Generic_names::create($validatedData);
            $userId = Auth::user()->id;
            $user = User::find($userId);

            broadcast(new GenericNameAdded($generic_name, $user));

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($generic_name)
                ->withProperties(['attributes' => $generic_name->toArray(), 'type' => 'Create'])
                ->log('Added a new generic name');


            // Redirect with success message
            return redirect()->back()->with('toast', [
                'message' => 'Generic name successfully added',
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
                'message' => 'Failed to add generic name: ' . $e->getMessage(),
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
        $generic_name = Generic_names::findOrFail($id);



        try {
            // Validate the request
            $validatedData = $request->validate([
                'generic_name' => 'required|string|max:255|unique:generic_names,generic_name,' . $id,
            ],);

            // Capture original data for logging
            $originalData = $generic_name->getOriginal();

            // Update the generic name
            $generic_name->update($validatedData);

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($generic_name)
                ->withProperties(['old' => $originalData, 'new' => $generic_name->toArray(), 'type' => 'Update'])
                ->log('Updated the generic name');

            // Redirect with success message
            return redirect()->back()->with('toast', [
                'message' => 'Generic name updated successfully',
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
                'message' => 'Failed to update generic name: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }


    public function destroy(string $id)
    {
        $generic_name = Generic_names::findOrFail($id);

        $generic_name->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($generic_name)
            ->withProperties(['type' => 'Delete'])
            ->log('Deleted the generic name');

        return redirect()->back()->with('toast', [
            'message' => 'Generic name deleted successfully',
            'type' => 'success'
        ]);
    }
}
