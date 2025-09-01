<div class="max-w-7xl mx-auto p-6 space-y-6">
    {{-- Header with Student Info --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="md:flex">
            {{-- Student Avatar & Basic Info --}}
            <div class="p-6 flex gap-6 items-center flex-1">
                <div class="h-20 w-20 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-2xl font-bold text-white">
                    {{ Str::of($admission->student->name)->substr(0,1)->upper() }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $admission->student->name }}</h1>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $admission->student->roll_no }} • {{ $admission->student->student_uid }}
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <span @class([
                            'px-3 py-1 rounded-full text-sm font-medium',
                            'bg-green-100 text-green-700' => $admission->status === 'active',
                            'bg-gray-100 text-gray-700' => $admission->status === 'completed',
                            'bg-red-100 text-red-700' => $admission->status === 'cancelled',
                        ])>
                            {{ ucfirst($admission->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="p-6 bg-gray-50 border-t md:border-t-0 md:border-l flex items-center gap-4">
                <a href="{{ route('admin.payments.create', ['admission_id' => $admission->id]) }}"
                   class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Record Payment
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total Fee --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Fee</p>
                    <p class="text-2xl font-bold mt-1">₹{{ number_format($stats['totalFee'], 2) }}</p>
                </div>
                <div class="h-12 w-12 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Paid --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Paid</p>
                    <p class="text-2xl font-bold mt-1">₹{{ number_format($stats['totalPaid'], 2) }}</p>
                </div>
                <div class="h-12 w-12 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Due Fee --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Due Amount</p>
                    <p class="text-2xl font-bold mt-1">₹{{ number_format($stats['dueFee'], 2) }}</p>
                </div>
                <div class="h-12 w-12 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Overdue Installments --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Overdue Installments</p>
                    <p class="text-2xl font-bold mt-1">{{ $stats['overdueCount'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column (Course & Schedule) --}}
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

                @if($admission->schedules->count())
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
                    @if($admission->mode === 'full')
                        <p class="text-xs text-gray-500 mt-2">Full payment - Single installment</p>
                    @endif
                @else
                    <p class="text-sm text-gray-600">No payment schedules found.</p>
                @endif
            </div>
        </div>

        {{-- Right Column (Contact & Transactions) --}}
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
