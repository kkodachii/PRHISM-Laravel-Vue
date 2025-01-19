<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->edit($request);
    }

    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function update(ProfileUpdateRequest $request, User $profile): RedirectResponse
    {
        $profile->fill($request->validated());

        if ($profile->isDirty('email')) {
            $profile->email_verified_at = null;
        }

        $profile->save();

        return redirect()->route('profile.index')->with('status', 'Profile updated successfully.');
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        Auth::logout();

        $user->update(['status' => 'Deactivated']);

        activity()
        ->causedBy($user)
        ->withProperties(['type' => 'Deactivate'])
        ->log('User deactivated successfully');

        return redirect()->route('home')->with('toast', [
            'message' => 'User account has been marked as deactivated.',
            'type' => 'success'
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateProfilePicture(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store("profile_pictures/{$user->id}", 'public');
            $user->update(['profile_picture' => $path]);
        }

        return redirect()->route('profile.index')->with('status', 'Profile picture updated successfully!');
    }

    public function deleteProfilePicture(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        return redirect()->route('profile.index')->with('status', 'Profile picture deleted successfully!');
    }
}

