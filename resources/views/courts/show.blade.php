@extends('layouts.app')

@section('content')
<div class="relative bg-gradient-to-br from-green-50 to-white pb-12">
    <div class="max-w-3xl mx-auto rounded-xl shadow-lg overflow-hidden mt-8">
        <div class="relative">
            <img src="{{ $court->image_url ?? asset('images/default-badminton-court.jpg') }}" alt="Court Image" class="w-full h-72 object-cover" onerror="this.onerror=null; this.src='{{ asset('images/default-badminton-court.jpg') }}';">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-6">
                <h1 class="text-3xl font-extrabold text-white drop-shadow mb-2">{{ $court->name }}</h1>
            </div>
        </div>
        <div class="bg-white p-8">
            <p class="mb-4 text-lg text-gray-700">{{ $court->description }}</p>
            <div class="mb-6 text-sm text-gray-500">Owned by: {{ $court->owner->name ?? 'N/A' }}</div>
            <a href="{{ route('bookings.index', ['court_id' => $court->id]) }}" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg text-lg font-semibold shadow hover:bg-green-700 transition">Book This Court</a>
            <a href="{{ route('courts.index', absolute: false) }}" class="ml-4 text-gray-600 hover:underline">Back to Courts</a>
        </div>
    </div>
</div>
@endsection 