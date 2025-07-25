@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Edit Court</h1>
    <form action="{{ route('courts.update', $court) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $court->name) }}" required>
            @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $court->description) }}</textarea>
            @error('description')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Image</label>
            @if($court->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $court->image) }}" alt="{{ $court->name }}" class="h-16 w-16 object-cover rounded">
                </div>
            @endif
            <input type="file" name="image" class="w-full border rounded px-3 py-2">
            @error('image')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('courts.index') }}" class="mr-4 px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Court</button>
        </div>
    </form>
</div>
@endsection 