<div class="p-4 md:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Admissions</h1>
            <p class="text-gray-600">Manage student applications and admissions</p>
        </div>
        <a href="{{ route('admin.admissions.create') }}"
           class="px-4 py-2 bg-orange-500 text-white rounded-lg flex items-center gap-2">
            <span>+</span> New Admission
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Applications -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Total Applications</p>
                    <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-orange-100 text-orange-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Statistics cards for Pending, Approved, Rejected -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Pending Review</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ $stats['pendingReview'] }}</p>
                </div>
                <div class="p-2 bg-yellow-100 text-yellow-500 rounded-lg">23</div>
            </div>
        </div>

        <!-- ...similar cards for Approved and Rejected... -->
    </div>

    <div class="bg-white p-6 rounded-xl border mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search and filters -->
            <div class="relative flex-1">
                <input type="text" wire:model.debounce.400ms="q" 
                       class="w-full pl-10 pr-4 py-2 border rounded-lg" 
                       placeholder="Search by name, email, or phone">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
            </div>
            
            <!-- Status and Batch filters -->
            <select wire:model="status" class="border rounded-lg px-4 py-2">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <select wire:model="batchId" class="border rounded-lg px-4 py-2">
                <option value="">All Batches</option>
                @foreach($batches as $b)
                    <option value="{{ $b->id }}">{{ $b->batch_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded-xl">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Name</th>
                    <th class="text-left p-3">Email / Phone</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-right p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $s)
                    <tr class="border-t">
                        <td class="p-3">{{ $s->name }}</td>
                        <td class="p-3">{{ $s->email ?? '—' }}<br>{{ $s->phone ?? '—' }}</td>
                        <td class="p-3">{{ ucfirst($s->status ?? 'active') }}</td>
                        <td class="p-3 text-right space-x-2">
                            <a class="px-2 py-1 rounded bg-gray-100"
                                href="{{ route('admin.admissions.edit', ['admission' => $s->id]) }}">
                                Edit
                            </a>

                            <a class="px-2 py-1 rounded bg-blue-50 text-blue-700"
                                href="{{ route('admin.admissions.show', ['admission' => $s->id]) }}">
                                View
                            </a>

                            <button x-data
                                x-on:click.prevent="if (confirm('Delete this student?')) { $wire.delete({{ $s->id }}) }"
                                class="px-2 py-1 rounded bg-red-50 text-red-700">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3" colspan="4">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $students->links() }}
    </div>
</div>
