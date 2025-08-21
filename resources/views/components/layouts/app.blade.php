<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Admin' }} — IMS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full bg-gray-50" x-data="{ open: false }">
    <div class="min-h-screen flex">
        <!-- Content -->
        <main class="flex-1">
            <header >
                @livewire('public.header')
            </header>
            <div class="p-4">
                {{ $slot }}
            </div>
            <footer>
                @livewire('public.footer')
            </footer>
        </main>
    </div>
    @livewireScripts

</body>

</html>
