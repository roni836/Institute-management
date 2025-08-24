<div class="p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Subjects</h1>
            <p class="text-gray-600">Manage academic subjects</p>
        </div>
        <a href="{{ route('admin.subjects.create') }}"
            class="px-4 py-2 bg-orange-500 text-white rounded-lg flex items-center gap-2">
            <span>+</span> Add Subject
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 md:p-6 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Total subjects</p>
                    <p class="text-2xl font-semibold">50</p>
                </div>
                <div class="text-orange-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Active subjects</p>
                    <p class="text-2xl font-semibold text-green-600">10</p>
                </div>
                <div class="text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>


        <div class="bg-white p-4 md:p-6 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Total Enrolled</p>
                    <p class="text-2xl font-semibold text-orange-500">45</p>
                </div>
                <div class="text-orange-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="q" class="w-full pl-10 pr-4 py-2 border rounded-lg"
                placeholder="Search subjects by name or code">
        </div>

        <select wire:model.live="statusFilter" class="border rounded-lg px-4 py-2">
            <option value="">All Status</option>
            <option value="Active">Active</option>
            <option value="Upcoming">Upcoming</option>
        </select>

        <select wire:model.live="sortField" class="border rounded-lg px-4 py-2">
            <option value="">Sort By</option>
            <option value="name">Name</option>
            <option value="students_count">Students</option>
            <option value="created_at">Date Created</option>
        </select>
    </div>

    <!-- Loading indicator -->
    <div wire:loading.delay class="w-full text-center py-4">
        <div
            class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-orange-500 border-r-transparent">
        </div>
    </div>

    <!-- Course cards with wire:loading.remove -->
    <div wire:loading.remove class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        @forelse($subjects as $c)
            <div class="bg-white p-4 md:p-6 rounded-xl border">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-4">
                    <div>
                        <h3 class="text-xl font-semibold">{{ $c->name }}</h3>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full text-sm {{ $c->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $c->status ?? 'Active' }}
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ $c->students_count ?? '45' }} students</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-4">
                    <a href="{{ route('admin.subjects.view', $c->id) }}"
                        class="flex-1 text-center py-2 border rounded-lg hover:bg-gray-50">
                        View Details
                    </a>
                    <a href="{{ route('admin.subjects.edit', $c->id) }}"
                        class="flex-1 text-center py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                        Manage Subjects
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-12 bg-white rounded-xl border">
                <p class="text-gray-500">No subjects found</p>
            </div>
        @endforelse
    </div>

    <h2 wire:loading.target="q" wire:loading>Searching....</h2>

</div>
