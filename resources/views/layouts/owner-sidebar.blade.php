<div class="w-64 h-screen bg-white shadow-lg flex flex-col">
    <div class="p-6 font-bold text-xl border-b">Owner Panel</div>
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-200">Dashboard</a>
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 rounded hover:bg-gray-200">Profile</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200">Booking</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200">Product</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200">Payment</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200">Report</a>
        <a href="{{ route('staff.index') }}" class="block px-4 py-2 rounded hover:bg-gray-200">Team Management</a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="p-4 border-t">
        @csrf
        <button type="submit" class="w-full text-left px-4 py-2 rounded hover:bg-gray-200">Log Out</button>
    </form>
</div> 