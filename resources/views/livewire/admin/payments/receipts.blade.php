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

    <!-- Print Button -->
    <div class="mb-4 text-center no-print" style="margin-bottom:12px;text-align:center">
        <button onclick="window.print()"
            style="padding:8px 14px;background:#000;color:#fff;border:none;cursor:pointer">Print Receipt</button>
        <a href="{{ route('admin.payments.index') }}"
            style="margin-left:8px;padding:8px 14px;background:#666;color:#fff;text-decoration:none;">Back to
            Payments</a>
    </div>

    <div class="max-w-5xl border-2 border-[#333] mx-auto p-4 bg-white text-gray-800 receipt-printable"
        style="font-family: Arial, Helvetica, sans-serif; max-height: 100vh; overflow: hidden;">
      


        <!-- Header -->
        <div
            style="padding-bottom:8px;margin-bottom:4px;display:flex;align-items:flex-start;justify-content:space-between;">
            <div style="display:flex;gap:12px;align-items:center;">
                {{-- <img src="{{ asset('logo.png') }}" alt="logo" style="height:150px;object-fit:contain" /> --}}
                <div>
                    {{-- <div style="font-size:18px;font-weight:700;">{{ env('APP_NAME') }}</div>
                    <div style="font-size:12px;color:#333;margin-top:2px;">JEE | AIIMS | NEET | NTSE | KVPY | OLYMPIADS</div> --}}
                    <div style="font-size:16px;color:#333;margin-top:6px;line-height:1.2">
                        @if ($admission->is_gst)
                            <strong>GST Regn No. </strong>: 10ABBCA8020F1ZG<br />
                        @endif
                        <strong>Service Type </strong>: Commercial coaching & Training<br />
                        <strong>Contact No. </strong>: 9155588414, 9798986029<br />
                        <strong>Address </strong>: PURNIA, BIHAR<br />
                        <strong>State Code </strong>: 10<br />
                        <strong>Place of Supply </strong>: Bihar<br />
                    </div>
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-size:14px;font-weight:600;margin-bottom:6px">Ahantra Edu Ventures Private Limited<br>
                    (A franchisee of Mentors Eduserv)</div>
                <div style="font-size:14px;font-weight:600;color:#000;margin-bottom:6px;">
                    # {{ $tx->receipt_number ?? 'TX-' . $tx->id }}</div>
                <div style="font-size:13px;color:#333;">
                    <div>Date: {{ $tx->date?->format('d-M-Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Account Overview -->
        <div>
            <div class="bg-gray-200 px-2 py-2  text-center" style="font-size:13px;font-weight:700;padding-bottom:4px;">
                ACCOUNT OVERVIEW</div>
            <div
                style="display:grid;grid-template-columns:repeat(2,1fr);gap:6px;font-size:12px; border:2px solid #000;padding-left:8px">
                <div style="padding:8px">
                    <div style="flex:1"> <strong>Name:</strong> {{ $student->name ?? '—' }}</div>
                    <div style="flex:1"> <strong>Father's Name:</strong> {{ $student->father_name ?? '—' }}</div>
                    <div style="flex:1"> <strong>Roll No:</strong> {{ $student->enrollment_id ?? '—' }}</div>
                    <div style="flex:1"> <strong>UID:</strong> {{ $student->student_uid ?? '—' }}</div>
                    {{-- <div style="flex:1"> <strong>Plan:</strong> PLAN 1</div> --}}
                    <div style="flex:1"> <strong>Address:</strong> {{ $student->address ?? '—' }}</div>
                </div>
                <div style="border-left:2px solid #000;padding:8px">
                    <div style="flex:1"> <strong>Admission Date:</strong>
                        {{ $admission->created_at?->format('d-M-Y') ?? '—' }}</div>
                    <div style="flex:1"> <strong>Mother's Name:</strong> {{ $student->mother_name ?? '—' }}</div>
                    <div style="flex:1 "> <strong>Course:</strong> {{ $course->name ?? '—' }} #
                        {{ $batch->batch_name ?? '—' }} ({{ date('Y') }}-{{ date('Y') + 1 }})</div>
                    <div style="flex:1"> <strong>Batch:</strong> {{ $batch->batch_name ?? '—' }}</div>
                    <div style="flex:1"> <strong>Status:</strong> {{ $admission->status ?? 'active' }}</div>
                </div>
            </div>
        </div>

        <!-- Fee Details -->
        <div>
            <div class="bg-gray-200 px-2 py-2  text-center" style="font-size:13px;font-weight:700;padding-bottom:6px;">
                FEE DETAILS</div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                @php
                    // Calculate fee components
                    $grossFee = $course->gross_fee ?? 0;
                    $discount = $admission->discount ?? 0;
                    $feeAfterDiscount = $grossFee - $discount;

                    $tutionFee = $course->tution_fee ?? 0;
                    $admissionFee = $course->admission_fee ?? 0;
                    $examFee = $course->exam_fee ?? 0;
                    $infraFee = $course->infra_fee ?? 0;
                    $smFee = $course->SM_fee ?? 0;
                    $techFee = $course->tech_fee ?? 0;
                    $otherFee = $course->other_fee ?? 0;

                    // Sum of all component fees
                    $totalComponentFees =
                        $tutionFee + $admissionFee + $examFee + $infraFee + $smFee + $techFee + $otherFee;

                    // Calculate GST (10%)
                    $taxableAmount = $feeAfterDiscount;

                    if ($tx->gst != 0) {
                        $gstAmount = $taxableAmount * 0.18;
                        $totalPayable = $taxableAmount + $gstAmount;
                    } else {
                        $totalPayable = $taxableAmount;
                    }

                    // Total payable

                @endphp
                <tr>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Gross Fee</td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Discount</td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Fee After Discount
                    </td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Tuition Fee</td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Admission Fee</td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Exam Fee</td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Infra Fee</td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">SM Fee</td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Tech Fee</td>
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Others Fee</td>
                    @if ($tx->gst != 0)
                        <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">GST (18%)</td>
                    @endif
                    <td style="border:1px solid #000;padding:6px;background:#f5f5f5;font-weight:bold">Total Payable</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($grossFee, 2) }}
                    </td>
                    <td style="border:1px solid #000;padding:6px;text-align:right;color:#dc2626">
                        -{{ number_format($discount, 2) }}</td>
                    <td style="border:1px solid #000;padding:6px;text-align:right;font-weight:bold">
                        {{ number_format($feeAfterDiscount, 2) }}</td>
                    <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($tutionFee, 2) }}
                    </td>
                    <td style="border:1px solid #000;padding:6px;text-align:right">
                        {{ number_format($admissionFee, 2) }}</td>
                    <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($examFee, 2) }}
                    </td>
                    <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($infraFee, 2) }}
                    </td>
                    <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($smFee, 2) }}</td>
                    <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($techFee, 2) }}
                    </td>
                    <td style="border:1px solid #000;padding:6px;text-align:right">{{ number_format($otherFee, 2) }}
                    </td>
                    @if ($tx->gst != 0)
                        <td style="border:1px solid #000;padding:6px;text-align:right;color:#2563eb">
                            {{ number_format($gstAmount, 2) }}</td>
                    @endif
                    <td style="border:1px solid #000;padding:6px;text-align:right;font-weight:bold">
                        {{ number_format($totalPayable, 2) }}</td>
                </tr>
                </tbody>
            </table>
        </div>


        <!-- Installment Details -->
        <div>
            <div class="bg-gray-200 px-2 py-2  text-center" style="font-size:13px;font-weight:700;padding-bottom:6px;">
                @if($isConsolidatedReceipt ?? false)
                    COMPLETE INSTALLMENT SCHEDULE
                @else
                    INSTALLMENT DETAILS
                @endif
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:6px;text-align:center;background:#f5f5f5">Inst. #</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Due Date</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Instalment Amount</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Paid Amount</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Balance</th>
                        <th style="border:1px solid #000;padding:6px;text-align:center;background:#f5f5f5">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalInstalment = 0;
                        $totalPaid = 0;
                        $totalBalance = 0;
                        $schedulesToShow = $isConsolidatedReceipt && !empty($allPaymentSchedules) ? $allPaymentSchedules : ($admission->schedules ?? []);
                    @endphp
                    @foreach ($schedulesToShow as $schedule)
                        @php
                            $instalmentAmount = (float) $schedule->amount;
                            $paidAmount = (float) $schedule->paid_amount;
                            $balance = max(0, $instalmentAmount - $paidAmount);
                            $status = $schedule->status ?? 'pending';
                            
                            $totalInstalment += $instalmentAmount;
                            $totalPaid += $paidAmount;
                            $totalBalance += $balance;
                            
                            // Determine row styling based on status
                            $rowStyle = '';
                            $statusColor = '#666';
                            if ($status === 'paid') {
                                $rowStyle = 'background:#e8f5e8;';
                                $statusColor = '#046c4e';
                            } elseif ($status === 'partial') {
                                $rowStyle = 'background:#fff3cd;';
                                $statusColor = '#856404';
                            } elseif ($balance > 0) {
                                $statusColor = '#b91c1c';
                            }
                        @endphp
                        <tr style="{{ $rowStyle }}">
                            <td style="border:1px solid #000;padding:6px;text-align:center">
                                {{ $schedule->installment_no ?? '—' }}</td>
                            <td style="border:1px solid #000;padding:6px">
                                {{ $schedule->due_date?->format('d-M-Y') ?? '—' }}</td>
                            <td style="border:1px solid #000;padding:6px;text-align:right">
                                {{ number_format($instalmentAmount, 2) }}</td>
                            <td style="border:1px solid #000;padding:6px;text-align:right">
                                {{ number_format($paidAmount, 2) }}</td>
                            <td style="border:1px solid #000;padding:6px;text-align:right;color:{{ $balance > 0 ? '#b91c1c' : '#046c4e' }}">
                                {{ $balance > 0 ? number_format($balance, 2) : '0.00' }}</td>
                            <td style="border:1px solid #000;padding:6px;text-align:center;color:{{ $statusColor }};font-weight:600">
                                {{ strtoupper($status) }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight:700;background:#f5f5f5">
                        <td style="border:1px solid #000;padding:6px" colspan="2">Total</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">
                            {{ number_format($totalInstalment, 2) }}</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right">
                            {{ number_format($totalPaid, 2) }}</td>
                        <td style="border:1px solid #000;padding:6px;text-align:right;color:#b91c1c">
                            {{ number_format($totalBalance, 2) }}</td>
                        <td style="border:1px solid #000;padding:6px;text-align:center">
                            @if($totalBalance == 0)
                                <span style="color:#046c4e">COMPLETED</span>
                            @else
                                <span style="color:#b91c1c">PENDING</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Transaction Details -->
        <div>
            <div class="bg-gray-200 px-2 py-2  text-center" style="font-size:13px;font-weight:700;padding-bottom:6px;">
                @if($isConsolidatedReceipt ?? false)
                    ALL PAYMENT TRANSACTIONS
                @else
                    TRANSACTION DETAILS
                @endif
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:6px;text-align:center;background:#f5f5f5">S.No</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Payment Date</th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Amount Paid</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Installment</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Due Date</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Mode</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPaid = 0;
                        $transactionsToShow = $isConsolidatedReceipt ? $consolidatedTransactions : collect([$tx]);
                    @endphp
                    @foreach ($transactionsToShow as $index => $transaction)
                        @php
                            $totalPaid += (float) $transaction->amount;
                        @endphp
                        <tr>
                            <td style="border:1px solid #000;padding:6px;text-align:center">
                                {{ $index + 1 }}</td>
                            <td style="border:1px solid #000;padding:6px;font-weight:600">
                                {{ $transaction->date?->format('d-M-Y') ?? '—' }}
                                <br><small style="color:#666;font-weight:normal">{{ $transaction->date?->format('h:i A') ?? '' }}</small>
                            </td>
                            <td style="border:1px solid #000;padding:6px;text-align:right;font-weight:600;color:#046c4e">
                                ₹ {{ number_format($transaction->amount, 2) }}</td>
                            <td style="border:1px solid #000;padding:6px">
                                {{ $transaction->schedule ? 'Installment #' . $transaction->schedule->installment_no : 'Advance Payment' }}
                                @if($transaction->schedule?->due_date)
                                    <br><small style="color:#666">Due: {{ $transaction->schedule->due_date->format('d-M-Y') }}</small>
                                @endif
                            </td>
                            <td style="border:1px solid #000;padding:6px">
                                {{ $transaction->schedule?->due_date?->format('d-M-Y') ?? '—' }}</td>
                            <td style="border:1px solid #000;padding:6px">
                                <span style="background:#e5e7eb;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600">
                                    {{ strtoupper($transaction->mode ?? '—') }}
                                </span>
                            </td>
                            <td style="border:1px solid #000;padding:6px;font-size:11px">
                                @if ($transaction->mode === 'cheque')
                                    <strong>CHQ:</strong> {{ $transaction->reference_no ?? '—' }}
                                    <br><small>Date: {{ $transaction->date?->format('d-M-Y') ?? '—' }}</small>
                                @elseif($transaction->mode === 'online')
                                    <strong>UTR:</strong> {{ $transaction->reference_no ?? '—' }}
                                @elseif($transaction->reference_no)
                                    <strong>Ref:</strong> {{ $transaction->reference_no }}
                                @else
                                    —
                                @endif
                                @if($transaction->transaction_id)
                                    <br><small style="color:#666">ID: {{ $transaction->transaction_id }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr style="font-weight:700;background:#f5f5f5">
                        <td style="border:1px solid #000;padding:6px" colspan="2">
                            @if($isConsolidatedReceipt ?? false)
                                Total Payment ({{ $consolidatedTransactions->count() }} Transactions)
                            @else
                                Total Payment
                            @endif
                        </td>
                        <td style="border:1px solid #000;padding:6px;text-align:right;color:#046c4e;font-size:14px">
                            ₹ {{ number_format($totalPaid, 2) }}</td>
                        <td style="border:1px solid #000;padding:6px" colspan="4">
                            @if($isConsolidatedReceipt ?? false)
                                Receipt No: {{ $tx->receipt_number ?? '—' }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            
            @if($isConsolidatedReceipt ?? false)
                <div style="margin-top: 10px; padding: 8px; background-color: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 4px; font-size: 11px;">
                    <strong style="color: #0369a1;">Consolidated Receipt:</strong> 
                    <span style="color: #0369a1;">This receipt covers {{ $consolidatedTransactions->count() }} installment payments made together on {{ $tx->date?->format('d-M-Y') }}.</span>
                </div>
            @endif
        </div>

        <!-- Other Transaction Details -->
        {{-- <div>
            <div class="bg-gray-200 px-2 py-2  text-center" style="font-size:13px;font-weight:700;padding-bottom:6px;">
                OTHER TRANSACTION DETAILS</div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;border:1px solid #000;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Date</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Receipt No.
                        </th>
                        <th style="border:1px solid #000;padding:6px;text-align:right;background:#f5f5f5">Amount Paid
                        </th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Received In
                        </th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">For</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Details</th>
                        <th style="border:1px solid #000;padding:6px;text-align:left;background:#f5f5f5">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #000;padding:6px;text-align:center;color:#666" colspan="7">No
                            additional transactions</td>
                    </tr>
                </tbody>
            </table>
        </div> --}}

        <!-- Terms & Conditions -->
        <div style="margin-top:12px">
            <div style="font-size:13px;font-weight:700;margin-bottom:6px">TERMS & CONDITIONS:</div>
            <ol style="font-size:11px;color:#333;margin:0 0 8px 18px;line-height:1.3; list-style-type: decimal;">
                <li>Tax will be charged as per applicable rate of payment date.</li>
                <li>Cheque/Draft is subject to Realization.</li>
                <li>In case of cheque dishonor, bank charges of Rs. 500 and late fine (upto Rs. 50/day) will be charged.
                </li>
                <li>Fee once paid will not be refunded/adjusted at any stage, under any circumstance.</li>
            </ol>
        </div>

    </div>

    <style>
        /* Print friendly compact styles */
        @media print {
            .no-print {
                display: none !important;
                height: 0 !important;
            }

            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .receipt-printable {
                margin: 0;
                padding: 10px;
                max-width: 100%;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            table {
                page-break-inside: avoid;
                font-size: 11px !important;
            }

            td, th {
                padding: 4px !important;
            }

            img {
                max-width: 100%;
                max-height: 120px;
            }

            a {
                color: #000;
                text-decoration: none;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4;
                margin: 0.5cm;
            }
        }

        /* Reduce default margins on small screens */
        @media screen and (max-width:900px) {
            .max-w-4xl {
                padding: 8px;
            }
        }
    </style>

</div>
