<div >
    <div class="mb-3 grid md:grid-cols-3 gap-2">
        <input type="text" placeholder="Search name / code"
               class="border rounded-lg p-2 w-full"
               wire:model.debounce.400ms="q">

        <div class="md:col-start-3 justify-self-end">
            <a href="{{ route('admin.courses.create') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg bg-black text-white">
                + New Course
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
                <th class="text-left p-3">Name</th>
                <th class="text-left p-3">Code</th>
                <th class="text-left p-3">Duration</th>
                <th class="text-left p-3">Fees</th>
                <th class="text-right p-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($courses as $c)
                <tr class="border-t">
                    <td class="p-3">{{ $c->name }}</td>
                    <td class="p-3">{{ $c->batch_code ?? '—' }}</td>
                    <td class="p-3">{{ $c->duration_months ? $c->duration_months.' mo' : '—' }}</td>
                    <td class="p-3">
                        ₹{{ number_format($c->gross_fee,2) }}
                        @if($c->discount > 0)
                            <span class="text-gray-500 text-xs">(-₹{{ number_format($c->discount,2) }})</span>
                        @endif
                        <span class="ml-1 font-medium">= ₹{{ number_format($c->net_fee,2) }}</span>
                    </td>
                    <td class="p-3 text-right space-x-2">
                        <a class="px-2 py-1 rounded bg-gray-100"
                           href="{{ route('admin.courses.edit', $c->id) }}">Edit</a>

                        <button class="px-2 py-1 rounded bg-red-50 text-red-700"
                                x-data
                                x-on:click.prevent="if (confirm('Delete this course?')) { $wire.delete({{ $c->id }}) }">
                            Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td class="p-3" colspan="5">No courses found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $courses->links() }}</div>
</div>
