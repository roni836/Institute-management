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

    <div class="max-w-4xl mx-auto p-4 bg-white text-gray-800 receipt-printable" style="font-family: Arial, Helvetica, sans-serif;">
        <!-- Selection Controls -->
        <div class="mb-4 no-print" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <div style="font-weight:600">Include Sections:</div>
            <label style="font-size:13px"><input type="checkbox" wire:model.live="show_account" class="mr-1" /> Account Details</label>
            <label style="font-size:13px"><input type="checkbox" wire:model.live="show_fee" class="mr-1" /> Fee Details</label>
            <label style="font-size:13px"><input type="checkbox" wire:model.live="show_breakdown" class="mr-1" /> Payment Breakdown</label>
            <label style="font-size:13px"><input type="checkbox" wire:model.live="show_installments" class="mr-1" /> Installments</label>
            <label style="font-size:13px"><input type="checkbox" wire:model.live="show_transactions" class="mr-1" /> Transactions</label>
            <label style="font-size:13px"><input type="checkbox" wire:model.live="show_other" class="mr-1" /> Other Transactions</label>
            <label style="font-size:13px"><input type="checkbox" wire:model.live="show_terms" class="mr-1" /> Terms & Conditions</label>
            <button wire:click="selectAll" style="margin-left:auto;padding:6px 10px;background:#111;color:#fff;border:none;cursor:pointer">Select All</button>
            <button wire:click="selectNone" style="padding:6px 10px;background:#666;color:#fff;border:none;cursor:pointer">Select None</button>
        </div>
        <!-- Header -->
        <div style="border-bottom:2px solid #000;padding-bottom:8px;margin-bottom:8px;display:flex;align-items:flex-start;justify-content:space-between;">
            <div style="display:flex;gap:12px;align-items:center;">
                <img src="{{ asset('logo.png') }}" alt="logo" style="height:48px;object-fit:contain" />
                <div>
                    <div style="font-size:18px;font-weight:700;">{{ env("APP_NAME") }}</div>
                    <div style="font-size:12px;color:#333;margin-top:2px;">JEE | AIIMS | NEET | NTSE | KVPY | OLYMPIADS</div>
                    <div style="font-size:11px;color:#333;margin-top:6px;line-height:1.2">
                        GST Regn No.: 10ADFPJ1214M1Z3<br />
                        Service Type: Commercial coaching & Training<br />
                        Contact No.: 8709833138<br />
                        Address: PURNIA, State Code: 10<br />
                    </div>
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-size:14px;font-weight:700;margin-bottom:6px">FEE STRUCTURE</div>
                <div style="font-size:18px;font-weight:700;color:#000;margin-bottom:6px;">{{ $tx->receipt_number ?? 'TX-' . $tx->id }}</div>
                <div style="font-size:12px;color:#333;">
                    <div>Date: {{ $tx->date?->format('d-M-Y') }}</div>
                    <div>Status: <span style="color:#008000;font-weight:700">SUCCESS</span></div>
                </div>
            </div>
        </div>

        <!-- Account Overview -->
        @if($show_account)
        <div style="margin-bottom:12px">
            <div style="font-size:13px;font-weight:700;border-bottom:1px solid #333;padding-bottom:6px;margin-bottom:8px">ACCOUNT OVERVIEW</div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;font-size:12px;">
                <div style="flex:1 1 45%;border:1px solid #000;padding:6px;"> <strong>Name:</strong> {{ $student->name ?? '—' }}</div>
                <div style="flex:1 1 45%;border:1px solid #000;padding:6px;"> <strong>Father's Name:</strong> {{ $student->father_name ?? '—' }}</div>
                <div style="flex:1 1 30%;border:1px solid #000;padding:6px;"> <strong>Roll No:</strong> {{ $student->roll_no ?? '—' }}</div>
                <div style="flex:1 1 30%;border:1px solid #000;padding:6px;"> <strong>UID:</strong> {{ $student->student_uid ?? '—' }}</div>
                <div style="flex:1 1 30%;border:1px solid #000;padding:6px;"> <strong>Plan:</strong> PLAN 1</div>
                <div style="flex:1 1 60%;border:1px solid #000;padding:6px;"> <strong>Address:</strong> {{ $student->address ?? '—' }}</div>
                <div style="flex:1 1 30%;border:1px solid #000;padding:6px;"> <strong>Admission Date:</strong> {{ $admission->created_at?->format('d-M-Y') ?? '—' }}</div>
                <div style="flex:1 1 30%;border:1px solid #000;padding:6px;"> <strong>Mother's Name:</strong> {{ $student->mother_name ?? '—' }}</div>
                <div style="flex:1 1 100%;border:1px solid #000;padding:6px;"> <strong>Course:</strong> {{ $course->name ?? '—' }} # {{ $batch->batch_name ?? '—' }} ({{ date('Y') }}-{{ date('Y') + 1 }})</div>
                <div style="flex:1 1 30%;border:1px solid #000;padding:6px;"> <strong>Batch:</strong> {{ $batch->batch_name ?? '—' }}</div>
                <div style="flex:1 1 30%;border:1px solid #000;padding:6px;"> <strong>Status:</strong> {{ $admission->status ?? 'active' }}</div>
            </div>
    </div>
    @endif

        <!-- Fee Details -->
    @if($show_fee)
    <div style="margin-bottom:12px">
            <div style="font-size:13px;font-weight:700;border-bottom:1px solid #333;padding-bottom:6px;margin-bottom:8px">FEE DETAILS</div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Description</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Gross Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->gross_fee ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Total Discount</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right;color:#dc2626">-{{ number_format($admission->discount ?? 0, 2) }}</td>
                    </tr>
                    <tr style="font-weight:700;background:#f5f5f5">
                        <td style="border:1px solid #000;padding:6px">Fee after Discount</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format(($course->gross_fee ?? 0) - ($admission->discount ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Taxable Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format(($course->gross_fee ?? 0) - ($admission->discount ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Tax* (18%)</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right;color:#2563eb">{{ number_format($tx->gst ?? 0, 2) }}</td>
                    </tr>
                    <tr style="font-weight:700;background:#f5f5f5">
                        <td style="border:1px solid #000;padding:6px">Total Payable Fee With Tax</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format(($course->gross_fee ?? 0) - ($admission->discount ?? 0) + ($tx->gst ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>
    </div>
    @endif

        <!-- Payment Breakdown -->
    @if($show_breakdown)
    <div style="margin-bottom:12px">
            <div style="font-size:13px;font-weight:700;border-bottom:1px solid #333;padding-bottom:6px;margin-bottom:8px">PAYMENT BREAKDOWN</div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Fee Component</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($course->tution_fee > 0)
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Tuition Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->tution_fee, 2) }}</td>
                    </tr>
                    @endif
                    @if($course->admission_fee > 0)
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Admission Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->admission_fee, 2) }}</td>
                    </tr>
                    @endif
                    @if($course->exam_fee > 0)
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Exam Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->exam_fee, 2) }}</td>
                    </tr>
                    @endif
                    @if($course->infra_fee > 0)
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Infrastructure Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->infra_fee, 2) }}</td>
                    </tr>
                    @endif
                    @if($course->SM_fee > 0)
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Study Material Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->SM_fee, 2) }}</td>
                    </tr>
                    @endif
                    @if($course->tech_fee > 0)
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Technology Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->tech_fee, 2) }}</td>
                    </tr>
                    @endif
                    @if($course->other_fee > 0)
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Other Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->other_fee, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="font-weight:700;background:#f5f5f5">
                        <td style="border:1px solid #000;padding:6px">Subtotal</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($course->gross_fee ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #000;padding:6px">Discount Applied</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right;color:#dc2626">-{{ number_format($admission->discount ?? 0, 2) }}</td>
                    </tr>
                    <tr style="font-weight:700;background:#f5f5f5">
                        <td style="border:1px solid #000;padding:6px">Net Fee</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format(($course->gross_fee ?? 0) - ($admission->discount ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>
    </div>
    @endif

        <!-- Installment Details -->
    @if($show_installments)
    <div style="margin-bottom:12px">
            <div style="font-size:13px;font-weight:700;border-bottom:1px solid #333;padding-bottom:6px;margin-bottom:8px">INSTALLMENT DETAILS</div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Due Date</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Instalment Amount</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Paid Amount</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Balance</th>
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
                            <td style="border:1px solid #000;padding:6px">{{ $schedule->due_date?->format('d-M-Y') ?? '—' }}</td>
                            <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($instalmentAmount, 2) }}</td>
                            <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($paidAmount, 2) }}</td>
                            <td style="border:1px solid #000;padding:6px;text-align:right;color:{{ $balance > 0 ? '#b91c1c' : '#046c4e' }}">{{ $balance > 0 ? number_format($balance, 2) : '0.00' }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight:700;background:#f5f5f5">
                        <td style="border:1px solid #000;padding:6px">Total</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($totalInstalment, 2) }}</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($totalPaid, 2) }}</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right;color:#b91c1c">{{ number_format($totalBalance, 2) }}</td>
                    </tr>
                </tbody>
            </table>
    </div>
    @endif

        <!-- Transaction Details -->
    @if($show_transactions)
    <div style="margin-bottom:12px">
            <div style="font-size:13px;font-weight:700;border-bottom:1px solid #333;padding-bottom:6px;margin-bottom:8px">TRANSACTION DETAILS</div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Date</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Amount Paid</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Inst Due date</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Receipt No.</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Mode</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Details</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Comment</th>
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
                            <td style="border:1px solid #000;padding:6px">{{ $transaction->date?->format('d-M-Y') ?? '—' }}</td>
                            <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($transaction->amount, 2) }}</td>
                            <td style="border:1px solid #000;padding:6px">{{ $transaction->schedule?->due_date?->format('d-M-Y') ?? '—' }}</td>
                            <td style="border:1px solid #000;padding:6px">{{ $transaction->receipt_number ?? '—' }}</td>
                            <td style="border:1px solid #000;padding:6px">{{ strtoupper($transaction->mode ?? '—') }}</td>
                            <td style="border:1px solid #000;padding:6px">
                                @if ($transaction->mode === 'cheque')
                                    CHQ NO: {{ $transaction->reference_no ?? '—' }} DT: {{ $transaction->date?->format('Y-m-d') ?? '—' }}
                                @elseif($transaction->mode === 'online')
                                    UTR {{ $transaction->reference_no ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="border:1px solid #000;padding:6px">{{ strtoupper($transaction->mode ?? '—') }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight:700;background:#f5f5f5">
                        <td style="border:1px solid #000;padding:6px">Total</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($totalPaid, 2) }}</td>
                        <td style="border:1px solid #000;padding:6px" colspan="5"></td>
                    </tr>
                </tbody>
            </table>
    </div>
    @endif

        <!-- Other Transaction Details -->
    @if($show_other)
    <div style="margin-bottom:12px">
            <div style="font-size:13px;font-weight:700;border-bottom:1px solid #333;padding-bottom:6px;margin-bottom:8px">OTHER TRANSACTION DETAILS</div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Date</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Receipt No.</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Amount Paid</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Received In</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">For</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Details</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #000;padding:6px;text-align:center;color:#666" colspan="7">No additional transactions</td>
                    </tr>
                </tbody>
            </table>
    </div>
    @endif

        <!-- Terms & Conditions -->
    @if($show_terms)
    <div style="margin-top:12px">
            <div style="font-size:13px;font-weight:700;margin-bottom:6px">TERMS & CONDITIONS:</div>
            <ol style="font-size:11px;color:#333;margin:0 0 8px 18px;line-height:1.3">
                <li>Tax will be charged as per applicable rate of payment date.</li>
                <li>Cheque/Draft is subject to Realization.</li>
                <li>In case of cheque dishonor, bank charges of Rs. 500 and late fine (upto Rs. 50/day) will be charged.</li>
                <li>Fee once paid will not be refunded/adjusted at any stage, under any circumstance.</li>
                <li>Subject to Patna Jurisdiction.</li>
            </ol>
    </div>
    @endif

        <!-- Print Button -->
        <div class="mt-8 text-center no-print" style="margin-top:12px;text-align:center">
            <button onclick="window.print()" style="padding:8px 14px;background:#000;color:#fff;border:none;cursor:pointer">Print Receipt</button>
            <a href="{{ route('admin.payments.index') }}" style="margin-left:8px;padding:8px 14px;background:#666;color:#fff;text-decoration:none;">Back to Payments</a>
        </div>
    </div>

    <style>
        /* Print friendly compact styles */
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 10px; }
            img { max-width: 100%; }
            a { color: #000; text-decoration: none; }
        }

        /* Reduce default margins on small screens */
        @media screen and (max-width:900px) {
            .max-w-4xl { padding: 8px; }
        }
    </style>

</div>
