@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-green-900/90 to-blue-900/90 rounded-3xl"></div>
    <img src="/images/badminton-hero.jpg" alt="Badminton Products" class="w-full h-64 object-cover rounded-3xl shadow-2xl">
    <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-4">
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
            <h1 class="text-5xl font-extrabold text-white drop-shadow-lg mb-4">Badminton Gear</h1>
            <p class="text-xl text-green-100 font-medium drop-shadow mb-6">Premium equipment • Professional quality • Best prices</p>
            <div class="flex items-center justify-center gap-6 text-white/90">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm font-medium">Premium Brands</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                    </svg>
                    <span class="text-sm font-medium">Fast Delivery</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">Quality Guaranteed</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-semibold text-red-800">Error</h3>
            </div>
            <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Enhanced Category Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100" data-tutorial="category-filters">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-wrap gap-3">
            @foreach(['Shoes','Clothing','Shuttlecocks','Rackets','Bags','Accessories'] as $cat)
                    <a href="?category={{ strtolower($cat) }}" 
                       class="px-6 py-3 border-2 rounded-xl font-semibold text-base transition-all transform hover:scale-105 {{ request('category') === strtolower($cat) ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white border-blue-600 shadow-lg' : 'bg-white text-gray-700 border-gray-200 hover:bg-blue-50 hover:border-blue-300' }}"
                       data-tutorial="category-{{ strtolower($cat) }}">
                        <div class="flex items-center gap-2">
                            @if(strtolower($cat) === 'shoes')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            @elseif(strtolower($cat) === 'clothing')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            @elseif(strtolower($cat) === 'shuttlecocks')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            @elseif(strtolower($cat) === 'rackets')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif(strtolower($cat) === 'bags')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            @endif
                            {{ $cat }}
                        </div>
                    </a>
            @endforeach
                <a href="?" 
                   class="px-6 py-3 border-2 rounded-xl font-semibold text-base transition-all transform hover:scale-105 {{ !request('category') ? 'bg-gradient-to-r from-green-600 to-green-700 text-white border-green-600 shadow-lg' : 'bg-white text-gray-700 border-gray-200 hover:bg-green-50 hover:border-green-300' }}"
                   data-tutorial="all-products-btn">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        All Products
                    </div>
                </a>
            </div>
            @if(auth()->user() && (auth()->user()->role === 'owner' || auth()->user()->role === 'staff'))
                <a href="{{ route('products.create', absolute: false) }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl shadow-lg hover:from-blue-700 hover:to-blue-800 font-semibold transition-all transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Product
                </a>
            @endif
        </div>
    </div>

    <!-- Enhanced Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8" data-tutorial="product-grid">
        @forelse($products as $product)
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" {{ $loop->first ? 'data-tutorial="product-card-example"' : '' }}>
                <!-- Product Image -->
                <div class="relative overflow-hidden">
                    @if($product->image)
                        <img src="{{ $product->image_url ?? asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="h-64 w-full object-cover group-hover:scale-110 transition-transform duration-300"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23ddd\' width=\'400\' height=\'300\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'18\' dy=\'10.5\' font-weight=\'bold\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                    @else
                        <div class="h-64 w-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-500 font-medium">No Image</p>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Quick Action Buttons -->
                    <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-lg hover:bg-white transition-colors">
                            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                        <button class="bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-lg hover:bg-white transition-colors">
                            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Category Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($product->category ?? 'General') }}
                        </span>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-6">
                    <div class="text-sm text-gray-500 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ $product->brand ?? 'Brand' }}
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2">{{ $product->name }}</h3>
                    
                    <!-- Price Section -->
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-2xl font-bold text-red-600">RM {{ number_format($product->price ?? 0, 2) }}</span>
                        @if(!empty($product->old_price) && $product->old_price > $product->price)
                            <span class="text-lg text-gray-400 line-through">RM {{ number_format($product->old_price, 2) }}</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                {{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}% OFF
                            </span>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    @if(!auth()->user() || (auth()->user() && auth()->user()->role !== 'owner'))
                        <div class="mb-3 text-sm {{ ($product->quantity ?? 0) > 0 ? 'text-gray-600' : 'text-red-600 font-semibold' }}">
                            @if(($product->quantity ?? 0) > 0)
                                In stock: {{ $product->quantity }}
                            @else
                                Out of stock
                            @endif
                        </div>
                        <form action="{{ route('cart.add', absolute: false) }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="flex items-center gap-2" {{ $loop->first ? 'data-tutorial="quantity-selector"' : '' }}>
                                <label for="qty-{{ $product->id }}" class="text-sm font-medium text-gray-700">Quantity:</label>
                                <input id="qty-{{ $product->id }}" name="quantity" type="number" min="1" value="1" 
                                       max="{{ max(1, $product->quantity ?? 0) }}"
                                       {{ ($product->quantity ?? 0) <= 0 ? 'disabled' : '' }}
                                       class="w-20 border-2 border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-colors {{ $product->quantity <= 0 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : '' }}"
                                       {{ $loop->first ? 'data-tutorial="quantity-input"' : '' }}>
                            </div>
                            <button type="submit" 
                                    {{ $product->quantity <= 0 ? 'disabled' : '' }}
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $loop->first ? 'data-tutorial="add-to-cart-btn"' : '' }}>
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
                                    </svg>
                                    {{ $product->quantity > 0 ? 'Add to Cart' : 'Out of Stock' }}
                                </div>
                            </button>
                        </form>
                    @endif
                    
                    @if(auth()->user() && (auth()->user()->role === 'owner' || auth()->user()->role === 'staff'))
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('products.edit', $product, absolute: false) }}" 
                               class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-4 py-3 rounded-xl font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all transform hover:scale-105 shadow-lg text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </div>
                            </a>
                            <form action="{{ route('products.destroy', $product, absolute: false) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-3 rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition-all transform hover:scale-105 shadow-lg">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </div>
                                </button>
                        </form>
                    </div>
                @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Products Found</h3>
                    <p class="text-gray-500 mb-6">We couldn't find any products matching your criteria.</p>
                    @if(auth()->user() && (auth()->user()->role === 'owner' || auth()->user()->role === 'staff'))
                        <a href="{{ route('products.create', absolute: false) }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl shadow-lg hover:from-blue-700 hover:to-blue-800 font-semibold transition-all transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Your First Product
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>

