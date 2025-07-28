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

            <!-- Time Slot Selection -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Select Time Slot
                </label>
                <div id="time-slots" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="col-span-full text-center text-gray-500 py-8">
                        Please select a court and date to view available time slots.
                    </div>
                </div>
                <input type="hidden" name="start_time" id="start_time">
                <input type="hidden" name="end_time" id="end_time">
                <div id="slot-error" class="text-red-500 text-sm mt-2 flex items-center gap-1 hidden">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Please select a time slot.</span>
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
    const timeSlots = [];
    for (let h = 8; h < 22; h++) {
        timeSlots.push({
            start: (h < 10 ? '0' : '') + h + ':00',
            end: (h+1 < 10 ? '0' : '') + (h+1) + ':00'
        });
    }

    function renderSlots(booked) {
        const container = document.getElementById('time-slots');
        container.innerHTML = '';
        
        if (!booked || booked.length === 0) {
            // Show all slots as available
            timeSlots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = slot.start + ' - ' + slot.end;
                btn.className = 'px-4 py-3 rounded-xl font-semibold bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-green-100 hover:text-green-700 hover:border-green-300 transition-all transform hover:scale-105';
                btn.onclick = function() {
                    selectSlot(btn, slot);
                };
                container.appendChild(btn);
            });
        } else {
            timeSlots.forEach(slot => {
                const isBooked = booked.some(b => (
                    (slot.start >= b.start_time && slot.start < b.end_time) ||
                    (slot.end > b.start_time && slot.end <= b.end_time)
                ));
                
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = slot.start + ' - ' + slot.end;
                
                if (isBooked) {
                    btn.className = 'px-4 py-3 rounded-xl font-semibold bg-red-100 text-red-700 border-2 border-red-200 cursor-not-allowed opacity-60';
                    btn.disabled = true;
                } else {
                    btn.className = 'px-4 py-3 rounded-xl font-semibold bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-green-100 hover:text-green-700 hover:border-green-300 transition-all transform hover:scale-105';
                    btn.onclick = function() {
                        selectSlot(btn, slot);
                    };
                }
                
                container.appendChild(btn);
            });
        }
    }

    function selectSlot(btn, slot) {
        // Remove selection from all buttons
        document.querySelectorAll('#time-slots button').forEach(b => {
            if (!b.disabled) {
                b.classList.remove('bg-green-600', 'text-white', 'border-green-500');
                b.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');
            }
        });
        
        // Select this button
        btn.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-200');
        btn.classList.add('bg-green-600', 'text-white', 'border-green-500');
        
        document.getElementById('start_time').value = slot.start;
        document.getElementById('end_time').value = slot.end;
        document.getElementById('slot-error').classList.add('hidden');
        
        calculatePrice();
    }

    function fetchAvailability() {
        const courtId = document.querySelector('select[name=court_id]').value;
        const date = document.querySelector('input[name=date]').value;
        
        if (!courtId || !date) {
            renderSlots([]);
            return;
        }
        
        // Show loading state
        const container = document.getElementById('time-slots');
        container.innerHTML = '<div class="col-span-full text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><div class="mt-2 text-gray-500">Loading available slots...</div></div>';
        
        fetch(`/booking-availability?court_id=${courtId}&date=${date}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(booked => {
                renderSlots(booked);
            })
            .catch(error => {
                console.error('Error fetching availability:', error);
                // Show error state but still render slots as available
                renderSlots([]);
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