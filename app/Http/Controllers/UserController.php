<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Barangays;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortField = $request->input('sort', 'id'); // Default sort field
        $sortDirection = $request->input('direction', 'asc'); // Default sort direction

        // Define RHU to Barangay mapping here
        $rhuBarangaysMap = [
            "1" => Barangays::where('rhu_id', 1)->whereNotIn('barangay_name', ['RHU main'])->get(),
            "2" => Barangays::where('rhu_id', 2)->whereNotIn('barangay_name', ['RHU 2'])->get(),
            "3" => Barangays::where('rhu_id', 3)->whereNotIn('barangay_name', ['RHU 3'])->get(),
        ];

        $users = User::with(['rhus', 'role', 'barangay'])
                ->when($request->search, function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%');
                })
                ->when($request->input('status'), function ($query) use ($request) {
                    $status = explode(',', $request->input('status'));

                    $query->where(function ($query) use ($status) {
                        if (in_array('active', $status)) {
                            $query->where('status', 'Active');  // Use where instead of orWhere
                        }
                        if (in_array('deactivated', $status)) {
                            $query->where('status', 'Deactivated');  // Use where instead of orWhere
                        }
                    });

                })
                ->when($sortField == 'status', function ($query) use ($sortDirection) {
                    $query->orderBy('status', $sortDirection);
                })
                ->when($sortField == 'rhu', function ($query) use ($sortDirection) {
                    $query->orderByRaw("(SELECT rhus.rhu_name FROM rhus WHERE rhus.id = users.rhu_id) $sortDirection");
                })
                ->when($sortField == 'role', function ($query) use ($sortDirection) {
                    $query->orderByRaw("(SELECT roles.name FROM roles WHERE roles.id = users.role_id) $sortDirection");
                })
                ->when($sortField == 'barangay', function ($query) use ($sortDirection) {
                    $query->orderByRaw("(SELECT barangays.barangay_name FROM barangays WHERE barangays.id = users.barangay_id) $sortDirection");
                })
                ->paginate(5);

        return inertia('SuperAdmin/Users', [
            'user_page' => $users,
            'barangayNames' => Barangays::all(), // All barangays for selection
            'rhuBarangaysMap' => $rhuBarangaysMap // Pass the mapping
        ]);
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Method for showing the form for creating a new user
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'rhu_id' => 'nullable|exists:rhus,id',
            'barangay_id' => 'required|exists:barangays,id',
            'profile_picture' => 'file|nullable|max:10000'
        ]);

        $validatedData['rhu_id'] = $validatedData['rhu_id'] ?? 1;
        $validatedData['barangay_id'] = $validatedData['barangay_id'] ?? 1;

        if ($request->hasFile('profile_picture')) {
            $validatedData['profile_picture'] = Storage::disk('public')->put('profile_picture', $request->profile_picture);
        }

        // Hash the password before storing
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Set default role_id if not provided
        if (!isset($validatedData['role_id'])) {
            $validatedData['role_id'] = 1; // Default role_id
        }

        // Set email_verified_at to current date and time
        $validatedData['email_verified_at'] = now();

        // Create the new user
        $user = User::create($validatedData);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['attributes' => $user->toArray(), 'type' => 'Create'])
            ->log('Added a new user');

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'message' => 'User successfully added',
            'type' => 'success'
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Method for displaying a specific user
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Method for showing the form for editing a specific user
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'rhu_id' => 'nullable|exists:rhus,id',
            'barangay_id' => 'nullable|exists:barangays,id',
            'profile_picture' => 'file|nullable|mimes:jpg,png,gif|max:10000'
        ]);

        $validatedData['rhu_id'] = $validatedData['rhu_id'] ?? 1;
        $validatedData['barangay_id'] = $validatedData['barangay_id'] ?? 1;

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture); // Delete the old picture
            }
            $validatedData['profile_picture'] = $request->file('profile_picture')->store('profile_picture', 'public');
        }

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['old' => $user->getOriginal(), 'new' => $user->toArray(), 'type' => 'Update'])
            ->log('Updated user details.');

            return redirect()->back()->with('toast', [
                'message' => 'User Updated Successfully',
                'type' => 'success'
            ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Delete the user
        $user->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['type' => 'Delete'])
            ->log('Deleted the user');

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'message' => 'User deleted successfully',
            'type' => 'success'
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = User::findOrFail($id);

        $user->status = 'Deactivated';
        $user->save();

        activity()
        ->causedBy($user)
        ->performedOn($user)
        ->withProperties(['type' => 'Deactivate'])
        ->log('User deactivated successfully');


        return redirect()->back()->with('toast', [
            'message' => 'User account has been marked as deactivated.',
            'type' => 'success'
        ]);
    }

    public function updateStatusreact(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'reac_password' => ['required', 'current_password'],
        ]);

        $user = User::findOrFail($id);

        $user->status = 'Active';
        $user->save();

        activity()
        ->causedBy($user)
        ->performedOn($user)
        ->withProperties(['type' => 'Reactivate'])
        ->log('User reactivated successfully');


        return redirect()->back()->with('toast', [
            'message' => 'User account has been marked as active.',
            'type' => 'success'
        ]);
    }

    public function changePassword(Request $request, $id): RedirectResponse
    {
        // Validate the new password
        $validated = $request->validate([
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Find the user by ID
        $user = User::findOrFail($id);

        // Update the user's password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('toast', [
            'message' => 'Password updated successfully.',
            'type' => 'success'
        ]);

    }

}
