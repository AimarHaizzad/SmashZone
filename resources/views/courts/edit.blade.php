@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-green-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-blue-600 to-green-600 rounded-3xl p-8 text-center">
        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-2">Edit Court</h1>
        <p class="text-xl text-blue-100 font-medium">Update your court information and settings</p>
    </div>
</div>

<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Enhanced Form -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-green-50 px-8 py-6 border-b border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21m5.25-4l.75 4m-7.5-4h10.5a2.25 2.25 0 002.25-2.25V7.5A2.25 2.25 0 0017.25 5.25H6.75A2.25 2.25 0 004.5 7.5v7.25A2.25 2.25 0 006.75 17z" />
                </svg>
                Edit Court Information
            </h2>
            <p class="text-gray-600 mt-2">Update the details for {{ $court->name }}</p>
        </div>
        
        <form action="{{ route('courts.update', $court) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Basic Information Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Court Number -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Court Number *
                        </label>
                        <input type="text" name="name" 
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-colors text-lg" 
                               value="{{ old('name', $court->name) }}" 
                               placeholder="e.g., Court 1, Court 2, Court A, etc."
                               required>
                        @error('name')
                            <div class="text-red-500 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <!-- Court Location -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Court Location
                        </label>
                        <select name="location" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-colors text-lg">
                            <option value="">Select Court Location</option>
                            <option value="middle" {{ old('location', $court->location ?? '') == 'middle' ? 'selected' : '' }}>Middle - Center of facility</option>
                            <option value="edge" {{ old('location', $court->location ?? '') == 'edge' ? 'selected' : '' }}>Edge - Side of facility</option>
                            <option value="corner" {{ old('location', $court->location ?? '') == 'corner' ? 'selected' : '' }}>Corner - Corner position</option>
                            <option value="center" {{ old('location', $court->location ?? '') == 'center' ? 'selected' : '' }}>Center - Central area</option>
                            <option value="side" {{ old('location', $court->location ?? '') == 'side' ? 'selected' : '' }}>Side - Side area</option>
                            <option value="front" {{ old('location', $court->location ?? '') == 'front' ? 'selected' : '' }}>Front - Near entrance</option>
                            <option value="back" {{ old('location', $court->location ?? '') == 'back' ? 'selected' : '' }}>Back - Rear area</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Court Status Section -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Court Status
                </h3>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Status *
                    </label>
                    <select name="status" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-300 focus:border-green-500 transition-colors text-lg" 
                            required>
                        <option value="">Select Court Status</option>
                        <option value="active" {{ old('status', $court->status ?? '') == 'active' ? 'selected' : '' }}>Active - Available for booking</option>
                        <option value="maintenance" {{ old('status', $court->status ?? '') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        <option value="closed" {{ old('status', $court->status ?? '') == 'closed' ? 'selected' : '' }}>Closed - Not available</option>
                    </select>
                    @error('status')
                        <div class="text-red-500 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            
            <!-- Image Upload Section -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Court Image
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <label class="block text-sm font-semibold text-gray-700">Update Court Image</label>
                    </div>
                    
                    <!-- Current Image Display -->
                    @if($court->image)
                        <div class="bg-white rounded-xl p-4 border-2 border-purple-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Current Image
                            </h4>
                            <img src="{{ asset('storage/' . $court->image) }}" 
                                 alt="{{ $court->name }}" 
                                 class="h-32 w-32 object-cover rounded-lg border-2 border-purple-200">
                        </div>
                    @endif
                    
                    <!-- Image Upload Area -->
                    <div class="border-2 border-dashed border-purple-300 rounded-xl p-6 text-center hover:border-purple-400 transition-colors">
                        <div id="image-preview" class="hidden mb-4">
                            <img id="preview-img" src="" alt="Preview" class="max-w-full h-48 object-cover rounded-lg mx-auto border-2 border-purple-200">
                            <button type="button" id="remove-image" class="mt-2 text-red-600 hover:text-red-800 text-sm font-medium">
                                Remove New Image
                            </button>
                        </div>
                        
                        <div id="upload-area" class="space-y-4">
                            <svg class="w-16 h-16 text-purple-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <div>
                                <p class="text-lg font-semibold text-gray-700">Drop new image here</p>
                                <p class="text-sm text-gray-500">or click to browse</p>
                            </div>
                        </div>
                        
                        <input type="file" name="image" id="image-input" 
                               class="hidden" 
                               accept="image/*"
                               onchange="previewImage(this)">
                    </div>
                    
                    @error('image')
                        <div class="text-red-500 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('courts.index') }}" 
                   class="flex-1 px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors text-center">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-green-700 transition-all transform hover:scale-105 shadow-lg">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Court
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const uploadArea = document.getElementById('upload-area');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
            uploadArea.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}

// Remove image functionality
document.getElementById('remove-image').addEventListener('click', function() {
    document.getElementById('image-input').value = '';
    document.getElementById('image-preview').classList.add('hidden');
    document.getElementById('upload-area').classList.remove('hidden');
});

// Drag and drop functionality
const uploadArea = document.getElementById('upload-area');
const imageInput = document.getElementById('image-input');

uploadArea.addEventListener('click', () => imageInput.click());

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('border-purple-400', 'bg-purple-50');
});

uploadArea.addEventListener('dragleave', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('border-purple-400', 'bg-purple-50');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('border-purple-400', 'bg-purple-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        imageInput.files = files;
        previewImage(imageInput);
    }
});
</script>
@endsection 