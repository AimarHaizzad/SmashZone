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
        try {
            $courts = Court::with('owner')->get();
            return view('courts.index', compact('courts'));
        } catch (\Exception $e) {
            \Log::error('Courts index failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Return empty collection if there's an error
            $courts = collect();
            return view('courts.index', compact('courts'))->withErrors(['error' => 'Failed to load courts. Please try again later.']);
        }
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
        try {
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
                try {
                    $cloudinaryService = new \App\Services\CloudinaryService();
                    $uploadResult = $cloudinaryService->uploadImage($request->file('image'), 'courts');
                    
                    if ($uploadResult && isset($uploadResult['secure_url'])) {
                        // Store the Cloudinary secure URL
                        $validated['image'] = $uploadResult['secure_url'];
                    } else {
                        // Fallback to local storage if Cloudinary is not available
                        \Log::warning('Cloudinary upload failed, falling back to local storage', [
                            'error' => 'Cloudinary upload returned null or invalid response'
                        ]);
                        
                        try {
                            $imagePath = $request->file('image')->store('courts', 'public');
                            $validated['image'] = $imagePath;
                            \Log::info('Court image saved to local storage', ['path' => $imagePath]);
                        } catch (\Throwable $storageException) {
                            \Log::error('Failed to save court image to local storage', [
                                'error' => $storageException->getMessage(),
                                'trace' => $storageException->getTraceAsString()
                            ]);
                            // Don't set image - allow court to be created without image
                            unset($validated['image']);
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error('Failed to store court image', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Fallback to local storage
                    try {
                        $imagePath = $request->file('image')->store('courts', 'public');
                        $validated['image'] = $imagePath;
                        \Log::info('Court image saved to local storage (fallback)', ['path' => $imagePath]);
                    } catch (\Throwable $storageException) {
                        \Log::error('Failed to save court image to local storage', [
                            'error' => $storageException->getMessage()
                        ]);
                        unset($validated['image']);
                    }
                }
            }

            $validated['owner_id'] = auth()->id();
            
            // Validate and sync pricing rules
            try {
                $pricingRules = $this->validatedPricingRules($request);
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Pricing rules validation failed', [
                    'error' => $e->getMessage(),
                    'errors' => $e->errors()
                ]);
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                \Log::error('Pricing rules validation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->withErrors(['pricing_rules' => 'Invalid pricing rules: ' . $e->getMessage()])->withInput();
            }
            
            try {
                $court = Court::create($validated);
            } catch (\Exception $e) {
                \Log::error('Failed to create court', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'validated' => $validated
                ]);
                return back()->withErrors(['error' => 'Failed to create court: ' . $e->getMessage()])->withInput();
            }
            
            try {
                $this->syncPricingRules($court, $pricingRules);
            } catch (\Exception $e) {
                \Log::error('Failed to sync pricing rules', [
                    'court_id' => $court->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue even if pricing rules fail
            }

            // Send web notifications
            try {
                $this->webNotificationService->notifyNewCourt($court);
            } catch (\Exception $e) {
                \Log::error('Failed to send web notification for new court', [
                    'court_id' => $court->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Use simple redirect to avoid URL generation issues
            return redirect('/courts')->with('success', 'Court created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Court store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withErrors(['error' => 'Failed to create court: ' . $e->getMessage()])->withInput();
        }
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
        try {
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
                try {
                    $cloudinaryService = new \App\Services\CloudinaryService();
                    
                    // Delete old image from Cloudinary if exists
                    if ($court->image) {
                        $publicId = $cloudinaryService->extractPublicId($court->image);
                        $cloudinaryService->deleteImage($publicId);
                    }
                    
                    // Upload new image
                    $uploadResult = $cloudinaryService->uploadImage($request->file('image'), 'courts');
                    
                    if ($uploadResult && isset($uploadResult['secure_url'])) {
                        // Store the Cloudinary secure URL
                        $validated['image'] = $uploadResult['secure_url'];
                    } else {
                        // Fallback to local storage if Cloudinary is not available
                        \Log::warning('Cloudinary upload failed, falling back to local storage', [
                            'court_id' => $court->id
                        ]);
                        
                        // Delete old local image if exists
                        if ($court->image && !filter_var($court->image, FILTER_VALIDATE_URL)) {
                            Storage::disk('public')->delete($court->image);
                        }
                        
                        try {
                            $imagePath = $request->file('image')->store('courts', 'public');
                            $validated['image'] = $imagePath;
                            \Log::info('Court image saved to local storage', ['path' => $imagePath]);
                        } catch (\Throwable $storageException) {
                            \Log::error('Failed to save court image to local storage', [
                                'error' => $storageException->getMessage()
                            ]);
                            return back()->withErrors(['image' => 'Failed to save image: ' . $storageException->getMessage()])->withInput();
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error('Failed to update court image', [
                        'court_id' => $court->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Fallback to local storage
                    try {
                        // Delete old local image if exists
                        if ($court->image && !filter_var($court->image, FILTER_VALIDATE_URL)) {
                            Storage::disk('public')->delete($court->image);
                        }
                        
                        $imagePath = $request->file('image')->store('courts', 'public');
                        $validated['image'] = $imagePath;
                        \Log::info('Court image saved to local storage (fallback)', ['path' => $imagePath]);
                    } catch (\Throwable $storageException) {
                        \Log::error('Failed to save court image to local storage', [
                            'error' => $storageException->getMessage()
                        ]);
                        return back()->withErrors(['image' => 'Failed to upload image: ' . $storageException->getMessage()])->withInput();
                    }
                }
            }

            // Validate and sync pricing rules
            try {
                $pricingRules = $this->validatedPricingRules($request);
            } catch (\Exception $e) {
                \Log::error('Pricing rules validation failed', ['error' => $e->getMessage()]);
                return back()->withErrors(['pricing_rules' => 'Invalid pricing rules: ' . $e->getMessage()])->withInput();
            }
            
            $court->update($validated);
            
            try {
                $this->syncPricingRules($court, $pricingRules);
            } catch (\Exception $e) {
                \Log::error('Failed to sync pricing rules', [
                    'court_id' => $court->id,
                    'error' => $e->getMessage()
                ]);
                // Continue even if pricing rules fail
            }

            // Send web notifications
            try {
                $this->webNotificationService->notifyCourtUpdated($court);
            } catch (\Exception $e) {
                \Log::error('Failed to send web notification for court update', [
                    'court_id' => $court->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Use simple redirect to avoid URL generation issues
            return redirect('/courts')->with('success', 'Court updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Court update failed', [
                'court_id' => $court->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to update court: ' . $e->getMessage()])->withInput();
        }
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

        // Use simple redirect to avoid URL generation issues
        return redirect('/courts')->with('success', 'Court deleted successfully.');
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
        try {
            $rawRules = array_values(array_filter($request->input('pricing_rules', []), function ($rule) {
                return is_array($rule) && (isset($rule['start_time']) || isset($rule['price_per_hour']));
            }));

            // If no pricing rules provided, return empty array (will use default pricing)
            if (empty($rawRules)) {
                return [];
            }

            $validator = Validator::make(
                ['pricing_rules' => $rawRules],
                [
                    'pricing_rules' => 'array',
                    'pricing_rules.*.label' => 'nullable|string|max:255',
                    'pricing_rules.*.start_time' => 'required|date_format:H:i',
                    'pricing_rules.*.end_time' => 'required|date_format:H:i',
                    'pricing_rules.*.price_per_hour' => 'required|numeric|min:0',
                    'pricing_rules.*.day_of_week' => 'nullable|integer|min:0|max:6',
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
                    // Ensure time is in H:i:s format for database
                    $start = $rule['start_time'];
                    if (strlen($start) === 5) {
                        $start = "{$start}:00";
                    }
                    
                    $end = $rule['end_time'];
                    if (strlen($end) === 5) {
                        $end = "{$end}:00";
                    }

                    // Convert day_of_week to integer if it's a string
                    $dayOfWeek = $rule['day_of_week'] ?? null;
                    if ($dayOfWeek !== null && !is_numeric($dayOfWeek)) {
                        $dayOfWeek = null;
                    } elseif ($dayOfWeek !== null) {
                        $dayOfWeek = (int) $dayOfWeek;
                    }

                    return [
                        'label' => $rule['label'] ?? null,
                        'start_time' => $start,
                        'end_time' => $end,
                        'day_of_week' => $dayOfWeek,
                        'price_per_hour' => (float) $rule['price_per_hour'],
                    ];
                })
                ->toArray();
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Pricing rules validation error', [
                'error' => $e->getMessage(),
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Pricing rules validation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function syncPricingRules(Court $court, array $pricingRules): void
    {
        try {
            // Only sync if pricing rules are provided
            if (empty($pricingRules)) {
                return;
            }
            
            $court->pricingRules()->delete();
            $court->pricingRules()->createMany($pricingRules);
        } catch (\Exception $e) {
            \Log::error('Failed to sync pricing rules', [
                'court_id' => $court->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
