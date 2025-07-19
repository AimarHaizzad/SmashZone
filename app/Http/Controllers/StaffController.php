<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    // Show staff creation form
    public function create()
    {
        // Only owner can access
        if (Auth::user()->role !== 'owner') {
            abort(403);
        }
        return view('staff.create');
    }

    // Handle staff creation
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'owner') {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
        ]);
        return redirect()->route('dashboard')->with('status', 'Staff created successfully!');
    }

    // List all staff
    public function index()
    {
        if (Auth::user()->role !== 'owner') {
            abort(403);
        }
        $staff = User::where('role', 'staff')->get();
        return view('staff.index', compact('staff'));
    }

    // Show edit form for staff
    public function edit($id)
    {
        if (Auth::user()->role !== 'owner') {
            abort(403);
        }
        $staff = User::where('role', 'staff')->findOrFail($id);
        return view('staff.edit', compact('staff'));
    }

    // Update staff info
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'owner') {
            abort(403);
        }
        $staff = User::where('role', 'staff')->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->id,
        ]);
        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        return redirect()->route('staff.index')->with('status', 'Staff updated successfully!');
    }

    // Delete staff
    public function destroy($id)
    {
        if (Auth::user()->role !== 'owner') {
            abort(403);
        }
        $staff = User::where('role', 'staff')->findOrFail($id);
        $staff->delete();
        return redirect()->route('staff.index')->with('status', 'Staff deleted successfully!');
    }
} 