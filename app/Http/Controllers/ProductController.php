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
            
            // Check if user should see products page tutorial (first time on this page)
            $user = auth()->user();
            $showTutorial = $user && $user->isCustomer() && !session('products_tutorial_shown', false);
            if ($showTutorial) {
                session(['products_tutorial_shown' => true]);
            }
            
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
                'quantity' => 'required|integer|min:0',
                'image' => 'nullable|image|max:10240',
                'category' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
            ]);

            if ($request->hasFile('image')) {
                try {
                    $cloudinaryService = new \App\Services\CloudinaryService();
                    $uploadResult = $cloudinaryService->uploadImage($request->file('image'), 'products');
                    
                    if ($uploadResult) {
                        // Store the Cloudinary secure URL
                        $validated['image'] = $uploadResult['secure_url'];
                    } else {
                        // Fallback to local storage if Cloudinary is not configured
                        \Log::warning('Cloudinary upload failed, falling back to local storage');
                        $validated['image'] = $request->file('image')->store('products', 'public');
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to store product image', ['error' => $e->getMessage()]);
                    // Fallback to local storage on error
                    try {
                        $validated['image'] = $request->file('image')->store('products', 'public');
                    } catch (\Exception $fallbackError) {
                        return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()])->withInput();
                    }
                }
            }

            try {
                Product::create($validated);
                return redirect('/products')->with('success', 'Product created successfully.');
            } catch (\Exception $e) {
                \Log::error('Product creation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $validated
                ]);
                return back()->withErrors(['error' => 'Failed to save product: ' . $e->getMessage()])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Product store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
                    
                    if ($uploadResult) {
                        // Store the Cloudinary secure URL
                        $validated['image'] = $uploadResult['secure_url'];
                    } else {
                        return back()->withErrors(['image' => 'Failed to upload image to Cloudinary'])->withInput();
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to update product image', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()])->withInput();
                }
            }

            $product->update($validated);
            return redirect('/products')->with('success', 'Product updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Product update failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
                } catch (\Exception $e) {
                    \Log::error('Failed to delete product image', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue with deletion even if image deletion fails
                }
            }
            
            $product->delete();
            return redirect('/products')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Product destroy failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }
}
