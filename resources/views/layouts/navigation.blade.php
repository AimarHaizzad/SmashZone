<nav class="bg-white shadow-sm px-6 py-3 flex items-center justify-between w-full z-30">
    <!-- Left: Logo and Links -->
    <div class="flex items-center gap-8">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
            <!-- SVG Badminton Logo -->
            <span class="inline-block"><svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="36" height="36" rx="8" fill="#e0f2fe"/><path d="M10 24l8-14 8 14M12 20h12" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="18" cy="28" r="2.5" fill="#22c55e"/></svg></span>
            <span class="text-2xl font-extrabold text-blue-700 tracking-wide group-hover:text-green-600 transition">SmashZone</span>
        </a>
        <div class="hidden md:flex items-center gap-6 ml-8">
            <a href="{{ route('dashboard') }}" class="text-base font-semibold {{ request()->routeIs('dashboard') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-700 hover:text-blue-700' }} transition pb-1">Dashboard</a>
            <a href="{{ route('courts.index') }}" class="text-base font-semibold {{ request()->routeIs('courts.*') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-700 hover:text-blue-700' }} transition pb-1">Courts</a>
            <a href="{{ route('bookings.index') }}" class="text-base font-semibold {{ request()->routeIs('bookings.*') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-700 hover:text-blue-700' }} transition pb-1">Bookings</a>
            <a href="{{ route('products.index') }}" class="text-base font-semibold {{ request()->routeIs('products.*') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-700 hover:text-blue-700' }} transition pb-1">Shop</a>
            <a href="{{ route('bookings.index') }}" class="text-base font-semibold text-gray-700 hover:text-blue-700 transition pb-1">My Bookings</a>
        </div>
    </div>
    <!-- Right: Icons and User -->
    <div class="flex items-center gap-4">
        <button class="hover:bg-blue-50 p-2 rounded-full transition"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg></button>
        <button class="hover:bg-blue-50 p-2 rounded-full transition"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg></button>
        <!-- User Dropdown -->
        <div class="relative group">
            <button class="hover:bg-blue-50 p-2 rounded-full transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span class="hidden md:inline text-gray-700 font-semibold">{{ Auth::user()->name ?? 'Account' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-lg shadow-lg py-2 z-50 hidden group-hover:block">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-blue-50">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</nav>
