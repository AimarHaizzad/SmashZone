<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Court;
use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\StaffAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    /**
     * Staff Bookings Management (similar to owner.bookings)
     */
    public function bookings()
    {
        // Only staff can access this
        if (!auth()->user()->isStaff()) {
            abort(403, 'Unauthorized. Only staff can access this feature.');
        }

        // Staff can see all bookings (not just owner-specific ones)
        $bookings = Booking::with(['court', 'user', 'payment'])
            ->orderBy('date', 'desc')
            ->get();
            
        return view('staff.bookings', compact('bookings'));
    }

    // === TEAM MANAGEMENT (OWNER ONLY) ===

    /**
     * Display a listing of staff members.
     */
    public function index()
    {
        // Check if user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can access staff management.');
        }

        $staff = User::where('role', 'staff')->orderBy('created_at', 'desc')->get();
        
        return view('staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        // Check if user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can access staff management.');
        }

        return view('staff.create');
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        // Check if user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can access staff management.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'phone' => $request->phone,
            'position' => $request->position,
            'email_verified_at' => now(), // Auto-verify staff accounts
        ]);

        // Send email notification to the new staff member
        $staff->notify(new StaffAccountCreated($staff, $request->password));

        return redirect()->route('staff.index', absolute: false)
            ->with('success', 'Staff member created successfully! An email with account details has been sent to ' . $staff->email);
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(User $staff)
    {
        // Check if user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can access staff management.');
        }

        if ($staff->role !== 'staff') {
            abort(404);
        }
        
        return view('staff.edit', compact('staff'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, User $staff)
    {
        // Check if user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can access staff management.');
        }

        if ($staff->role !== 'staff') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $staff->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
        ]);

        if ($request->filled('password')) {
            $staff->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect()->route('staff.index', absolute: false)
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Activate/verify a staff member
     */
    public function activate(User $staff)
    {
        // Check if user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can access staff management.');
        }

        if ($staff->role !== 'staff') {
            abort(404);
        }

        // Verify the staff member's email
        $staff->update([
            'email_verified_at' => now()
        ]);

        return redirect()->route('staff.index', absolute: false)
            ->with('success', 'Staff member activated successfully!');
    }

    /**
     * Activate all pending staff members
     */
    public function activateAll()
    {
        // Check if user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can access staff management.');
        }

        // Activate all pending staff (where email_verified_at is null)
        $activatedCount = User::where('role', 'staff')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);

        return redirect()->route('staff.index', absolute: false)
            ->with('success', "Successfully activated {$activatedCount} staff member(s)!");
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(User $staff)
    {
        // Check if user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can access staff management.');
        }

        if ($staff->role !== 'staff') {
            abort(404);
        }

        $staff->delete();

        return redirect()->route('staff.index', absolute: false)
            ->with('success', 'Staff member deleted successfully!');
    }
} 