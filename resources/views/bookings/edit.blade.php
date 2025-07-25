@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Edit Booking</h1>
    <form action="{{ route('bookings.update', $booking) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Court</label>
            <select name="court_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Court</option>
                @foreach($courts as $court)
                    <option value="{{ $court->id }}" {{ old('court_id', $booking->court_id) == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                @endforeach
            </select>
            @error('court_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Date</label>
            <input type="date" name="date" class="w-full border rounded px-3 py-2" value="{{ old('date', $booking->date) }}" required>
            @error('date')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Start Time</label>
            <input type="time" name="start_time" class="w-full border rounded px-3 py-2" value="{{ old('start_time', $booking->start_time) }}" required>
            @error('start_time')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">End Time</label>
            <input type="time" name="end_time" id="end_time" class="w-full border rounded px-3 py-2" value="{{ old('end_time', $booking->end_time) }}" required>
            @error('end_time')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Estimated Price</label>
            <input type="text" id="price_display" class="w-full border rounded px-3 py-2 bg-gray-100" value="RM {{ number_format($booking->total_price, 2) }}" readonly>
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
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2" required>
                <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        @if($errors->has('overlap'))
            <div class="text-red-500 text-sm mb-4">{{ $errors->first('overlap') }}</div>
        @endif
        <div class="flex justify-end">
            <a href="{{ route('bookings.index') }}" class="mr-4 px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Booking</button>
        </div>
    </form>
</div>
@endsection 