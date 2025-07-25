@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Add Booking</h1>
    <form action="{{ route('bookings.store') }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Court</label>
            <select name="court_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Court</option>
                @foreach($courts as $court)
                    <option value="{{ $court->id }}" {{ (isset($selectedCourtId) && $selectedCourtId == $court->id) || old('court_id') == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                @endforeach
            </select>
            @error('court_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Date</label>
            <input type="date" name="date" class="w-full border rounded px-3 py-2" value="{{ old('date') }}" required>
            @error('date')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Time Slots</label>
            <div id="time-slots" class="flex flex-wrap gap-2"></div>
            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">
            <div id="slot-error" class="text-red-500 text-sm mt-2"></div>
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
            timeSlots.forEach(slot => {
                const isBooked = booked.some(b => (
                    (slot.start >= b.start_time && slot.start < b.end_time) ||
                    (slot.end > b.start_time && slot.end <= b.end_time)
                ));
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = slot.start + ' - ' + slot.end;
                btn.className = 'px-3 py-1 rounded font-semibold ' +
                    (isBooked ? 'bg-red-500 text-white cursor-not-allowed' : 'bg-gray-200 text-gray-700 hover:bg-green-500 hover:text-white');
                btn.disabled = isBooked;
                btn.onclick = function() {
                    document.querySelectorAll('#time-slots button').forEach(b => b.classList.remove('bg-green-600', 'text-white'));
                    btn.classList.add('bg-green-600', 'text-white');
                    document.getElementById('start_time').value = slot.start;
                    document.getElementById('end_time').value = slot.end;
                    document.getElementById('slot-error').textContent = '';
                };
                container.appendChild(btn);
            });
        }
        function fetchAvailability() {
            const courtId = document.querySelector('select[name=court_id]').value;
            const date = document.querySelector('input[name=date]').value;
            if (!courtId || !date) {
                renderSlots([]);
                return;
            }
            fetch(`/booking-availability?court_id=${courtId}&date=${date}`)
                .then(r => r.json())
                .then(booked => renderSlots(booked));
        }
        document.querySelector('select[name=court_id]').addEventListener('change', fetchAvailability);
        document.querySelector('input[name=date]').addEventListener('change', fetchAvailability);
        // Initial render
        fetchAvailability();
        // Prevent form submit if no slot selected
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!document.getElementById('start_time').value || !document.getElementById('end_time').value) {
                document.getElementById('slot-error').textContent = 'Please select a time slot.';
                e.preventDefault();
            }
        });
        </script>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Estimated Price</label>
            <input type="text" id="price_display" class="w-full border rounded px-3 py-2 bg-gray-100" value="RM 0.00" readonly>
        </div>
        <script>
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
                document.getElementById('price_display').value = `RM ${price.toFixed(2)}`;
            }
            document.querySelector('input[name=start_time]').addEventListener('change', calculatePrice);
            document.querySelector('input[name=end_time]').addEventListener('change', calculatePrice);
        </script>
        @if($errors->has('overlap'))
            <div class="text-red-500 text-sm mb-4">{{ $errors->first('overlap') }}</div>
        @endif
        <div class="flex justify-end">
            <a href="{{ route('bookings.index') }}" class="mr-4 px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Add Booking</button>
        </div>
    </form>
</div>
@endsection 