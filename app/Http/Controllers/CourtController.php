<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use Illuminate\Support\Facades\Storage;
use App\Services\WebNotificationService;

class CourtController extends Controller
{
    protected $webNotificationService;

    public function __construct(WebNotificationService $webNotificationService)
    {
        $this->webNotificationService = $webNotificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courts = Court::all();
        return view('courts.index', compact('courts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user() || (!auth()->user()->isOwner() && !auth()->user()->isStaff())) {
            abort(403);
        }
        return view('courts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user() || (!auth()->user()->isOwner() && !auth()->user()->isStaff())) {
            abort(403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,maintenance,closed',
            'location' => 'nullable|in:middle,edge,corner,center,side,front,back',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $validated['owner_id'] = auth()->id();
        $court = Court::create($validated);

        // Send web notifications
        try {
            $this->webNotificationService->notifyNewCourt($court);
        } catch (\Exception $e) {
            \Log::error('Failed to send web notification for new court', [
                'court_id' => $court->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('courts.index')->with('success', 'Court created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $court = Court::findOrFail($id);
        return view('courts.show', compact('court'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Court $court)
    {
        if (!auth()->user() || (!auth()->user()->isOwner() && !auth()->user()->isStaff())) {
            abort(403);
        }
        
        // Staff can edit any court, owners can only edit their own courts
        if (auth()->user()->isOwner() && $court->owner_id !== auth()->id()) {
            abort(403);
        }
        
        return view('courts.edit', compact('court'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Court $court)
    {
        if (!auth()->user() || (!auth()->user()->isOwner() && !auth()->user()->isStaff())) {
            abort(403);
        }
        
        // Staff can update any court, owners can only update their own courts
        if (auth()->user()->isOwner() && $court->owner_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,maintenance,closed',
            'location' => 'nullable|in:middle,edge,corner,center,side,front,back',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($court->image) {
                Storage::disk('public')->delete($court->image);
            }
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $court->update($validated);

        // Send web notifications
        try {
            $this->webNotificationService->notifyCourtUpdated($court);
        } catch (\Exception $e) {
            \Log::error('Failed to send web notification for court update', [
                'court_id' => $court->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('courts.index')->with('success', 'Court updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Court $court)
    {
        if (!auth()->user() || (!auth()->user()->isOwner() && !auth()->user()->isStaff())) {
            abort(403);
        }
        
        // Staff can delete any court, owners can only delete their own courts
        if (auth()->user()->isOwner() && $court->owner_id !== auth()->id()) {
            abort(403);
        }
        
        // Store court info before deletion for notifications
        $courtName = $court->name;
        $ownerId = $court->owner_id;

        if ($court->image) {
            Storage::disk('public')->delete($court->image);
        }
        $court->delete();

        // Send web notifications
        try {
            $this->webNotificationService->notifyCourtDeleted($courtName, $ownerId);
        } catch (\Exception $e) {
            \Log::error('Failed to send web notification for court deletion', [
                'court_name' => $courtName,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('courts.index')->with('success', 'Court deleted successfully.');
    }

    public function availability(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $courts = \App\Models\Court::with(['bookings' => function($q) use ($date) {
            $q->whereDate('date', $date);
        }])->get();

        $data = $courts->map(function($court) {
            return [
                'id' => $court->id,
                'name' => $court->name,
                'is_booked' => $court->bookings->count() > 0,
            ];
        });

        return response()->json($data);
    }
}
