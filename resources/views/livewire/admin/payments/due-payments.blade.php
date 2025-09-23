<div class="container mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Due Payments</h1>

    {{-- Filters --}}
    <div class="mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
        <input
            type="text"
            placeholder="Search name/phone"
            wire:model.live.debounce.500ms="q"
            class="border rounded px-3 py-2 md:col-span-2"
        />

        <select wire:model.live="status" class="border rounded px-3 py-2">
            <option value="overdue">Overdue</option>
            <option value="upcoming">Due in next N days</option>
            <option value="all">All with dues</option>
        </select>

        <input
            type="number" min="0" wire:model.live="days"
            class="border rounded px-3 py-2"
            title="Only used when 'Due in next N days' is selected"
        />

        <select wire:model.live="courseId" class="border rounded px-3 py-2">
            <option value="">All Courses</option>
            @foreach($courses as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="batchId" class="border rounded px-3 py-2">
            <option value="">All Batches</option>
            @foreach($batches as $b)
                <option value="{{ $b->id }}">{{ $b->batch_name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-3 py-2">S.no</th>
                    <th class="px-3 py-2">Student</th>
                    <th class="px-3 py-2">Phone</th>
                    <th class="px-3 py-2">Course / Batch</th>
                    <th class="px-3 py-2 text-right">Fee Total</th>
                    <th class="px-3 py-2 text-right">Fee Due</th>
                    <th class="px-3 py-2">Next Due</th>
                    <th class="px-3 py-2 text-right">Next Amt</th>
                    <th class="px-3 py-2 text-center">Pending Inst.</th>
                    <th class="px-3 py-2 text-center">Status</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dues as $i=>$row)
                    @php
                        $overdue = $row->next_due_date && \Carbon\Carbon::parse($row->next_due_date)->isPast();
                    @endphp
                    <tr class="border-t">
                        <td class="px-3 py-2 font-medium">{{ $i+1 }}.</td>
                        <td class="px-3 py-2 font-medium">{{ $row->student_name }}</td>
                        <td class="px-3 py-2">{{ $row->student_phone }}</td>
                        <td class="px-3 py-2">
                            <div>{{ $row->course_name }}</div>
                            <div class="text-xs text-gray-500">{{ $row->batch_name }}</div>
                        </td>
                        <td class="px-3 py-2 text-right">₹{{ number_format($row->fee_total,2) }}</td>
                        <td class="px-3 py-2 text-right font-semibold">₹{{ number_format($row->fee_due,2) }}</td>
                        <td class="px-3 py-2">
                            @if($row->next_due_date)
                                <span class="{{ $overdue ? 'text-red-600 font-semibold' : '' }}">
                                    {{ \Carbon\Carbon::parse($row->next_due_date)->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            @if(!is_null($row->next_due_amount))
                                ₹{{ number_format(max(0,$row->next_due_amount),2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center">{{ $row->pending_installments ?? 0 }}</td>
                        <td class="px-3 py-2 text-center">
                            @if($overdue)
                                <span class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs">Overdue</span>
                            @else
                                <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs">Pending</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.admissions.show', $row->id) }}"
                               class="text-blue-600 hover:underline text-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-3 py-6 text-center text-gray-500">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $dues->links() }}
    </div>
</div>
