<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use Illuminate\Support\Facades\Storage;
use App\Services\WebNotificationService;
use Illuminate\Support\Facades\Validator;

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
        $pricingRules = $this->validatedPricingRules($request);
        $court = Court::create($validated);
        $this->syncPricingRules($court, $pricingRules);

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
        
        $court->load('pricingRules');
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

        $pricingRules = $this->validatedPricingRules($request);
        $court->update($validated);
        $this->syncPricingRules($court, $pricingRules);

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

    protected function validatedPricingRules(Request $request): array
    {
        $rawRules = array_values(array_filter($request->input('pricing_rules', []), function ($rule) {
            return is_array($rule) && (isset($rule['start_time']) || isset($rule['price_per_hour']));
        }));

        $validator = Validator::make(
            ['pricing_rules' => $rawRules],
            [
                'pricing_rules' => 'required|array|min:1',
                'pricing_rules.*.label' => 'nullable|string|max:255',
                'pricing_rules.*.start_time' => 'required|date_format:H:i',
                'pricing_rules.*.end_time' => 'required|date_format:H:i',
                'pricing_rules.*.price_per_hour' => 'required|numeric|min:0',
            ]
        );

        $validator->after(function ($validator) use ($rawRules) {
            foreach ($rawRules as $index => $rule) {
                if (($rule['start_time'] ?? '') === ($rule['end_time'] ?? '')) {
                    $validator->errors()->add("pricing_rules.{$index}.end_time", 'End time must be different from start time.');
                }
            }
        });

        $validated = $validator->validate();

        return collect($validated['pricing_rules'])
            ->map(function ($rule) {
                $start = strlen($rule['start_time']) === 5 ? "{$rule['start_time']}:00" : $rule['start_time'];
                $end = strlen($rule['end_time']) === 5 ? "{$rule['end_time']}:00" : $rule['end_time'];

                return [
                    'label' => $rule['label'] ?? null,
                    'start_time' => $start,
                    'end_time' => $end,
                    'day_of_week' => $rule['day_of_week'] ?? null,
                    'price_per_hour' => $rule['price_per_hour'],
                ];
            })
            ->toArray();
    }

    protected function syncPricingRules(Court $court, array $pricingRules): void
    {
        $court->pricingRules()->delete();
        $court->pricingRules()->createMany($pricingRules);
    }
}
