@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if (session('status') === 'profile-updated')
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-800 font-medium">Profile updated successfully!</span>
                </div>
            </div>
        @endif
        <!-- Profile Overview Card -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-sm border border-blue-100 p-8 mb-8">
            <div class="flex items-center space-x-6">
                <!-- Avatar Section -->
                <div class="relative">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ Storage::url(auth()->user()->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="w-24 h-24 rounded-full object-cover shadow-lg"
                             id="profile-preview">
                    @else
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg" id="profile-placeholder">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <button type="button" 
                            class="absolute -bottom-2 -right-2 bg-white rounded-full p-2 shadow-md hover:shadow-lg transition-shadow"
                            onclick="document.getElementById('profile-picture-input').click()">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                    <!-- Hidden file input - will be moved to form -->
                    <input type="file" 
                           id="profile-picture-input" 
                           accept="image/*" 
                           class="hidden" 
                           onchange="previewProfilePicture(this)">
                </div>
                
                <!-- User Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ auth()->user()->name }}</h1>
                    <p class="text-gray-600 mb-1">{{ auth()->user()->email }}</p>
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                        @if(auth()->user()->email_verified_at)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                Unverified
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('Profile Information') }}
                    </h3>
                    <p class="text-gray-600 mt-1">{{ __("Update your account's profile information and email address.") }}</p>
                </div>
            </div>
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Change Password Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ __('Security Settings') }}
                    </h3>
                    <p class="text-gray-600 mt-1">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                </div>
            </div>
            @include('profile.partials.update-password-form')
        </div>

        <!-- Delete Account Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        {{ __('Danger Zone') }}
                    </h3>
                    <p class="text-gray-600 mt-1">{{ __('Permanently delete your account and all associated data.') }}</p>
                </div>
            </div>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewProfilePicture(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Hide placeholder and show preview
            const placeholder = document.getElementById('profile-placeholder');
            const preview = document.getElementById('profile-preview');
            
            if (placeholder) {
                placeholder.style.display = 'none';
            }
            
            if (preview) {
                preview.src = e.target.result;
            } else {
                // Create preview element if it doesn't exist
                const avatarContainer = input.closest('.relative');
                const newPreview = document.createElement('img');
                newPreview.id = 'profile-preview';
                newPreview.src = e.target.result;
                newPreview.alt = 'Profile Picture';
                newPreview.className = 'w-24 h-24 rounded-full object-cover shadow-lg';
                avatarContainer.insertBefore(newPreview, input);
            }
            
            // Sync with form input
            const formInput = document.getElementById('profile-picture-form-input');
            if (formInput) {
                // Create a new FileList with the selected file
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(input.files[0]);
                formInput.files = dataTransfer.files;
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
