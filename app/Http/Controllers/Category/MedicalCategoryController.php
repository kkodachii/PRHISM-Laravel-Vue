<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\Request;
use App\Models\Medical_categories;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MedicalCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $unusedCategories = DB::table('medical_categories')
    ->leftJoin('medicines', 'medical_categories.id', '=', 'medicines.category_id')
    ->whereNull('medicines.category_id')
    ->select('medical_categories.id')
    ->get();

        return inertia('SuperAdmin/Category/MedicalCategory', [
            'unusedCategories' => $unusedCategories,
            'medical_categories' => Medical_categories::when($request->search, function ($query) use ($request) {
                $query->where('category', 'like', '%' . $request->search . '%');
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
                'category' => 'required|string|max:255|unique:medical_categories,category',
            ], );

            // Create the new medical category
            $medical_category = Medical_categories::create($validatedData);

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($medical_category)
                ->withProperties(['attributes' => $medical_category->toArray(), 'type' => 'Create'])
                ->log('Added a new medical category');

            // Redirect with success message
            return redirect()->back()->with('toast', [
                'message' => 'Medical category successfully added',
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
                'message' => 'Failed to add medical category: ' . $e->getMessage(),
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
        $medical_category = Medical_categories::findOrFail($id);

        // Define custom validation messages

        try {
            // Validate the request
            $validatedData = $request->validate([
                'category' => 'required|string|max:255|unique:medical_categories,category,' . $id,
            ], );

            // Capture original data for logging
            $originalData = $medical_category->getOriginal();

            // Update the medical category
            $medical_category->update($validatedData);

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($medical_category)
                ->withProperties(['old' => $originalData, 'new' => $medical_category->toArray(),'type' => 'Update'])
                ->log('Updated the medical category');

            // Redirect with success message
            return redirect()->back()->with('toast', [
                'message' => 'Medical category updated successfully',
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
                'message' => 'Failed to update medical category: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }


    public function destroy(string $id)
    {
        $medical_category = Medical_categories::findOrFail($id);

        $medical_category->delete();

        activity()
        ->causedBy(Auth::user())
        ->performedOn($medical_category)
        ->withProperties(['type' => 'Delete'])
        ->log('Deleted the medical category');

        return redirect()->back()->with('toast', [
            'message' => 'Medical category deleted successfully',
            'type' => 'success'
        ]);
    }
}
