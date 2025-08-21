@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <!-- Enhanced Header -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Book Your Court</h1>
        <p class="text-gray-600">Select your preferred court, date, and time slot</p>
    </div>

    <!-- Enhanced Form -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6">
        @csrf
            
            <!-- Court Selection -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21m5.25-4l.75 4m-7.5-4h10.5a2.25 2.25 0 002.25-2.25V7.5A2.25 2.25 0 0017.25 5.25H6.75A2.25 2.25 0 004.5 7.5v7.25A2.25 2.25 0 006.75 17z" />
                    </svg>
                    Select Court
                </label>
                <select name="court_id" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-colors text-lg" required>
                    <option value="">Choose a court...</option>
                @foreach($courts as $court)
                        <option value="{{ $court->id }}" {{ (isset($selectedCourtId) && $selectedCourtId == $court->id) || old('court_id') == $court->id ? 'selected' : '' }}>
                            {{ $court->name }}
                        </option>
                @endforeach
            </select>
                @error('court_id')
                    <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Date Selection -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Select Date
                </label>
                <input type="date" name="date" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-colors text-lg" value="{{ old('date') }}" required>
                @error('date')
                    <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Enhanced Time Selection -->
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Select Your Booking Time
                </label>
                
                <!-- Start Time Selection -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-600">Start Time</label>
                    <select name="start_time" id="start_time" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-colors text-lg" required>
                        <option value="">Choose start time...</option>
                    </select>
                </div>
                
                <!-- Duration Selection -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-600">Duration</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" class="duration-btn px-4 py-3 border-2 border-gray-200 rounded-xl font-semibold hover:border-blue-300 transition-colors" data-duration="1">
                            1 Hour
                        </button>
                        <button type="button" class="duration-btn px-4 py-3 border-2 border-gray-200 rounded-xl font-semibold hover:border-blue-300 transition-colors" data-duration="2">
                            2 Hours
                        </button>
                        <button type="button" class="duration-btn px-4 py-3 border-2 border-gray-200 rounded-xl font-semibold hover:border-blue-300 transition-colors" data-duration="3">
                            3 Hours
                        </button>
                    </div>
                </div>
                
                <!-- End Time Display -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-600">End Time</label>
                    <div id="end-time-display" class="px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-lg font-semibold text-gray-500">
                        Select start time and duration
                    </div>
                </div>
                
                <input type="hidden" name="end_time" id="end_time">
                <div id="slot-error" class="text-red-500 text-sm mt-2 flex items-center gap-1 hidden">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Please select a start time and duration.</span>
                </div>
            </div>

            <!-- Price Display -->
            <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-6 border border-blue-100">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-600">Estimated Price</span>
                        <div class="text-2xl font-bold text-blue-600" id="price_display">RM 0.00</div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                        </svg>
                    </div>
                </div>
            </div>

            @if($errors->has('overlap'))
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 text-red-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ $errors->first('overlap') }}</span>
        </div>
        </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-4">
                <a href="{{ route('bookings.index') }}" 
                   class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors text-center">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 shadow-lg">
                    Confirm Booking
                </button>
        </div>
        </form>
        </div>
        </div>

        <script>
    let selectedDuration = 1;
    let availableTimes = [];

    function populateStartTimes(booked) {
        const startTimeSelect = document.getElementById('start_time');
        startTimeSelect.innerHTML = '<option value="">Choose start time...</option>';
        
        // Generate available start times (8 AM to 9 PM)
        for (let h = 8; h < 21; h++) {
            const time = (h < 10 ? '0' : '') + h + ':00';
            const endTime = ((h + selectedDuration) < 10 ? '0' : '') + (h + selectedDuration) + ':00';
            
            // Check if this time slot is available for the selected duration
            let isAvailable = true;
            if (booked && booked.length > 0) {
                for (let i = 0; i < selectedDuration; i++) {
                    const checkTime = ((h + i) < 10 ? '0' : '') + (h + i) + ':00';
                    const isBooked = booked.some(b => (
                        (checkTime >= b.start_time && checkTime < b.end_time) ||
                        (endTime > b.start_time && endTime <= b.end_time)
                    ));
                    if (isBooked) {
                        isAvailable = false;
                        break;
                    }
                }
            }
            
            if (isAvailable) {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = time + ' - ' + endTime + ' (' + selectedDuration + ' hour' + (selectedDuration > 1 ? 's' : '') + ')';
                startTimeSelect.appendChild(option);
            }
        }
    }

    function updateEndTime() {
        const startTime = document.getElementById('start_time').value;
        const endTimeDisplay = document.getElementById('end-time-display');
        
        if (startTime && selectedDuration) {
            const [hours, minutes] = startTime.split(':').map(Number);
            const endHours = hours + selectedDuration;
            const endTime = (endHours < 10 ? '0' : '') + endHours + ':00';
            
            endTimeDisplay.textContent = endTime;
            endTimeDisplay.className = 'px-4 py-3 bg-green-50 border-2 border-green-200 rounded-xl text-lg font-semibold text-green-700';
            
            document.getElementById('end_time').value = endTime;
            document.getElementById('slot-error').classList.add('hidden');
            calculatePrice();
        } else {
            endTimeDisplay.textContent = 'Select start time and duration';
            endTimeDisplay.className = 'px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-lg font-semibold text-gray-500';
            document.getElementById('end_time').value = '';
        }
    }

    function fetchAvailability() {
        const courtId = document.querySelector('select[name=court_id]').value;
        const date = document.querySelector('input[name=date]').value;
        
        if (!courtId || !date) {
            populateStartTimes([]);
            return;
        }
        
        // Show loading state
        const startTimeSelect = document.getElementById('start_time');
        startTimeSelect.innerHTML = '<option value="">Loading available times...</option>';
        
        fetch(`/booking-availability?court_id=${courtId}&date=${date}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(booked => {
                populateStartTimes(booked);
            })
            .catch(error => {
                console.error('Error fetching availability:', error);
                // Show error state but still render slots as available
                populateStartTimes([]);
            });
    }

    function calculatePrice() {
        const start = document.querySelector('input[name=start_time]').value;
        const end = document.querySelector('input[name=end_time]').value;
        let price = 0;

        if (start && end) {
            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);
            const startMinutes = sh * 60 + sm;
            const endMinutes = eh * 60 + em;
            const hours = (endMinutes - startMinutes) / 60;
            if (hours > 0) price = hours * 20;
        }

        document.getElementById('price_display').textContent = `RM ${price.toFixed(2)}`;
    }

    // Event Listeners
    document.querySelector('select[name=court_id]').addEventListener('change', fetchAvailability);
    document.querySelector('input[name=date]').addEventListener('change', fetchAvailability);
    document.getElementById('start_time').addEventListener('change', updateEndTime);
    
    // Duration button listeners
    document.querySelectorAll('.duration-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active state from all buttons
            document.querySelectorAll('.duration-btn').forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white', 'border-blue-500');
                b.classList.add('border-gray-200', 'hover:border-blue-300');
            });
            
            // Add active state to clicked button
            this.classList.remove('border-gray-200', 'hover:border-blue-300');
            this.classList.add('bg-blue-600', 'text-white', 'border-blue-500');
            
            selectedDuration = parseInt(this.dataset.duration);
            
            // Refresh available times with new duration
            fetchAvailability();
            
            // Update end time if start time is already selected
            updateEndTime();
        });
    });
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!document.getElementById('start_time').value || !document.getElementById('end_time').value) {
            document.getElementById('slot-error').classList.remove('hidden');
            e.preventDefault();
            return false;
        }
        
        // Show loading state on form submission
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>Processing...';
        submitBtn.disabled = true;
        
        // Re-enable after a delay in case of validation errors
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });

    // Initialize with current date if no date is set
    const dateInput = document.querySelector('input[name=date]');
    if (!dateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    }
    
    // Initialize
    fetchAvailability();
        </script>
@endsection 