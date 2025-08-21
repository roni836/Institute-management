<div>
    <div class="mb-3 grid md:grid-cols-4 gap-2">
        <input type="text" placeholder="Search name/email/phone/roll/uid"
            class="input input-bordered w-full border rounded-lg p-2" wire:model.debounce.400ms="q">

        <select class="border rounded-lg p-2" wire:model="status">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="alumni">Alumni</option>
        </select>

        <select class="border rounded-lg p-2" wire:model="batchId">
            <option value="">All Batches</option>
            @foreach ($batches as $b)
                <option value="{{ $b->id }}">
                    {{ $b->batch_name }} — {{ $b->course->name }}
                </option>
            @endforeach
        </select>

        <a href="{{ route('admin.admissions.create') }}"
            class="justify-self-end inline-flex items-center px-3 py-2 rounded-lg bg-black text-white">
            + New Admission
        </a>

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
