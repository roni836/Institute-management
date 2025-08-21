<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Teachers</h1>
        <a href="{{ route('admin.teachers.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
           Add Teacher
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <input type="text" wire:model.debounce.400ms="search"
               placeholder="Search by name or email"
               class="border rounded px-3 py-2">
        <select wire:model="perPage" class="border rounded px-3 py-2">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        <div class="hidden md:block"></div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Name</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $t)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $t->name }}</td>
                        <td class="p-3">{{ $t->email }}</td>
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
