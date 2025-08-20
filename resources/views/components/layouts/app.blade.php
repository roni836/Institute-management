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
