<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Admin' }} — IMS</title>
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full bg-gray-50" x-data="{ open: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="bg-white w-72 border-r hidden md:block">
            <div class="p-4 font-semibold text-xl">IMS Admin</div>
            <nav class="px-2 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100">Dashboard</a>
                <a href="{{ route('admin.admissions.index') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100">Admissions</a>
                <a href="{{ route('admin.students.index') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100">Students</a>
                <a href="{{ route('admin.courses.index') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100">Courses</a>
                <a href="{{ route('admin.batches.index') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100">Batches</a>
            </nav>
        </aside>

        <!-- Mobile offcanvas -->
        <div class="md:hidden" x-show="open" x-transition @click.self="open=false">
            <div class="fixed inset-0 bg-black/40"></div>
            <aside class="fixed inset-y-0 left-0 w-72 bg-white shadow-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-semibold text-lg">IMS Admin</div>
                    <button @click="open=false" class="p-2">✕</button>
                </div>
                <nav class="space-y-1 text-sm">
                    <a href="{{ route('admin.dashboard') }}"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100">Dashboard</a>
                    <a href="{{ route('admin.students.index') }}"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100">Students</a>
                    <a href="{{ route('admin.admissions.index') }}"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100">Admissions</a>
                    <a href="{{ route('admin.payments.index') }}"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100">Payments</a>
                    <a href="{{ route('admin.courses.index') }}"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100">Courses</a>
                    <a href="{{ route('admin.batches.index') }}"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100">Batches</a>
                </nav>
            </aside>
        </div>

        <!-- Content -->
        <main class="flex-1">
            <header class="bg-white border-b p-3 flex items-center justify-between sticky top-0">
                <div class="flex items-center gap-2">
                    <button class="md:hidden inline-flex p-2 rounded-lg border" @click="open=true">☰</button>
                    <h1 class="font-semibold">Welcome</h1>
                </div>
                <div class="text-sm">
                    {{ auth()->user()->name ?? 'User' }}
                </div>
            </header>
            <div class="p-4">
                {{ $slot }}
            </div>
        </main>
    </div>
    @livewireScripts

</body>

</html>
