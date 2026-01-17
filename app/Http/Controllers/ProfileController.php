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
                    $cloudinaryService = new \App\Services\CloudinaryService();
                    
                    // Delete old profile picture if exists
                    if ($user->profile_picture) {
                        // Check if it's a Cloudinary URL or local file
                        if (filter_var($user->profile_picture, FILTER_VALIDATE_URL)) {
                            // It's a Cloudinary URL - delete from Cloudinary
                            $publicId = $cloudinaryService->extractPublicId($user->profile_picture);
                            if ($publicId) {
                                $cloudinaryService->deleteImage($publicId);
                            }
                        } else {
                            // It's a local file - delete from local storage
                            Storage::disk('public')->delete($user->profile_picture);
                        }
                    }
                    
                    // Upload new profile picture to Cloudinary
                    $uploadResult = $cloudinaryService->uploadImage($request->file('profile_picture'), 'profile-pictures');
                    
                    if ($uploadResult && isset($uploadResult['secure_url'])) {
                        // Store the Cloudinary secure URL
                        $validated['profile_picture'] = $uploadResult['secure_url'];
                        \Log::info('Profile picture uploaded to Cloudinary successfully', [
                            'user_id' => $user->id,
                            'secure_url' => $uploadResult['secure_url']
                        ]);
                    } else {
                        // Cloudinary upload failed - check if we should use local storage
                        $cloudinaryUrl = config('cloudinary.cloud_url');
                        if (empty($cloudinaryUrl)) {
                            // Cloudinary not configured - use local storage as fallback
                            \Log::warning('Cloudinary not configured, using local storage for profile picture');
                            try {
                                $imagePath = $request->file('profile_picture')->store('profile-pictures', 'public');
                                $validated['profile_picture'] = $imagePath;
                                \Log::info('Profile picture saved to local storage', ['path' => $imagePath]);
                            } catch (\Throwable $storageException) {
                                \Log::error('Failed to save profile picture to local storage', [
                                    'error' => $storageException->getMessage()
                                ]);
                                return back()->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.'])->withInput();
                            }
                        } else {
                            // Cloudinary is configured but upload failed - show error
                            \Log::error('Cloudinary upload failed even though CLOUDINARY_URL is set');
                            return back()->withErrors(['profile_picture' => 'Failed to upload profile picture to Cloudinary. Please check your Cloudinary credentials or try again later.'])->withInput();
                        }
                    }
                } catch (\RuntimeException $e) {
                    // RuntimeExceptions from CloudinaryService (like initialization failures)
                    \Log::error('Cloudinary upload failed with RuntimeException', [
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['profile_picture' => 'Cloudinary error: ' . $e->getMessage() . '. Please check your CLOUDINARY_URL in .env file.'])->withInput();
                } catch (\Exception $e) {
                    \Log::error('Failed to upload profile picture', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return back()->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.'])->withInput();
                }
            }

            // Fill user with validated data
            $user->fill($validated);

            // If email is changed, reset email verification
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            // Save the user
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
