@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-green-900/90 to-blue-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-green-600 to-blue-600 rounded-3xl p-8 text-center">
        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-2">Badminton Courts</h1>
        <p class="text-xl text-green-100 font-medium">Book your favorite court and enjoy a smashing game!</p>
    </div>
</div>

<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <!-- Stats and Info -->
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center gap-2 text-green-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2M9 17H7a4 4 0 01-4-4V7a4 4 0 014-4h10a4 4 0 014 4v6a4 4 0 01-4 4h-2M9 17v2a4 4 0 004 4h2a4 4 0 004-4v-2" />
                        </svg>
                        <span class="text-2xl font-bold">{{ $courts->count() }}</span>
                    </div>
                    <span class="text-gray-600 font-medium">Courts Available</span>
                </div>
                <p class="text-gray-600">Select a date to check court availability and book your preferred time slot.</p>
            </div>
            
            <!-- Date Picker and Actions -->
            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <!-- Enhanced Date Picker -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                    <label for="availability-date" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Check Availability
                    </label>
                    <input type="date" 
                           id="availability-date" 
                           value="{{ date('Y-m-d') }}"
                           class="border-2 border-blue-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-colors">
                </div>
                
                <!-- Add Court Button (Owner and Staff) -->
                @if(auth()->user() && (auth()->user()->role === 'owner' || auth()->user()->role === 'staff'))
                    <a href="{{ route('courts.create') }}" 
                       class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Court
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced Courts Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($courts as $court)
            @php
                $isBooked = $court->bookings->count() > 0;
            @endphp
            <div id="court-card-{{ $court->id }}" 
                 class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 {{ $isBooked ? 'border-red-300' : 'border-green-200' }}">
                
                <!-- Court Image with Status Badge -->
                <div class="relative">
                    <img src="{{ $court->image ? asset('storage/' . $court->image) : asset('images/default-badminton-court.jpg') }}" 
                         alt="{{ $court->name }}" 
                         class="h-48 w-full object-cover">
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $isBooked ? 'bg-red-500 text-white' : 'bg-green-500 text-white' }}">
                            {{ $isBooked ? 'Booked' : 'Available' }}
                        </span>
                    </div>
                    
                    <!-- Court Type Badge (if available) -->
                    @if($court->type)
                        <div class="absolute top-4 left-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                                {{ ucfirst($court->type) }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <!-- Court Information -->
                <div class="p-6 flex-1 flex flex-col">
                    <h2 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ $court->name }}
                    </h2>
                    
                    <p class="text-gray-600 flex-1 mb-4 line-clamp-3">{{ $court->description ?: 'Professional badminton court with excellent facilities and lighting.' }}</p>
                    
                    <!-- Court Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Owner: {{ $court->owner->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Created: {{ $court->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('courts.show', $court) }}" 
                           class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-colors text-center text-sm">
                            Details
                        </a>
                        <a href="{{ route('bookings.create', ['court_id' => $court->id]) }}" 
                           class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-lg font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 text-center text-sm">
                            Book Now
                        </a>
                    </div>
                    
                    <!-- Owner/Staff Actions -->
                    @if(auth()->user() && (auth()->user()->role === 'owner' || auth()->user()->role === 'staff') && auth()->user()->id === $court->owner_id)
                        <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('courts.edit', $court) }}" 
                               class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg font-medium hover:bg-blue-200 transition-colors text-center text-xs">
                                Edit
                            </a>
                            <form action="{{ route('courts.destroy', $court) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this court?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full px-3 py-2 bg-red-100 text-red-700 rounded-lg font-medium hover:bg-red-200 transition-colors text-xs">
                                    Delete
                                </button>
                            </form>
                        </div>
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
                        <a href="{{ route('courts.create') }}" 
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
// Enhanced Date Picker with Loading State
document.getElementById('availability-date').addEventListener('change', function() {
    const date = this.value;
    const cards = document.querySelectorAll('[id^="court-card-"]');
    
    // Show loading state
    cards.forEach(card => {
        const statusBadge = card.querySelector('.absolute.top-4.right-4 span');
        if (statusBadge) {
            statusBadge.textContent = 'Loading...';
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-semibold bg-gray-500 text-white';
        }
    });
    
    fetch(`/courts-availability?date=${date}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(court => {
                const card = document.getElementById('court-card-' + court.id);
                if (card) {
                    const statusBadge = card.querySelector('.absolute.top-4.right-4 span');
                    if (statusBadge) {
                        if (court.is_booked) {
                            card.classList.remove('border-green-200');
                            card.classList.add('border-red-300');
                            statusBadge.textContent = 'Booked';
                            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-semibold bg-red-500 text-white';
                        } else {
                            card.classList.remove('border-red-300');
                            card.classList.add('border-green-200');
                            statusBadge.textContent = 'Available';
                            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-semibold bg-green-500 text-white';
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error fetching availability:', error);
            // Reset to default state on error
            cards.forEach(card => {
                const statusBadge = card.querySelector('.absolute.top-4.right-4 span');
                if (statusBadge) {
                    statusBadge.textContent = 'Available';
                    statusBadge.className = 'px-3 py-1 rounded-full text-sm font-semibold bg-green-500 text-white';
                }
            });
        });
});

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