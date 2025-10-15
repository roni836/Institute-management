<div class="container mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">Due Payments</h1>
        <button wire:click="exportToExcel" 
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export to Excel
        </button>
    </div>

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
