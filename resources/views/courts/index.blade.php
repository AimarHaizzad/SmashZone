@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-8 sm:mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-green-900/90 to-blue-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-green-600 to-blue-600 rounded-3xl p-4 sm:p-6 lg:p-8 text-center">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mb-2">Badminton Courts</h1>
        <p class="text-base sm:text-lg lg:text-xl text-green-100 font-medium">Book your favorite court and enjoy a smashing game!</p>
    </div>
</div>

<div class="max-w-7xl mx-auto py-4 sm:py-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-semibold text-red-800">Error</h3>
            </div>
            <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 mb-6 sm:mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 sm:gap-6">
            <!-- Stats and Info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-4">
                    <div class="flex items-center gap-2 text-green-600">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2M9 17H7a4 4 0 01-4-4V7a4 4 0 014-4h10a4 4 0 014 4v6a4 4 0 01-4 4h-2M9 17v2a4 4 0 004 4h2a4 4 0 004-4v-2" />
                        </svg>
                        <span class="text-xl sm:text-2xl font-bold">{{ $courts->count() }}</span>
                    </div>
                    <span class="text-sm sm:text-base text-gray-600 font-medium">Total Courts</span>
                </div>
                <p class="text-sm sm:text-base text-gray-600">Manage your badminton courts and view their details.</p>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-center">
                <!-- Add Court Button (Owner and Staff) -->
                @if(auth()->user() && (auth()->user()->role === 'owner' || auth()->user()->role === 'staff'))
                    <a href="{{ route('courts.create', absolute: false) }}" 
                       class="w-full sm:w-auto bg-gradient-to-r from-green-600 to-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Court
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced Courts Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @forelse($courts as $court)
            <div id="court-card-{{ $court->id }}" 
                 class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                
                <!-- Court Image with Status Badge -->
                <div class="relative">
                    <img src="{{ $court->image_url ?? asset('images/default-badminton-court.jpg') }}" 
                         alt="{{ $court->name ?? 'Court' }}" 
                         class="h-40 sm:h-48 w-full object-cover"
                         onerror="this.onerror=null; this.src='{{ asset('images/default-badminton-court.jpg') }}';">
                    
                    <!-- Court Type Badge (if available) -->
                    @if($court->type)
                        <div class="absolute top-3 left-3 sm:top-4 sm:left-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                                {{ ucfirst($court->type) }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <!-- Court Information -->
                <div class="p-4 sm:p-6 flex-1 flex flex-col">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ $court->name ?? 'Unnamed Court' }}
                    </h2>
                    
                    
                    <!-- Court Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Owner: {{ $court->owner->name ?? ($court->owner_id ? 'User #' . $court->owner_id : 'Unknown') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs sm:text-sm">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium {{ ($court->status ?? 'active') === 'active' ? 'text-green-600' : (($court->status ?? 'active') === 'maintenance' ? 'text-yellow-600' : 'text-red-600') }}">
                                Status: {{ ucfirst($court->status ?? 'active') }}
                            </span>
                        </div>
                        @if($court->location)
                        <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Location: {{ ucfirst($court->location) }}</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Created: {{ $court->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    

                    
                    <!-- Owner/Staff Actions -->
                    @if(auth()->user() && (auth()->user()->role === 'owner' || auth()->user()->role === 'staff'))
                        @if(auth()->user()->role === 'staff' || auth()->user()->id === $court->owner_id)
                            <div class="flex flex-col sm:flex-row gap-2 mt-3 pt-3 border-t border-gray-100">
                                <a href="{{ route('courts.edit', $court, absolute: false) }}" 
                                   class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg font-medium hover:bg-blue-200 transition-colors text-center text-xs sm:text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('courts.destroy', $court, absolute: false) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this court?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full px-3 py-2 bg-red-100 text-red-700 rounded-lg font-medium hover:bg-red-200 transition-colors text-xs sm:text-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <!-- Enhanced Empty State -->
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No Courts Available</h3>
                    <p class="text-gray-600 mb-6">There are currently no badminton courts registered in the system.</p>
                    @if(auth()->user() && (auth()->user()->role === 'owner' || auth()->user()->role === 'staff'))
                        <a href="{{ route('courts.create', absolute: false) }}" 
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Your First Court
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
// Add smooth animations for card interactions
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('[id^="court-card-"]');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection 