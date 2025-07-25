<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use Illuminate\Support\Facades\Storage;

class CourtController extends Controller
{
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
        return view('courts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $validated['owner_id'] = auth()->id();
        Court::create($validated);
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
        return view('courts.edit', compact('court'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($court->image) {
                Storage::disk('public')->delete($court->image);
            }
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $court->update($validated);
        return redirect()->route('courts.index')->with('success', 'Court updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Court $court)
    {
        if ($court->image) {
            Storage::disk('public')->delete($court->image);
        }
        $court->delete();
        return redirect()->route('courts.index')->with('success', 'Court deleted successfully.');
    }
}
