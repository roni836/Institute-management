<div class="p-4 md:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Batches</h1>
            <p class="text-gray-600">Manage course batches and schedules</p>
        </div>
        <a href="{{ route('admin.batches.create') }}"
           class="px-4 py-2 bg-orange-500 text-white rounded-lg flex items-center gap-2">
            <span>+</span> Schedule Batch
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Total Batches</p>
                    <p class="text-2xl font-semibold">{{ $totalBatches }}</p>
                </div>
                <div class="p-2 bg-orange-100 text-orange-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Running</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $runningBatches }}</p>
                </div>
                <div class="p-2 bg-green-100 text-green-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Upcoming</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $upcomingBatches }}</p>
                </div>
                <div class="p-2 bg-blue-100 text-blue-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Completed</p>
                    <p class="text-2xl font-semibold text-gray-600">{{ $completedBatches }}</p>
                </div>
                <div class="p-2 bg-gray-100 text-gray-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                <input type="text" wire:model.live.debounce.400ms="q" 
                       class="w-full pl-10 pr-4 py-2 border rounded-lg" 
                       placeholder="Search Batches">
            </div>

            <select wire:model.live="courseFilter" class="border rounded-lg px-4 py-2">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="border rounded-lg px-4 py-2">
                <option value="">All Status</option>
                <option value="Upcoming">Upcoming</option>
                <option value="Running">Running</option>
                <option value="Completed">Completed</option>
            </select>
        </div>
    </div>

    <div wire:loading.remove  class="overflow-x-auto bg-white border rounded-xl">
        <div class="min-w-full inline-block align-middle">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-3">Batch</th>
                        <th class="text-left p-3">Course</th>
                        <th class="text-left p-3">Dates</th>
                        <th class="text-right p-3">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($batches as $b)
                        <tr class="border-t">
                            <td class="p-3">{{ $b->batch_name }}</td>
                            <td class="p-3">{{ $b->course->name }}</td>
                            <td class="p-3">
                                {{ \Carbon\Carbon::parse($b->start_date)->format('d M Y') }} →
                                {{ \Carbon\Carbon::parse($b->end_date)->format('d M Y') }}
                            </td>
                            <td class="p-3 text-right space-x-2">
                                <a class="px-2 py-1 rounded bg-gray-100"
                                   href="{{ route('admin.batches.edit', $b->id) }}">Edit</a>

                                <button class="px-2 py-1 rounded bg-red-50 text-red-700"
                                        x-data
                                        x-on:click.prevent="if (confirm('Delete this batch?')) { $wire.delete({{ $b->id }}) }">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-3" colspan="4">No batches found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <h2 wire:loading.target="q" wire:loading>Searching....</h2>

    <div class="mt-4 md:mt-6">
        {{ $batches->links() }}
    </div>
</div>
