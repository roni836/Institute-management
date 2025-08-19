<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white min-h-screen">
        <div class="p-4 text-2xl font-bold">Admin Panel</div>
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-gray-700">Dashboard</a>
            <a href="{{ route('admin.users') }}" class="block px-4 py-2 hover:bg-gray-700">Users</a>
            <a href="{{ route('admin.payments') }}" class="block px-4 py-2 hover:bg-gray-700">Payments</a>
            <a href="{{ route('admin.reports') }}" class="block px-4 py-2 hover:bg-gray-700">Reports</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        {{ $slot }}
    </main>
</div>
