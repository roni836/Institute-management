<div class="p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Courses</h1>
            <p class="text-gray-600">Manage academic courses and curriculum</p>
        </div>
        <a href="{{ route('admin.courses.create') }}"
           class="px-4 py-2 bg-orange-500 text-white rounded-lg flex items-center gap-2">
            <span>+</span> Create Course
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 md:p-6 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Total Courses</p>
                    <p class="text-2xl font-semibold">{{ $totalCourses }}</p>
                </div>
                <div class="text-orange-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Active Courses</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $activeCourses }}</p>
                </div>
                <div class="text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Upcoming</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $upcomingCourses }}</p>
                </div>
                <div class="text-blue-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Total Enrolled</p>
                    <p class="text-2xl font-semibold text-orange-500">{{ $totalEnrolled }}</p>
                </div>
                <div class="text-orange-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" wire:model.debounce.400ms="q" 
                   class="w-full pl-10 pr-4 py-2 border rounded-lg" 
                   placeholder="Search Courses">
        </div>
        <button class="px-4 py-2 border rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filter by Status
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        @forelse($courses as $c)
            <div class="bg-white p-4 md:p-6 rounded-xl border">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-4">
                    <div>
                        <h3 class="text-xl font-semibold">{{ $c->name }}</h3>
                        <p class="text-gray-600">{{ $c->description ?? 'Complete course covering essential topics' }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm {{ $c->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $c->status ?? 'Active' }}
                    </span>
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $c->duration_months }} months</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>{{ $c->students_count ?? '45' }} students</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <p class="text-gray-600">Instructor</p>
                        <p class="font-medium">{{ $c->instructor_name ?? 'Prof. Rahul Kumar' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Course Fee</p>
                        <p class="font-medium text-orange-500">₹{{ number_format($c->net_fee) }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-4">
                    <a href="{{ route('admin.courses.show', $c->id) }}" 
                       class="flex-1 text-center py-2 border rounded-lg hover:bg-gray-50">
                        View Details
                    </a>
                    <a href="{{ route('admin.courses.edit', $c->id) }}" 
                       class="flex-1 text-center py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                        Manage Course
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-12 bg-white rounded-xl border">
                <p class="text-gray-500">No courses found</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
</div>
