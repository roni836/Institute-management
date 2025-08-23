<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Dashboard' }} — Antra IMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
<body class="h-full bg-gray-50 font-poppins" x-data="{ open: false, currentPage: @json(Route::currentRouteName()) }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="hidden lg:flex flex-col w-72 bg-primary-700 text-white h-screen sticky top-0">
            <div class="p-6 border-b border-primary-500">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">Antra IMS</h1>
                        <p class="text-primary-200 text-sm font-medium">Admin Portal</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <div class="text-primary-200 text-xs uppercase tracking-wider font-semibold mb-4 px-3">
                    Main Menu
                </div>
                <a href="{{ route('admin.dashboard') }}" @click="currentPage = 'admin.dashboard'" 
                   :class="currentPage === 'admin.dashboard' ? 'active' : ''"
                   class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                    <div class="w-5 h-5 mr-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"/>
                        </svg>
                    </div>
                    Dashboard
                </a>
                <a href="{{ route('admin.admissions.index') }}" @click="currentPage = 'admin.admissions.index'"
                   :class="currentPage === 'admin.admissions.index' || currentPage === 'admin.admissions.create' || currentPage === 'admin.admissions.edit' || currentPage === 'admin.admissions.show' ? 'active' : ''"
                   class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                    <div class="w-5 h-5 mr-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    Admissions
                </a>
                <a href="{{ route('admin.payments.index') }}" @click="currentPage = 'admin.payments.index'"
                   :class="currentPage === 'admin.payments.index' || currentPage === 'admin.payments.create' || currentPage === 'admin.due-payments.index' || currentPage === 'admin.payments.receipt' ? 'active' : ''"
                   class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                    <div class="w-5 h-5 mr-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    Payments
                </a>
                <a href="{{ route('admin.students.index') }}" @click="currentPage = 'admin.students.index'"
                   :class="currentPage === 'admin.students.index' || currentPage === 'student.profile' ? 'active' : ''"
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
                <a href="{{ route('admin.batches.index') }}" @click="currentPage = 'admin.batches.index'"
                   :class="currentPage === 'admin.batches.index' || currentPage === 'admin.batches.create' || currentPage === 'admin.batches.edit' ? 'active' : ''"
                   class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                    <div class="w-5 h-5 mr-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    Batches
                </a>
                <a href="{{ route('admin.teachers.index') }}" @click="currentPage = 'admin.teachers.index'"
                   :class="currentPage === 'admin.teachers.index' || currentPage === 'admin.teachers.create' ? 'active' : ''"
                   class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                    <div class="w-5 h-5 mr-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    Teachers
                </a>
            </nav>
            <div class="p-4 border-t border-primary-500">
                <div class="flex items-center space-x-3 p-3 rounded-xl bg-primary-500">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">{{ substr(auth()->user()->name ?? 'Admin User', 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-xs text-primary-200">Super Admin</p>
                    </div>
                    <a href="{{ route('logout') }}" class="text-primary-200 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Mobile offcanvas -->
        <div class="lg:hidden" x-show="open" x-transition @click.self="open=false">
            <div class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>
            <aside class="fixed inset-y-0 left-0 w-72 bg-primary-600 text-white h-screen z-50">
                <div class="flex items-center justify-between p-6 border-b border-primary-500">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold tracking-tight">Antra IMS</h1>
                            <p class="text-primary-200 text-sm font-medium">Admin Portal</p>
                        </div>
                    </div>
                    <button @click="open=false" class="p-2 text-white hover:bg-primary-700 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <div class="text-primary-200 text-xs uppercase tracking-wider font-semibold mb-4 px-3">
                        Main Menu
                    </div>
                    <a href="{{ route('admin.dashboard') }}" @click="currentPage = 'admin.dashboard'; open = false" 
                       :class="currentPage === 'admin.dashboard' ? 'active' : ''"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"/>
                            </svg>
                        </div>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.admissions.index') }}" @click="currentPage = 'admin.admissions.index'; open = false"
                       :class="currentPage === 'admin.admissions.index' || currentPage === 'admin.admissions.create' || currentPage === 'admin.admissions.edit' || currentPage === 'admin.admissions.show' ? 'active' : ''"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        Admissions
                    </a>
                    <a href="{{ route('admin.payments.index') }}" @click="currentPage = 'admin.payments.index'; open = false"
                       :class="currentPage === 'admin.payments.index' || currentPage === 'admin.payments.create' || currentPage === 'admin.due-payments.index' || currentPage === 'admin.payments.receipt' ? 'active' : ''"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        Payments
                    </a>
                    <a href="{{ route('admin.students.index') }}" @click="currentPage = 'admin.students.index'; open = false"
                       :class="currentPage === 'admin.students.index' || currentPage === 'student.profile' ? 'active' : ''"
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
                    <a href="{{ route('admin.courses.index') }}" @click="currentPage = 'admin.courses.index'; open = false"
                       :class="currentPage === 'admin.courses.index' || currentPage === 'admin.courses.create' || currentPage === 'admin.courses.edit' ? 'active' : ''"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 006 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        Courses
                    </a>
                    <a href="{{ route('admin.batches.index') }}" @click="currentPage = 'admin.batches.index'; open = false"
                       :class="currentPage === 'admin.batches.index' || currentPage === 'admin.batches.create' || currentPage === 'admin.batches.edit' ? 'active' : ''"
                       class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="w-5 h-5 mr-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24  circumstantial:round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    Batches
                </a>
                <a href="{{ route('admin.teachers.index') }}" @click="currentPage = 'admin.teachers.index'; open = false"
                   :class="currentPage === 'admin.teachers.index' || currentPage === 'admin.teachers.create' ? 'active' : ''"
                   class="nav-item flex items-center px-4 py-3 rounded-xl text-sm font-medium">
                    <div class="w-5 h-5 mr-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    Teachers
                </a>
            </nav>
        </aside>
    </div>

        <!-- Main Content -->
        <main class="flex-1 min-h-screen">
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
                                <h1 class="text-2xl font-bold text-gray-900" x-text="currentPage === 'admin.dashboard' ? 'Dashboard' : 
                                    currentPage === 'admin.admissions.index' || currentPage === 'admin.admissions.create' || currentPage === 'admin.admissions.edit' || currentPage === 'admin.admissions.show' ? 'Admissions' :
                                    currentPage === 'admin.payments.index' || currentPage === 'admin.payments.create' || currentPage === 'admin.due-payments.index' || currentPage === 'admin.payments.receipt' ? 'Payments' :
                                    currentPage === 'admin.students.index' || currentPage === 'student.profile' ? 'Students' :
                                    currentPage === 'admin.courses.index' || currentPage === 'admin.courses.create' || currentPage === 'admin.courses.edit' ? 'Courses' :
                                    currentPage === 'admin.batches.index' || currentPage === 'admin.batches.create' || currentPage === 'admin.batches.edit' ? 'Batches' :
                                    currentPage === 'admin.teachers.index' || currentPage === 'admin.teachers.create' ? 'Teachers' : 'Dashboard'">Dashboard</h1>
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
            <div class="p-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    @vite(['resources/js/app.js'])
    @livewireStyles
    @livewireScripts
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>