<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 grid place-items-center text-white text-lg font-semibold">
                {{ Str::of($admission->student->name)->substr(0,1)->upper() }}
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-semibold">{{ $admission->student->name }}</h1>
                <div class="text-sm text-gray-600">
                    Roll: {{ $admission->student->roll_no }} • UID: {{ $admission->student->student_uid }}
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span @class([
                'px-3 py-1 rounded-full text-xs font-medium capitalize',
                'bg-green-100 text-green-700' => $admission->status === 'completed',
                'bg-blue-100 text-blue-700' => $admission->status === 'active',
                'bg-red-100 text-red-700' => $admission->status === 'cancelled',
            ])>
                {{ $admission->status }}
            </span>

            <a href="{{ route('admin.payments.create', ['admission_id' => $admission->id]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-black text-white hover:bg-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M12 4v16m8-8H4"/></svg>
                Record Payment
            </a>
        </div>
    </div>

    {{-- Overview Cards --}}
    <div class="grid md:grid-cols-4 gap-4">
        <div class="border rounded-xl bg-white p-4">
            <div class="text-xs text-gray-500">Total Fee</div>
            <div class="text-lg font-semibold">₹ {{ number_format($admission->fee_total, 2) }}</div>
        </div>
        <div class="border rounded-xl bg-white p-4">
            <div class="text-xs text-gray-500">Total Paid</div>
            <div class="text-lg font-semibold">₹ {{ number_format($this->totalPaid, 2) }}</div>
        </div>
        <div class="border rounded-xl bg-white p-4">
            <div class="text-xs text-gray-500">Due</div>
            <div class="text-lg font-semibold">₹ {{ number_format($admission->fee_due, 2) }}</div>
        </div>
        <div class="border rounded-xl bg-white p-4">
            <div class="text-xs text-gray-500">Overdue Installments</div>
            <div class="text-lg font-semibold">{{ $this->overdueCount }}</div>
        </div>
    </div>

    {{-- Progress --}}
    <div class="bg-white border rounded-xl p-4">
        <div class="flex items-center justify-between text-sm mb-2">
            <div class="text-gray-700 font-medium">Payment Progress</div>
            <div class="text-gray-600">{{ $this->paidPercent }}%</div>
        </div>
        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-2 bg-gradient-to-r from-emerald-500 to-teal-500"
                 style="width: {{ $this->paidPercent }}%"></div>
        </div>
        @if($this->nextDue)
            <div class="text-sm text-gray-600 mt-3">
                Next due: <span class="font-medium">Inst #{{ $this->nextDue['installment_no'] }}</span>
                • ₹ {{ $this->nextDue['remaining'] }} • {{ $this->nextDue['due_date'] }}
                <span @class([
                    'ml-2 px-2 py-0.5 rounded text-xs',
                    'bg-yellow-100 text-yellow-800' => $this->nextDue['status']==='pending' || $this->nextDue['status']==='partial',
                    'bg-green-100 text-green-700' => $this->nextDue['status']==='paid',
                ])>
                    {{ $this->nextDue['status'] }}
                </span>
            </div>
        @endif
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Course & Batch --}}
            <div class="bg-white border rounded-xl p-4">
                <h2 class="text-base font-semibold mb-3">Course & Batch</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-3">
                        <div class="text-xs text-gray-500 mb-1">Course</div>
                        <div class="font-medium">{{ $admission->batch->course->name }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            Duration: {{ $admission->batch->course->duration_months ?? '—' }} months
                        </div>
                        <div class="text-sm text-gray-600">
                            Gross Fee: ₹ {{ number_format($admission->batch->course->gross_fee, 2) }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Discount: ₹ {{ number_format($admission->discount, 2) }}
                        </div>
                    </div>

                    <div class="border rounded-lg p-3">
                        <div class="text-xs text-gray-500 mb-1">Batch</div>
                        <div class="font-medium">{{ $admission->batch->batch_name }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            Start: {{ \Carbon\Carbon::parse($admission->batch->start_date)->format('d M Y') ?? '—' }}
                            • End: {{ \Carbon\Carbon::parse($admission->batch->end_date)->format('d M Y') ?? '—' }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Admission Date: {{ \Carbon\Carbon::parse($admission->admission_date)->format('d M Y') }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Mode: <span class="capitalize">{{ $admission->mode }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Schedule --}}
            <div class="bg-white border rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-semibold">Payment Schedule</h2>
                    @if($admission->mode === 'installment')
                        <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700">
                            {{ $admission->schedules->count() }} installments
                        </span>
                    @else
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">Full Payment</span>
                    @endif
                </div>

                @if($admission->mode === 'installment' && $admission->schedules->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left bg-gray-50">
                                    <th class="p-2">#</th>
                                    <th class="p-2">Due Date</th>
                                    <th class="p-2">Amount</th>
                                    <th class="p-2">Paid</th>
                                    <th class="p-2">Remaining</th>
                                    <th class="p-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($admission->schedules as $s)
                                    @php
                                        $remaining = max(0, (float)$s->amount - (float)$s->paid_amount);
                                    @endphp
                                    <tr>
                                        <td class="p-2 font-medium">#{{ $s->installment_no }}</td>
                                        <td class="p-2">{{ \Carbon\Carbon::parse($s->due_date)->format('d M Y') }}</td>
                                        <td class="p-2">₹ {{ number_format($s->amount,2) }}</td>
                                        <td class="p-2">₹ {{ number_format($s->paid_amount,2) }}</td>
                                        <td class="p-2">₹ {{ number_format($remaining,2) }}</td>
                                        <td class="p-2">
                                            <span @class([
                                                'px-2 py-1 rounded text-xs capitalize',
                                                'bg-green-100 text-green-700' => $s->status === 'paid',
                                                'bg-yellow-100 text-yellow-800' => $s->status === 'partial' || $s->status === 'pending',
                                            ])>{{ $s->status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-600">No installment schedule (full payment).</p>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- Contact Card --}}
            <div class="bg-white border rounded-xl p-4">
                <h2 class="text-base font-semibold mb-3">Student Contact</h2>
                <div class="space-y-1 text-sm">
                    <div><span class="text-gray-500">Phone:</span> {{ $admission->student->phone ?? '—' }}</div>
                    <div><span class="text-gray-500">Email:</span> {{ $admission->student->email ?? '—' }}</div>
                    <div><span class="text-gray-500">Address:</span> {{ $admission->student->address ?? '—' }}</div>
                    <div><span class="text-gray-500">Father:</span> {{ $admission->student->father_name ?? '—' }}</div>
                    <div><span class="text-gray-500">Mother:</span> {{ $admission->student->mother_name ?? '—' }}</div>
                    <div>
                        <span class="text-gray-500">Student Status:</span>
                        <span class="ml-1 px-2 py-0.5 rounded text-xs capitalize
                            {{ $admission->student->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $admission->student->status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Transactions --}}
            <div class="bg-white border rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-semibold">Recent Transactions</h2>
                    <a href="{{ route('admin.payments.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                </div>

                @if($admission->transactions->count())
                    <ul class="space-y-3">
                        @foreach($admission->transactions->take(6) as $t)
                            <li class="border rounded-lg p-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium">₹ {{ number_format($t->amount,2) }}</div>
                                    <div class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($t->date)->format('d M Y') }}
                                        • <span class="capitalize">{{ $t->mode }}</span>
                                        @if($t->reference_no)
                                            • Ref: {{ $t->reference_no }}
                                        @endif
                                    </div>
                                </div>
                                <span @class([
                                    'px-2 py-1 rounded text-xs capitalize',
                                    'bg-green-100 text-green-700' => $t->status === 'success',
                                    'bg-yellow-100 text-yellow-800' => $t->status === 'pending',
                                    'bg-red-100 text-red-700' => $t->status === 'failed',
                                ])>{{ $t->status }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-600">No transactions yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
