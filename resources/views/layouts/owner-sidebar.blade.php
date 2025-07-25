<div class="w-64 h-screen bg-white shadow-lg flex flex-col">
    <div class="p-6 font-bold text-xl border-b">Owner Panel</div>
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-200">Dashboard</a>
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 rounded hover:bg-gray-200">Profile</a>
        <a href="{{ route('owner.bookings') }}" class="block px-4 py-2 rounded hover:bg-gray-200">Bookings</a>
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
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="p-4 border-t">
        @csrf
        <button type="submit" class="w-full text-left px-4 py-2 rounded hover:bg-gray-200">Log Out</button>
    </form>
</div> 