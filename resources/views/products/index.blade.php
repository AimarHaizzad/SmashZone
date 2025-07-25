@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div class="flex flex-wrap gap-3 mb-4">
            @foreach(['Shoes','Clothing','Shuttlecocks','Rackets','Bags','Accessories'] as $cat)
                <a href="?category={{ strtolower($cat) }}" class="px-5 py-2 border-2 rounded-lg font-semibold text-base {{ request('category') === strtolower($cat) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-800 border-gray-300 hover:bg-blue-50' }} transition">Badminton {{ $cat }}</a>
            @endforeach
            <a href="?" class="px-5 py-2 border-2 rounded-lg font-semibold text-base {{ !request('category') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-800 border-gray-300 hover:bg-blue-50' }} transition">All</a>
        </div>
        @if(auth()->user() && auth()->user()->role === 'owner')
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Add Product</a>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @forelse($products as $product)
            <div class="bg-white rounded-2xl shadow-lg p-4 flex flex-col relative group border border-gray-100">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-48 w-full object-contain rounded-xl mb-4 bg-gray-50">
                @else
                    <div class="h-48 w-full flex items-center justify-center bg-gray-100 rounded-xl mb-4 text-gray-400">No Image</div>
                @endif
                <div class="absolute top-4 right-4 flex flex-col gap-2 z-10">
                    <button class="bg-white rounded-full p-2 shadow hover:bg-blue-50"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-gray-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7' /></svg></button>
                    <button class="bg-white rounded-full p-2 shadow hover:bg-blue-50"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-gray-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 16l4-4m0 0l-4-4m4 4H7' /></svg></button>
                </div>
                <div class="text-sm text-gray-500 mb-1">{{ $product->brand ?? 'Brand' }}</div>
                <div class="text-lg font-bold text-gray-900 mb-1">{{ $product->name }}</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-red-600 font-bold text-lg">RM {{ number_format($product->price, 2) }}</span>
                    @if($product->old_price)
                        <span class="text-gray-400 line-through text-base">RM {{ number_format($product->old_price, 2) }}</span>
                    @endif
                </div>
                <div class="flex-1"></div>
                @if(auth()->user() && auth()->user()->role === 'owner')
                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('products.edit', $product) }}" class="bg-yellow-400 text-white px-3 py-1 rounded shadow hover:bg-yellow-500">Edit</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded shadow hover:bg-red-700">Delete</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-gray-500 py-8 col-span-full">No products found.</div>
        @endforelse
    </div>
</div>
@if(auth()->user() && auth()->user()->role === 'owner')
<!-- Remove the Add Product Modal and its script -->
@endif
@endsection 