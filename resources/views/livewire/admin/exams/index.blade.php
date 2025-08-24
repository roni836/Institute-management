<div>
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Exams</h1>
        <a href=""
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
           New Exam
        </a>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <input type="text" wire:model.debounce.400ms="search" placeholder="Search by exam name or batch"
               class="border rounded px-3 py-2">

        <select wire:model="batch_id" class="border rounded px-3 py-2">
            <option value="">All Batches</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
            @endforeach
        </select>

        <select wire:model="perPage" class="border rounded px-3 py-2">
            <option>10</option>
            <option>15</option>
            <option>25</option>
            <option>50</option>
        </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Exam Name</th>
                    <th class="p-3 text-left">Batch</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                    <tr class="border-t">
                        <td class="p-3">{{ $exam->exam_date }}</td>
                        <td class="p-3 font-medium">{{ $exam->name }}</td>
                        <td class="p-3">{{ $exam->batch?->batch_name }}</td>
                        <td class="p-3 no-print">
                            <a href="{{ route('admin.exams.show', $exam->id) }}"
                               class="px-3 py-1 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700">
                               View Students
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-6 text-center text-gray-500" colspan="4">
                            No exams found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $exams->links() }}</div>
</div>

</div>
