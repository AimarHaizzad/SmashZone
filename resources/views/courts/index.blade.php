@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-green-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2M9 17H7a4 4 0 01-4-4V7a4 4 0 014-4h10a4 4 0 014 4v6a4 4 0 01-4 4h-2M9 17v2a4 4 0 004 4h2a4 4 0 004-4v-2" /></svg>
            Badminton Courts
        </h1>
        @if(auth()->user() && auth()->user()->role === 'owner')
            <a href="{{ route('courts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Add Court</a>
        @endif
    </div>
    <div class="mb-4 text-gray-600">Book your favorite court and enjoy a smashing game!</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @forelse($courts as $court)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
                <img src="{{ $court->image ? asset('storage/' . $court->image) : asset('images/default-badminton-court.jpg') }}" alt="{{ $court->name }}" class="h-48 w-full object-cover">
                <div class="p-4 flex-1 flex flex-col">
                    <h2 class="text-xl font-bold text-green-800 mb-2">{{ $court->name }}</h2>
                    <p class="text-gray-600 flex-1">{{ $court->description }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <a href="{{ route('courts.show', $court) }}" class="text-blue-600 hover:underline font-semibold">Details</a>
                        <a href="{{ route('bookings.create', ['court_id' => $court->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Book</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8 col-span-full">No courts found.</div>
        @endforelse
    </div>
</div>
@endsection 