@if(isset($showTutorial) && $showTutorial)
    @push('scripts')
    <script>
    (function() {
        'use strict';
        
        function initProductsTutorial() {
            if (typeof introJs === 'undefined') {
                console.error('Intro.js library not loaded');
                return;
            }
            
            function elementExists(selector) {
                const element = document.querySelector(selector);
                if (!element) return false;
                const rect = element.getBoundingClientRect();
                const style = window.getComputedStyle(element);
                return rect.width > 0 && rect.height > 0 && style.display !== 'none' && style.visibility !== 'hidden';
            }
            
            // Find first product card for tutorial
            function findFirstProductCard() {
                const productCards = document.querySelectorAll('[data-tutorial="product-card-example"]');
                if (productCards.length > 0) {
                    const firstCard = productCards[0];
                    if (!firstCard.id) {
                        firstCard.id = 'tutorial-product-card';
                    }
                    return '#tutorial-product-card';
                }
                // Fallback: find any product card
                const anyCard = document.querySelector('.grid > div');
                if (anyCard && !anyCard.id) {
                    anyCard.id = 'tutorial-product-card';
                    return '#tutorial-product-card';
                }
                return null;
            }
            
            // Find first add to cart button
            function findAddToCartButton() {
                const addButtons = document.querySelectorAll('[data-tutorial="add-to-cart-btn"]');
                if (addButtons.length > 0) {
                    return '[data-tutorial="add-to-cart-btn"]';
                }
                // Fallback: find any add to cart button
                const anyButton = document.querySelector('button[type="submit"]');
                if (anyButton) {
                    if (!anyButton.id) {
                        anyButton.id = 'tutorial-add-cart-btn';
                    }
                    return '#tutorial-add-cart-btn';
                }
                return null;
            }
            
            // Find quantity input
            function findQuantityInput() {
                const quantityInput = document.querySelector('[data-tutorial="quantity-input"]');
                if (quantityInput) {
                    return '[data-tutorial="quantity-input"]';
                }
                // Fallback: find first quantity input
                const anyInput = document.querySelector('input[type="number"][name="quantity"]');
                if (anyInput) {
                    if (!anyInput.id) {
                        anyInput.id = 'tutorial-quantity-input';
                    }
                    return '#tutorial-quantity-input';
                }
                return null;
            }
            
            // Find quantity selector container
            function findQuantitySelector() {
                const quantitySelector = document.querySelector('[data-tutorial="quantity-selector"]');
                if (quantitySelector) {
                    return '[data-tutorial="quantity-selector"]';
                }
                return null;
            }
            
            const steps = [
                {
                    element: '[data-tutorial="category-filters"]',
                    intro: '<div style="text-align: center;"><h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: 600; color: #1f2937;">🛍️ Step 1: Welcome to Our Shop!</h3><p style="margin: 0; color: #6b7280; line-height: 1.6;">Welcome to SmashZone\'s product catalog! Here you can browse and purchase premium badminton equipment. Let\'s learn how to shop step by step!</p></div>',
                    position: 'bottom',
                    tooltipClass: 'introjs-tooltip-custom'
                },
                {
                    element: '[data-tutorial="category-rackets"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🏸 Step 2: Category Filters</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Use these category buttons to filter products by type:<br>• <strong>Shoes</strong> - Badminton footwear<br>• <strong>Clothing</strong> - Sports apparel<br>• <strong>Rackets</strong> - Badminton rackets<br>• <strong>Shuttlecocks</strong> - Game equipment<br>• <strong>Bags</strong> - Sports bags<br>• <strong>Accessories</strong> - Other gear<br><br>Click any category to see only those products. The selected category will be highlighted in blue!</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="all-products-btn"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📦 Step 3: View All Products</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Click the <strong>"All Products"</strong> button (green button on the right) to remove any filters and see everything we have in stock. This is useful when you want to browse all available items without restrictions!</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="product-grid"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📋 Step 4: Product Grid</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">This grid displays all products matching your current filter. Each card shows:<br>• Product image<br>• Brand and name<br>• Price<br>• Stock availability<br>• Category badge<br><br>Scroll down to see more products. Let\'s look at a product card in detail!</p></div>',
                    position: 'top'
                }
            ];
            
            // Add product card step if products exist
            const productCardSelector = findFirstProductCard();
            if (productCardSelector && elementExists(productCardSelector)) {
                steps.push({
                    element: productCardSelector,
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🛍️ Step 5: Understanding Product Cards</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Each product card displays:<br>• <strong>Category badge</strong> (top left) - Shows product category<br>• <strong>Product image</strong> - Visual preview<br>• <strong>Brand name</strong> - Manufacturer<br>• <strong>Product name</strong> - Full product title<br>• <strong>Price</strong> - Current price (red) with discount if available<br>• <strong>Stock status</strong> - Shows "In stock: X" or "Out of stock"<br><br>Hover over the card to see quick action buttons (like/filter)!</p></div>',
                    position: 'top'
                });
            }
            
            // Add quantity selector step
            const quantitySelector = findQuantitySelector();
            const quantityInput = findQuantityInput();
            if ((quantitySelector && elementExists(quantitySelector)) || (quantityInput && elementExists(quantityInput))) {
                const selectorToUse = quantitySelector || quantityInput;
                steps.push({
                    element: selectorToUse,
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🔢 Step 6: Select Quantity</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Before adding to cart, choose how many items you want using the <strong>quantity input field</strong>. You can:<br>• Type a number directly<br>• Use the up/down arrows on the input<br>• Maximum quantity is limited by stock availability<br><br>If the product is out of stock, the quantity field will be disabled (greyed out).</p></div>',
                    position: 'top'
                });
            }
            
            // Add add to cart step if button exists
            const addCartSelector = findAddToCartButton();
            if (addCartSelector && elementExists(addCartSelector)) {
                steps.push({
                    element: addCartSelector,
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🛒 Step 7: Add to Cart</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Once you\'ve selected the quantity, click the <strong>"Add to Cart"</strong> button! This will:<br>• Add the product to your shopping cart<br>• Save your selection<br>• Allow you to continue shopping<br>• Show a confirmation message<br><br>You can add multiple products to your cart before checking out!</p></div>',
                    position: 'top'
                });
                
                // Add final step about what happens next
                steps.push({
                    element: addCartSelector,
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">✅ Step 8: After Adding to Cart</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">After clicking "Add to Cart":<br>• The product is saved to your cart<br>• You\'ll see a success notification<br>• The cart icon in the navigation will update<br>• You can continue shopping or go to checkout<br><br>To view your cart, click the shopping cart icon in the top navigation bar. From there, you can review items, update quantities, or proceed to checkout! 🎉</p></div>',
                    position: 'top'
                });
            }
            
            // Filter valid steps
            const validSteps = steps.filter(step => elementExists(step.element));
            
            if (validSteps.length === 0) return;
            
            const intro = introJs();
            intro.setOptions({
                steps: validSteps,
                showProgress: true,
                showBullets: true,
                exitOnOverlayClick: true,
                exitOnEsc: true,
                keyboardNavigation: true,
                disableInteraction: false,
                scrollToElement: true,
                scrollPadding: 20,
                nextLabel: 'Next →',
                prevLabel: '← Previous',
                skipLabel: 'Skip',
                doneLabel: 'Got it! 🎉',
                tooltipClass: 'customTooltip',
                highlightClass: 'customHighlight',
                buttonClass: 'introjs-button',
                tooltipPosition: 'auto' // Let intro.js decide the best position
            });
            
            // Ensure tooltip is visible after each step and style properly
            intro.onchange(function(targetElement) {
                setTimeout(function() {
                    const tooltip = document.querySelector('.introjs-tooltip');
                    if (tooltip) {
                        tooltip.style.display = 'block';
                        tooltip.style.visibility = 'visible';
                        tooltip.style.opacity = '1';
                        tooltip.style.zIndex = '999999';
                        
                        // Ensure header has gradient and white text
                        const header = tooltip.querySelector('.introjs-tooltip-header');
                        if (header) {
                            header.style.background = 'linear-gradient(135deg, #3b82f6 0%, #10b981 100%)';
                            header.style.color = 'white';
                            const headerText = header.querySelector('h3, h4');
                            if (headerText) {
                                headerText.style.color = 'white';
                            }
                        }
                        
                        // Ensure skip button is styled as button
                        const skipButton = tooltip.querySelector('.introjs-skipbutton');
                        if (skipButton) {
                            skipButton.style.color = 'white';
                            skipButton.style.background = 'rgba(255, 255, 255, 0.2)';
                            skipButton.style.border = '1px solid rgba(255, 255, 255, 0.3)';
                            skipButton.style.padding = '8px 16px';
                            skipButton.style.borderRadius = '8px';
                            skipButton.style.fontWeight = '500';
                        }
                        
                        // Also ensure content is visible
                        const content = tooltip.querySelector('.introjs-tooltipcontent');
                        if (content) {
                            content.style.display = 'block';
                            content.style.visibility = 'visible';
                            content.style.opacity = '1';
                        }
                    }
                }, 100);
            });
            
            // Debug: Log when tutorial starts
            intro.onstart(function() {
                console.log('Products tutorial started with', validSteps.length, 'steps');
            });
            
            intro.onexit(function() {
                console.log('Products tutorial exited');
            });
            
            setTimeout(() => intro.start(), 1000);
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initProductsTutorial);
        } else {
            setTimeout(initProductsTutorial, 100);
        }
    })();
    </script>
    <style>
    /* Professional Tutorial Styling - Matching First Image Style */
    .customTooltip {
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        max-width: 400px !important;
        padding: 0 !important;
        background: white !important;
        overflow: hidden !important;
    }

    /* Gradient Header like first image */
    .introjs-tooltip-header {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%) !important;
        padding: 20px 20px 16px 20px !important;
        border-bottom: none !important;
        color: white !important;
        position: relative !important;
    }

    .introjs-tooltip-header h3,
    .introjs-tooltip-header h4 {
        color: white !important;
        margin: 0 !important;
        font-weight: 600 !important;
    }

    .introjs-tooltipcontent {
        padding: 20px !important;
        font-size: 14px !important;
        line-height: 1.6 !important;
        color: #374151 !important;
        background: white !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .introjs-tooltipbuttons {
        padding: 16px 20px 20px 20px !important;
        border-top: 1px solid #e5e7eb !important;
        text-align: center !important;
        background: white !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .introjs-tooltip {
        z-index: 999999 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: absolute !important;
        max-width: 400px !important;
        min-width: 300px !important;
    }

    .introjs-tooltip * {
        visibility: visible !important;
    }

    .customHighlight {
        border-radius: 12px !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3) !important;
    }

    /* Skip button styled like Next/Previous buttons */
    .introjs-skipbutton {
        position: absolute !important;
        top: 12px !important;
        right: 12px !important;
        z-index: 10 !important;
        margin: 0 !important;
        color: white !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        transition: all 0.2s ease !important;
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        cursor: pointer !important;
        backdrop-filter: blur(4px) !important;
    }

    .introjs-skipbutton:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        border-color: rgba(255, 255, 255, 0.5) !important;
        transform: translateY(-1px) !important;
    }

    /* Button styling */
    .introjs-button {
        border-radius: 8px !important;
        padding: 10px 20px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        transition: all 0.2s ease !important;
        border: none !important;
        cursor: pointer !important;
    }

    .introjs-button.introjs-prevbutton {
        background: #f3f4f6 !important;
        color: #374151 !important;
        border: 1px solid #e5e7eb !important;
        flex: 1 !important;
        max-width: 48% !important;
    }

    .introjs-button.introjs-prevbutton:hover {
        background: #e5e7eb !important;
        border-color: #d1d5db !important;
    }

    .introjs-button.introjs-nextbutton {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white !important;
        border: none !important;
        flex: 1 !important;
        max-width: 48% !important;
    }

    .introjs-button.introjs-nextbutton:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;
    }
    </style>
    @endpush
@endif

@endsection 