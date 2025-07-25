<div class="w-56 h-screen bg-white shadow-lg flex flex-col">
    <div class="p-6 font-bold text-2xl border-b text-green-700 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        SmashZone
    </div>
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 rounded hover:bg-green-50 flex items-center gap-2 {{ request()->routeIs('profile.*') ? 'bg-green-100 text-green-700 font-bold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            Profile
        </a>
        <a href="{{ route('bookings.index') }}" class="block px-4 py-2 rounded hover:bg-green-50 flex items-center gap-2 {{ request()->routeIs('bookings.*') ? 'bg-green-100 text-green-700 font-bold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            Booking
        </a>
        <a href="{{ route('courts.index') }}" class="block px-4 py-2 rounded hover:bg-green-50 flex items-center gap-2 {{ request()->routeIs('courts.*') ? 'bg-green-100 text-green-700 font-bold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4" /></svg>
            Courts
        </a>
        <a href="{{ route('products.index') }}" class="block px-4 py-2 rounded hover:bg-green-50 flex items-center gap-2 {{ request()->routeIs('products.*') ? 'bg-green-100 text-green-700 font-bold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h1l2 9h12l2-9h1" /></svg>
            Products
        </a>
        <a href="{{ route('payments.index') }}" class="block px-4 py-2 rounded hover:bg-green-50 flex items-center gap-2 {{ request()->routeIs('payments.*') ? 'bg-green-100 text-green-700 font-bold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2v-7a2 2 0 00-2-2z" /></svg>
            Payments
        </a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-green-50 flex items-center gap-2 opacity-50 cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2M9 17H7a4 4 0 01-4-4V7a4 4 0 014-4h10a4 4 0 014 4v6a4 4 0 01-4 4h-2M9 17v2a4 4 0 004 4h2a4 4 0 004-4v-2" /></svg>
            Report
        </a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="p-4 border-t">
        @csrf
        <button type="submit" class="w-full text-left px-4 py-2 rounded hover:bg-green-50 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" /></svg>
            Log Out
        </button>
    </form>
</div> 