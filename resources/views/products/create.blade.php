@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Add Product</h1>
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name') }}" required>
            @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Category</label>
            <select name="category" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Category</option>
                @foreach(['Shoes','Clothing','Shuttlecocks','Rackets','Bags','Accessories'] as $cat)
                    <option value="{{ strtolower($cat) }}" {{ old('category') == strtolower($cat) ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            @error('category')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Brand</label>
            <select name="brand" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Brand</option>
                @foreach(['Yonex','Li-Ning','Carlton','Victor','Apacs','Others'] as $brand)
                    <option value="{{ $brand }}" {{ old('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                @endforeach
            </select>
            @error('brand')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            @error('description')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Price (RM)</label>
            <input type="number" name="price" step="0.01" class="w-full border rounded px-3 py-2" value="{{ old('price') }}" required>
            @error('price')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Old Price (RM, optional)</label>
            <input type="number" name="old_price" step="0.01" class="w-full border rounded px-3 py-2" value="{{ old('old_price') }}">
            @error('old_price')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Quantity</label>
            <input type="number" name="quantity" class="w-full border rounded px-3 py-2" value="{{ old('quantity') }}" required>
            @error('quantity')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Image</label>
            <input type="file" name="image" class="w-full border rounded px-3 py-2">
            @error('image')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('products.index') }}" class="mr-4 px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Add Product</button>
        </div>
    </form>
</div>
@endsection 