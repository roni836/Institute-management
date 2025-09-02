<div class="p-4 md:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Teachers</h1>
            <p class="text-gray-600">Manage faculty and teaching staff</p>
        </div>
        <a href="{{ route('admin.teachers.create') }}"
           class="px-4 py-2 bg-orange-500 text-white rounded-lg flex items-center gap-2">
            <span>+</span> Add Teacher
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Total Teachers</p>
                    <p class="text-2xl font-semibold">{{ $totalTeachers }}</p>
                </div>
                <div class="p-2 bg-orange-100 text-orange-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Active Teachers</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $activeTeachers }}</p>
                </div>
                <div class="p-2 bg-green-100 text-green-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Courses Taught</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $coursesTaught }}</p>
                </div>
                <div class="p-2 bg-blue-100 text-blue-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Students Taught</p>
                    <p class="text-2xl font-semibold text-orange-500">{{ $studentsTaught }}</p>
                </div>
                <div class="p-2 bg-orange-100 text-orange-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 md:p-6 rounded-xl border mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.400ms="search" 
                       class="w-full pl-10 pr-4 py-2 border rounded-lg" 
                       placeholder="Search Teachers">
            </div>
            <select wire:model="perPage" class="border rounded-lg px-4 py-2">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Name</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Phone no.</th>
                    <th class="text-left p-3">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $t)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $t->name }}</td>
                        <td class="p-3">{{ $t->email }}</td>
                        <td class="p-3">{{ $t->phone }}</td>
                        <td class="p-3 text-gray-600 text-sm">{{ $t->created_at?->format('d-M-Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-6 text-center text-gray-500" colspan="3">No teachers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $teachers->links() }}</div>
</div>
