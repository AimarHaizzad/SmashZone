<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();
            
            // Filter by category if provided
            if ($request->has('category') && $request->category !== '') {
                $query->where('category', $request->category);
            }
            
            $products = $query->get();
            
            // Check if user should see products page tutorial (first time users only)
            // Use database field instead of session to persist across logouts
            $user = auth()->user();
            $showTutorial = $user && $user->isCustomer() && !$user->products_tutorial_completed;
            
            return view('products.index', compact('products', 'showTutorial'));
        } catch (\Exception $e) {
            \Log::error('Product index failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Return empty products collection if there's an error
            $products = collect();
            return view('products.index', compact('products'))->withErrors(['error' => 'Failed to load products. Please try again later.']);
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
        return view('products.create');
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
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'old_price' => 'nullable|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'image' => 'nullable|image|max:10240',
                'category' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
            ]);

            if ($request->hasFile('image')) {
                try {
                    $cloudinaryService = new \App\Services\CloudinaryService();
                    $uploadResult = $cloudinaryService->uploadImage($request->file('image'), 'products');
                    
                    if ($uploadResult && isset($uploadResult['secure_url'])) {
                        // Store the Cloudinary secure URL
                        $validated['image'] = $uploadResult['secure_url'];
                        \Log::info('Product image uploaded to Cloudinary successfully', [
                            'secure_url' => $uploadResult['secure_url']
                        ]);
                    } else {
                        // Cloudinary upload failed - check if we should use local storage
                        $cloudinaryUrl = config('cloudinary.cloud_url');
                        if (empty($cloudinaryUrl)) {
                            // Cloudinary not configured - use local storage as fallback
                            \Log::warning('Cloudinary not configured, using local storage');
                            try {
                                $imagePath = $request->file('image')->store('products', 'public');
                                $validated['image'] = $imagePath;
                                \Log::info('Product image saved to local storage', ['path' => $imagePath]);
                            } catch (\Throwable $storageException) {
                                \Log::error('Failed to save product image to local storage', [
                                    'error' => $storageException->getMessage()
                                ]);
                                unset($validated['image']);
                            }
                        } else {
                            // Cloudinary is configured but upload failed - show error
                            \Log::error('Cloudinary upload failed even though CLOUDINARY_URL is set', [
                                'has_upload_result' => !is_null($uploadResult),
                                'upload_result' => $uploadResult
                            ]);
                            return back()->withErrors([
                                'image' => 'Failed to upload image to Cloudinary. Please check your Cloudinary credentials or try again later.'
                            ])->withInput();
                        }
                    }
                } catch (\RuntimeException $e) {
                    // RuntimeExceptions from CloudinaryService (like initialization failures)
                    \Log::error('Cloudinary upload failed with RuntimeException', [
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors([
                        'image' => 'Cloudinary error: ' . $e->getMessage() . '. Please check your CLOUDINARY_URL in .env file.'
                    ])->withInput();
                } catch (\Throwable $e) {
                    \Log::error('Failed to store product image', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    
                    // Check Cloudinary config before falling back
                    $cloudinaryUrl = config('cloudinary.cloud_url');
                    if (empty($cloudinaryUrl)) {
                        // Cloudinary not configured - use local storage
                        try {
                            $imagePath = $request->file('image')->store('products', 'public');
                            $validated['image'] = $imagePath;
                            \Log::info('Product image saved to local storage (fallback)', ['path' => $imagePath]);
                        } catch (\Throwable $storageException) {
                            \Log::error('Failed to save product image to local storage', [
                                'error' => $storageException->getMessage()
                            ]);
                            unset($validated['image']);
                        }
                    } else {
                        // Cloudinary configured but upload failed - show error
                        \Log::error('Cloudinary upload exception', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        return back()->withErrors([
                            'image' => 'Failed to upload image to Cloudinary: ' . $e->getMessage()
                        ])->withInput();
                    }
                }
            }

            try {
                // Convert empty old_price to null
                if (isset($validated['old_price']) && ($validated['old_price'] === '' || $validated['old_price'] == 0)) {
                    $validated['old_price'] = null;
                }
                Product::create($validated);
                return redirect('/products')->with('success', 'Product created successfully.');
            } catch (\Throwable $e) {
                \Log::error('Product creation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'data' => $validated
                ]);
                return back()->withErrors(['error' => 'Failed to save product: ' . $e->getMessage()])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Product store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        if (!auth()->user() || (!auth()->user()->isOwner() && !auth()->user()->isStaff())) {
            abort(403);
        }
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            if (!auth()->user() || (!auth()->user()->isOwner() && !auth()->user()->isStaff())) {
                abort(403);
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'old_price' => 'nullable|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'image' => 'nullable|image|max:10240',
                'category' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
            ]);

            if ($request->hasFile('image')) {
                try {
                    $cloudinaryService = new \App\Services\CloudinaryService();
                    
                    // Delete old image from Cloudinary if exists
                    if ($product->image) {
                        $publicId = $cloudinaryService->extractPublicId($product->image);
                        $cloudinaryService->deleteImage($publicId);
                    }
                    
                    // Upload new image
                    $uploadResult = $cloudinaryService->uploadImage($request->file('image'), 'products');
                    
                    if ($uploadResult && isset($uploadResult['secure_url'])) {
                        // Store the Cloudinary secure URL
                        $validated['image'] = $uploadResult['secure_url'];
                        \Log::info('Product image uploaded to Cloudinary successfully', [
                            'product_id' => $product->id,
                            'secure_url' => $uploadResult['secure_url']
                        ]);
                    } else {
                        // Cloudinary upload failed - check if we should use local storage
                        $cloudinaryUrl = config('cloudinary.cloud_url');
                        if (empty($cloudinaryUrl)) {
                            // Cloudinary not configured - use local storage as fallback
                            \Log::warning('Cloudinary not configured, using local storage for update');
                            // Delete old local image if exists
                            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                                Storage::disk('public')->delete($product->image);
                            }
                            
                            try {
                                $imagePath = $request->file('image')->store('products', 'public');
                                $validated['image'] = $imagePath;
                                \Log::info('Product image saved to local storage', ['path' => $imagePath]);
                            } catch (\Throwable $storageException) {
                                \Log::error('Failed to save product image to local storage', [
                                    'error' => $storageException->getMessage()
                                ]);
                                return back()->withErrors(['image' => 'Failed to save image: ' . $storageException->getMessage()])->withInput();
                            }
                        } else {
                            // Cloudinary is configured but upload failed - show error
                            \Log::error('Cloudinary upload failed even though CLOUDINARY_URL is set', [
                                'product_id' => $product->id,
                                'has_upload_result' => !is_null($uploadResult),
                                'upload_result' => $uploadResult
                            ]);
                            return back()->withErrors([
                                'image' => 'Failed to upload image to Cloudinary. Please check your Cloudinary credentials or try again later.'
                            ])->withInput();
                        }
                    }
                } catch (\RuntimeException $e) {
                    // RuntimeExceptions from CloudinaryService (like initialization failures)
                    \Log::error('Cloudinary upload failed with RuntimeException during update', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors([
                        'image' => 'Cloudinary error: ' . $e->getMessage() . '. Please check your CLOUDINARY_URL in .env file.'
                    ])->withInput();
                } catch (\Throwable $e) {
                    \Log::error('Failed to update product image', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    
                    // Check Cloudinary config before falling back
                    $cloudinaryUrl = config('cloudinary.cloud_url');
                    if (empty($cloudinaryUrl)) {
                        // Cloudinary not configured - use local storage
                        try {
                            // Delete old local image if exists
                            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                                Storage::disk('public')->delete($product->image);
                            }
                            
                            $imagePath = $request->file('image')->store('products', 'public');
                            $validated['image'] = $imagePath;
                            \Log::info('Product image saved to local storage (fallback)', ['path' => $imagePath]);
                        } catch (\Throwable $storageException) {
                            \Log::error('Failed to save product image to local storage', [
                                'error' => $storageException->getMessage()
                            ]);
                            return back()->withErrors(['image' => 'Failed to upload image: ' . $storageException->getMessage()])->withInput();
                        }
                    } else {
                        // Cloudinary configured but upload failed - show error
                        \Log::error('Cloudinary upload exception during update', [
                            'product_id' => $product->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        return back()->withErrors([
                            'image' => 'Failed to upload image to Cloudinary: ' . $e->getMessage()
                        ])->withInput();
                    }
                }
            }

            try {
                // Convert empty old_price to null
                if (isset($validated['old_price']) && ($validated['old_price'] === '' || $validated['old_price'] == 0)) {
                    $validated['old_price'] = null;
                }
                $product->update($validated);
                return redirect('/products')->with('success', 'Product updated successfully.');
            } catch (\Throwable $e) {
                \Log::error('Product update failed', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                return back()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Product update failed', [
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            if (!auth()->user() || (!auth()->user()->isOwner() && !auth()->user()->isStaff())) {
                abort(403);
            }
            
            if ($product->image) {
                try {
                    $cloudinaryService = new \App\Services\CloudinaryService();
                    $publicId = $cloudinaryService->extractPublicId($product->image);
                    $cloudinaryService->deleteImage($publicId);
                } catch (\Throwable $e) {
                    \Log::error('Failed to delete product image', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    // Continue with deletion even if image deletion fails
                }
            }
            
            try {
                $product->delete();
                return redirect('/products')->with('success', 'Product deleted successfully.');
            } catch (\Throwable $e) {
                \Log::error('Product delete failed', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                return back()->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
            }
        } catch (\Throwable $e) {
            \Log::error('Product destroy failed', [
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }
}
