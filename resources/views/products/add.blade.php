@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Add Product</h1>
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Category</label>
            <select name="category" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Category</option>
                <option value="shoes">Shoes</option>
                <option value="clothing">Clothing</option>
                <option value="shuttlecocks">Shuttlecocks</option>
                <option value="rackets">Rackets</option>
                <option value="bags">Bags</option>
                <option value="accessories">Accessories</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Brand</label>
            <select name="brand" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Brand</option>
                <option value="Yonex">Yonex</option>
                <option value="Li-Ning">Li-Ning</option>
                <option value="Carlton">Carlton</option>
                <option value="Victor">Victor</option>
                <option value="Apacs">Apacs</option>
                <option value="Others">Others</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Price (RM)</label>
            <input type="number" name="price" step="0.01" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Old Price (RM, optional)</label>
            <input type="number" name="old_price" step="0.01" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Quantity</label>
            <input type="number" name="quantity" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Image</label>
            <input type="file" name="image" class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex justify-end">
            <a href="{{ route('products.index') }}" class="mr-4 px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Add Product</button>
        </div>
    </form>
</div>
@endsection 