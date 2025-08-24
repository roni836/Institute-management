<div title="View Course" class="bg-white border rounded-xl p-6 max-w-3xl">
    <h2 class="text-lg font-semibold mb-4">Course Details</h2>

    <dl class="space-y-3 mb-6">
        <div>
            <dt class="text-xs text-gray-500">Name</dt>
            <dd class="text-base font-medium">{{ $course->name }}</dd>
        </div>
        <div>
            <dt class="text-xs text-gray-500">Batch Code</dt>
            <dd class="text-base font-medium">{{ $course->batch_code ?? '-' }}</dd>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs text-gray-500">Duration (months)</dt>
                <dd class="text-base font-medium">{{ $course->duration_months ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Gross Fee</dt>
                <dd class="text-base font-medium">₹{{ number_format($course->gross_fee, 2) }}</dd>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs text-gray-500">Discount</dt>
                <dd class="text-base font-medium">₹{{ number_format($course->discount ?? 0, 2) }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Net Fee</dt>
                <dd class="text-base font-medium">
                    ₹{{ number_format((float)($course->gross_fee ?? 0) - (float)($course->discount ?? 0), 2) }}
                </dd>
            </div>
        </div>
    </dl>

    <!-- ✅ Batches Section -->
    <div class="mt-6">
        <h3 class="text-md font-semibold mb-2">Batches</h3>

        @if($course->batches->count())
            <div class="overflow-x-auto border rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Batch Name</th>
                            <th class="px-4 py-2 text-left">Start Date</th>
                            <th class="px-4 py-2 text-left">End Date</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($course->batches as $i => $batch)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $i + 1 }}</td>
                                <td class="px-4 py-2">{{ $batch->batch_name }}</td>
                                <td class="px-4 py-2">{{ $batch->start_date}}</td>
                                <td class="px-4 py-2">{{ $batch->end_date}}</td>
                                <td class="px-4 py-2">
                                    @if($batch->is_active)
                                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-600">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-sm">No batches found for this course.</p>
        @endif
    </div>

    {{-- <div class="pt-6 flex gap-2">
        <a href="{{ route('admin.courses.edit', $course->id) }}" class="px-4 py-2 rounded-lg bg-black text-white">Edit</a>
        <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded-lg border">Back</a>
    </div> --}}
</div>
