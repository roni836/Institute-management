<div>
    <div class="mb-3 grid md:grid-cols-3 gap-2">
        <input type="text" placeholder="Search batch / course"
               class="border rounded-lg p-2 w-full"
               wire:model.debounce.400ms="q">

        <div class="md:col-start-3 justify-self-end">
            <a href="{{ route('admin.batches.create') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg bg-black text-white">
                + New Batch
            </a>
        </div>
    </div>

    @if (session('ok'))
        <div class="mb-3 p-2 rounded bg-green-50 border text-green-800">{{ session('ok') }}</div>
    @endif

    <div class="overflow-x-auto bg-white border rounded-xl">
        <table class="min-w-full text-sm">
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
                        {{ $b->start_date }} →
                        {{ $b->end_date }}
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

    <div class="mt-3">{{ $batches->links() }}</div>
</div>
