<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Dashboard' }} — Ahantra Edu</title>
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
                        Main Menu
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"/>
                            </svg>
                        </div>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.admissions.index') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        Admissions
                    </a>
                    <a href="{{ route('admin.payments.index') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        Payments
                    </a>
                    <a href="{{ route('admin.attendance.index') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Attendance
                    </a>
                    <a href="{{ route('admin.students.index') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Students
                    </a>
                    <div class="text-primary-200 text-xs uppercase tracking-wider font-semibold mb-4 px-3 pt-6">
                        Academic
                    </div>

                    <a href="{{ route('admin.exams.index') }}" @click="currentPage = 'admin.exams.index'"
                       :class="currentPage === 'admin.exams.index' || currentPage === 'admin.courses.create' || currentPage === 'admin.courses.edit' ? 'active' : ''"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        </div>
                        Exams
                    </a>
                    <a href="{{ route('admin.courses.index') }}" @click="currentPage = 'admin.courses.index'"
                       :class="currentPage === 'admin.courses.index' || currentPage === 'admin.courses.create' || currentPage === 'admin.courses.edit' ? 'active' : ''"

                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 006 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        Courses
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" @click="currentPage = 'admin.subjects.index'"
                       :class="currentPage === 'admin.subjects.index' || currentPage === 'admin.subjects.create' || currentPage === 'admin.subjects.edit' ? 'active' : ''"

                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 006 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        Subjects
                    </a>
                    <a href="{{ route('admin.batches.index') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        Batches
                    </a>
                    <a href="{{ route('admin.teachers.index') }}"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        Teachers
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
                                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                                    <p class="text-gray-600 text-sm font-medium">Welcome back to your admin panel</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5z"/>
                                    </svg>
                                </button>
                                <div class="flex items-center space-x-3">
                                    <div class="text-right hidden sm:block">
                                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Admin User' }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@antraims.com' }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold">{{ substr(auth()->user()->name ?? 'Admin User', 0, 2) }}</span>
                                    </div>
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