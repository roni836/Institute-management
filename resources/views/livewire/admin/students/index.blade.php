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
        <x-stat-card title="Total Students" :value="$stats['total']" icon="users" color="orange"/>
        <x-stat-card title="Active Students" :value="$stats['active']" icon="user-check" color="green"/>
        <x-stat-card title="Completed" :value="$stats['completed']" icon="user-graduate" color="blue"/>
        <x-stat-card title="This Month" :value="$stats['thisMonth']" icon="calendar" color="orange"/>
    </div>

    <div class="mb-3 grid md:grid-cols-4 gap-2">
        <input type="text" placeholder="Search name/email/phone" class="input input-bordered w-full border rounded-lg p-2"
               wire:model.debounce.400ms="q">

        <select class="border rounded-lg p-2" wire:model="status">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="alumni">Alumni</option>
        </select>

        <select class="border rounded-lg p-2" wire:model="batchId">
            <option value="">All Batches</option>
            @foreach($batches as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
        </select>
    </div>

    @if (session('ok'))
        <div class="mb-3 p-2 rounded bg-green-50 border text-green-800">{{ session('ok') }}</div>
    @endif

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
                        <td class="p-3">{{ ucfirst($s->status) }}</td>
                        <td class="p-3">
                            <a href="{{ route('student.profile', $s->id) }}" 
                            class="text-blue-600 hover:underline">
                            View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3" colspan="4">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $students->links() }}
    </div>
</div>
