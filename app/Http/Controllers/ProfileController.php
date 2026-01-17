<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return Redirect::route('login', absolute: false)->with('error', 'Please login to update your profile.');
            }

            $validated = $request->validated();

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                try {
                    // Delete old profile picture if exists
                    if ($user->profile_picture) {
                        Storage::disk('public')->delete($user->profile_picture);
                    }
                    
                    // Store new profile picture
                    $validated['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
                } catch (\Exception $e) {
                    \Log::error('Failed to upload profile picture', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.'])->withInput();
                }
            }

            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            return Redirect::route('profile.edit', absolute: false)->with('status', 'profile-updated');
        } catch (\Exception $e) {
            \Log::error('Profile update failed', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to update profile. Please try again.'])->withInput();
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/', absolute: false);
    }
}
