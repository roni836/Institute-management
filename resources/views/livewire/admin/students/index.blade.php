<div class="p-4 md:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Students</h1>
            <p class="text-gray-600">Manage enrolled students and their progress</p>
        </div>
        {{-- <a href="" class="px-4 py-2 bg-orange-500 text-white rounded-lg flex items-center gap-2">
            <span>+</span> Add Student
        </a> --}}
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card title="Total Students" :value="$stats['total']" icon="users" color="orange" />
        <x-stat-card title="Active Students" :value="$stats['active']" icon="user-check" color="green" />
        <x-stat-card title="Completed" :value="$stats['completed']" icon="user-graduate" color="blue" />
        <x-stat-card title="This Month" :value="$stats['thisMonth']" icon="calendar" color="orange" />
    </div>

    <div class="bg-white p-6 rounded-xl border mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="q"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg" placeholder="Search by name, father name, enrollment ID, email, or phone">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
            </div>

            <!-- Status filter -->
            <select wire:model.live="status" class="border rounded-lg px-4 py-2">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="alumni">Alumni</option>
            </select>

            <!-- Batch filter -->
            <select wire:model.live="batchId" class="border rounded-lg px-4 py-2">
                <option value="">All Batches</option>
                @foreach ($batches as $b)
                    <option value="{{ $b->id }}">{{ $b->batch_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if (session('ok'))
        <div class="mb-3 p-2 rounded bg-green-50 border text-green-800">{{ session('ok') }}</div>
    @endif

    <div class="overflow-x-auto bg-white border rounded-xl">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">S.no</th>
                    <th class="text-left p-3">Name</th>
                    <th class="text-left p-3">Father's name</th>
                    <th class="text-left p-3">Entrollement Id</th>
                    <th class="text-left p-3">Email / Phone</th>
                    {{-- <th class="text-left p-3">Status</th> --}}
                    <th class=" p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $i=>$s)
                    <tr class="border-t">
                        <td class="p-3">{{ $i + 1 }}.</td>
                        <td class="p-3">{{ $s->name }}</td>
                        <td class="p-3">{{ $s->father_name }}</td>
                        <td class="p-3">
                            <div class="font-medium">{{ $s->enrollment_id }}</div>
                        </td>
                        <td class="p-3">{{ $s->email ?? '—' }}<br>{{ $s->phone ?? '—' }}</td>
                        {{-- <td class="p-3">{{ ucfirst($s->status) }}</td> --}}
                        <td class=" text-center p-3 space-x-2">
                            {{-- <a href="{{ route('student.edit', $s->id) }}"
                                class="text-green-600  bg-green-50 hover:bg-green-100 px-2 py-1 rounded">
                                Edit
                            </a> --}}
                            <a href="{{ route('student.profile', $s->id) }}"
                                class="text-blue-600  bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded">
                                View
                            </a>
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
