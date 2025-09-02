<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Teacher Dashboard' }} — Ahantra Edu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" 
    rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
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
    @livewireStyles

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .nav-item {
            transition: all 0.2s ease;
        }
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }
        .nav-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-right: 3px solid #fef0e6;
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Fixed Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r shadow-sm overflow-y-auto">
            <div class="p-4 space-y-4">
                <!-- Logo -->
                <div class="flex justify-center items-center mb-4">
                    <div class="w-28 h-28 flex items-center justify-center">
                        <img src="{{ asset('logo.png') }}" alt="" class="w-full object-contain">
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="space-y-1">
                    <div class="text-primary-200 text-xs uppercase tracking-wider font-semibold mb-4 px-3">
                        Teacher Menu
                    </div>
                    <a href="{{ route('teacher.dashboard') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"/>
                            </svg>
                        </div>
                        Dashboard
                    </a>
                    
                    <div class="text-primary-200 text-xs uppercase tracking-wider font-semibold mb-4 px-3 pt-6">
                        Academic
                    </div>

                    <a href="{{ route('teacher.exams.index') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        </div>
                        Exams
                    </a>
                    
                    <a href="{{ route('teacher.attendance.index') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Attendance
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content (Scrollable) -->
        <main class="flex-1 ml-64"> <!-- Added margin-left to match sidebar width -->
            <div class="min-h-screen bg-gray-50">
                <!-- Header -->
                <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button @click="open = true" 
                                        class="lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                </button>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">Teacher Dashboard</h1>
                                    <p class="text-gray-600 text-sm font-medium">Welcome back, {{ Auth::user()->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-3">
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                                    </div>
                                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <form method="POST" action="{{ route('teacher.logout') }}" class="ml-2">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors" title="Logout">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="px-3 py-2">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
