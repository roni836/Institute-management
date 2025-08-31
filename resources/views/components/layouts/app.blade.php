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
            <div class="p-4">
                {{ $slot }}
            </div>
        </main>
    </div>
    @livewireScripts

</body>
<script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    colors: {
                       primary: {
                        50:  '#fef0e6',
                        100: '#fde1ce',
                        200: '#fbb384',
                        300: '#f8863a',
                        400: '#de5d08',
                        500: '#ac4806',
                        600: '#f88437', // main
                        700: '#7b3404',
                        800: '#592503',
                        900: '#3b1902',
                        }
                    }
                }
            }
        }
    </script>
</html>
