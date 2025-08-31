<div>
    @php
        $tx = $tx ?? null;
        $admission = $tx->admission;
        $student = $admission?->student;
        $batch = $admission?->batch;
        $course = $batch?->course;

        $afterDue = (float) ($admission->fee_due ?? 0);
        $paid = (float) $tx->amount;
        $beforeDue = $afterDue + $paid;
    @endphp

    <div class="max-w-4xl mx-auto p-6 bg-white">
        <!-- Header -->
        <div class="border-b-2 border-gray-800 pb-4 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Mentors Eduserv™</h1>
                    <p class="text-lg text-gray-600 mb-3">JEE | AIIMS | NEET | NTSE | KVPY | OLYMPIADS Get Started...</p>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>GST Regn No.: 10ADFPJ1214M1Z3</p>
                        <p>Service Type.: Commercial coaching & Training</p>
                        <p>SAC.: </p>
                        <p>Contact No.: 8709833138</p>
                        <p>Address: PURNIA</p>
                        <p>State Code: 10</p>
                        <p>Place of Supply: BIHAR</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">FEE STRUCTURE</h2>
                    <div class="text-2xl font-bold text-blue-600 mb-2">{{ $tx->receipt_number ?? 'TX-' . $tx->id }}</div>
                    <div class="text-sm text-gray-600">
                        <p>Date: {{ $tx->date?->format('d-M-Y') }}</p>
                        <p>Status: <span class="text-green-600 font-bold">SUCCESS</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Overview -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-300">ACCOUNT OVERVIEW</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Name:</span>
                    <span class="ml-2">{{ $student->name ?? '—' }}</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Father's Name:</span>
                    <span class="ml-2">{{ $student->father_name ?? '—' }}</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Roll No:</span>
                    <span class="ml-2">{{ $student->roll_no ?? '—' }}</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">UID:</span>
                    <span class="ml-2">{{ $student->student_uid ?? '—' }}</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Plan Name:</span>
                    <span class="ml-2">PLAN 1</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Address:</span>
                    <span class="ml-2">{{ $student->address ?? '—' }}</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Admission Date:</span>
                    <span class="ml-2">{{ $admission->created_at?->format('d-M-Y') ?? '—' }}</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Mother's Name:</span>
                    <span class="ml-2">{{ $student->mother_name ?? '—' }}</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300 md:col-span-2">
                    <span class="font-bold text-gray-700">Course:</span>
                    <span class="ml-2">{{ $course->name ?? '—' }} # {{ $batch->batch_name ?? '—' }} #
                        ({{ date('Y') }}-{{ date('Y') + 1 }})</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Batch:</span>
                    <span class="ml-2">{{ $batch->batch_name ?? '—' }}</span>
                </div>
                <div class="bg-gray-50 p-3 border border-gray-300">
                    <span class="font-bold text-gray-700">Status:</span>
                    <span class="ml-2">{{ $admission->status ?? 'active' }}</span>
                </div>
            </div>
        </div>

        <!-- Fee Details -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-300">FEE DETAILS</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Description</th>
                            <th class="border border-gray-300 p-3 text-right font-bold text-sm">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 p-3">Gross Fee</td>
                            <td class="border border-gray-300 p-3 text-right">
                                {{ number_format($admission->total_fee ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-3">Total Discount</td>
                            <td class="border border-gray-300 p-3 text-right">
                                {{ number_format($admission->discount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-3">Fee after Discount</td>
                            <td class="border border-gray-300 p-3 text-right">
                                {{ number_format(($admission->total_fee ?? 0) - ($admission->discount ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-3">Tuition Fee</td>
                            <td class="border border-gray-300 p-3 text-right">
                                {{ number_format($admission->tuition_fee ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-3">Others Fee</td>
                            <td class="border border-gray-300 p-3 text-right">
                                {{ number_format(($admission->total_fee ?? 0) - ($admission->tuition_fee ?? 0) - ($admission->discount ?? 0), 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-3">Plan Amount</td>
                            <td class="border border-gray-300 p-3 text-right">0.00</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-3">Late Fine</td>
                            <td class="border border-gray-300 p-3 text-right">0.00</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-3">Taxable Fee</td>
                            <td class="border border-gray-300 p-3 text-right">
                                {{ number_format(($admission->total_fee ?? 0) - ($admission->discount ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-3">Tax*</td>
                            <td class="border border-gray-300 p-3 text-right text-blue-600">
                                {{ number_format($tx->gst ?? 0, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-100 font-bold">
                            <td class="border border-gray-300 p-3">Total Payable Fee With Tax</td>
                            <td class="border border-gray-300 p-3 text-right">
                                {{ number_format(($admission->total_fee ?? 0) - ($admission->discount ?? 0) + ($tx->gst ?? 0), 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Installment Details -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-300">INSTALLMENT DETAILS</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Due Date</th>
                            <th class="border border-gray-300 p-3 text-right font-bold text-sm">Instalment Amount</th>
                            <th class="border border-gray-300 p-3 text-right font-bold text-sm">Paid Amount</th>
                            <th class="border border-gray-300 p-3 text-right font-bold text-sm">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalInstalment = 0;
                            $totalPaid = 0;
                            $totalBalance = 0;
                        @endphp
                        @foreach ($admission->schedules ?? [] as $schedule)
                            @php
                                $instalmentAmount = (float) $schedule->amount;
                                $paidAmount = (float) $schedule->paid_amount;
                                $balance = max(0, $instalmentAmount - $paidAmount);

                                $totalInstalment += $instalmentAmount;
                                $totalPaid += $paidAmount;
                                $totalBalance += $balance;
                            @endphp
                            <tr>
                                <td class="border border-gray-300 p-3">
                                    {{ $schedule->due_date?->format('d-M-Y') ?? '—' }}</td>
                                <td class="border border-gray-300 p-3 text-right">
                                    {{ number_format($instalmentAmount, 2) }}</td>
                                <td class="border border-gray-300 p-3 text-right">{{ number_format($paidAmount, 2) }}
                                </td>
                                <td
                                    class="border border-gray-300 p-3 text-right {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $balance > 0 ? number_format($balance, 2) : '0.00' }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-100 font-bold">
                            <td class="border border-gray-300 p-3"><strong>Total</strong></td>
                            <td class="border border-gray-300 p-3 text-right">
                                <strong>{{ number_format($totalInstalment, 2) }}</strong></td>
                            <td class="border border-gray-300 p-3 text-right">
                                <strong>{{ number_format($totalPaid, 2) }}</strong></td>
                            <td class="border border-gray-300 p-3 text-right bg-red-50 text-red-600">
                                <strong>{{ number_format($totalBalance, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-300">TRANSACTION DETAILS</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Date</th>
                            <th class="border border-gray-300 p-3 text-right font-bold text-sm">Amount Paid</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Inst Due date</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Receipt No.</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Mode</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Details</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPaid = 0;
                        @endphp
                        @foreach ($admission->transactions ?? [] as $transaction)
                            @php
                                $totalPaid += (float) $transaction->amount;
                            @endphp
                            <tr>
                                <td class="border border-gray-300 p-3">
                                    {{ $transaction->date?->format('d-M-Y') ?? '—' }}</td>
                                <td class="border border-gray-300 p-3 text-right">
                                    {{ number_format($transaction->amount, 2) }}</td>
                                <td class="border border-gray-300 p-3">
                                    {{ $transaction->schedule?->due_date?->format('d-M-Y') ?? '—' }}</td>
                                <td class="border border-gray-300 p-3">{{ $transaction->receipt_number ?? '—' }}</td>
                                <td class="border border-gray-300 p-3">{{ strtoupper($transaction->mode ?? '—') }}
                                </td>
                                <td class="border border-gray-300 p-3">
                                    @if ($transaction->mode === 'cheque')
                                        CHQ NO: {{ $transaction->reference_no ?? '—' }} DT:
                                        {{ $transaction->date?->format('Y-m-d') ?? '—' }}
                                    @elseif($transaction->mode === 'online')
                                        UTR {{ $transaction->reference_no ?? '—' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="border border-gray-300 p-3">{{ strtoupper($transaction->mode ?? '—') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-100 font-bold">
                            <td class="border border-gray-300 p-3"><strong>Total</strong></td>
                            <td class="border border-gray-300 p-3 text-right">
                                <strong>{{ number_format($totalPaid, 2) }}</strong></td>
                            <td class="border border-gray-300 p-3" colspan="5"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Other Transaction Details -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-300">OTHER TRANSACTION DETAILS
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Date</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Receipt No.</th>
                            <th class="border border-gray-300 p-3 text-right font-bold text-sm">Amount Paid</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Received In</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">For</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Details</th>
                            <th class="border border-gray-300 p-3 text-left font-bold text-sm">Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 p-3 text-center text-gray-500" colspan="7">No
                                additional transactions</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-800 mb-3">TERMS & CONDITIONS:</h3>
            <ol class="list-decimal list-inside text-sm text-gray-600 space-y-2">
                <li>Tax will be charged as per applicable rate of payment date.</li>
                <li>Cheque/Draft is subject to Realization.</li>
                <li>In case of cheque dishonor, bank charges of Rs. 500 and late fine (upto Rs. 50/day) will be charged.
                </li>
                <li>Fee once paid will not be refunded/adjusted at any stage, under any circumstance.</li>
                <li>Subject to Patna Jurisdiction.</li>
            </ol>
        </div>

        <!-- Print Button -->
        <div class="mt-8 text-center no-print">
            <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Print Receipt
            </button>
            <a href="{{ route('admin.payments.index') }}"
                class="ml-4 px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Back to Payments
            </a>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
                padding: 20px;
            }

            .max-w-4xl {
                max-width: none;
            }
        }
    </style>

</div>
