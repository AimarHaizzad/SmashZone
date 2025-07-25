@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Staff</h1>
        <a href="{{ route('staff.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Add Staff</a>
    </div>
    <div class="bg-white shadow rounded p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $user)
                <tr>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2 flex space-x-2">
                        <a href="{{ route('staff.edit', $user) }}" class="bg-yellow-400 text-white px-3 py-1 rounded">Edit</a>
                        <form action="{{ route('staff.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($staff->isEmpty())
            <div class="text-center text-gray-500 py-8">No staff found.</div>
        @endif
    </div>
</div>
@endsection 