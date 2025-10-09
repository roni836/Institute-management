@php
    $tx = $tx ?? null;
    $admission = $tx->admission;
    $student   = $admission?->student;
    $batch     = $admission?->batch;
    $course    = $batch?->course;

    $afterDue  = (float)($admission->fee_due ?? 0);
    $paid      = (float)$tx->amount;
    $beforeDue = $afterDue + $paid;

    function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $tx->receipt_number ?? 'TX-'.$tx->id }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, Arial, sans-serif; 
            color: #111; 
            font-size: 12px; 
            margin: 0;
            padding: 20px;
        }
        .container { 
            width: 100%; 
            max-width: 800px; 
            margin: 0 auto; 
        }
        .header { 
            border-bottom: 2px solid #333; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
        }
        .company-info { 
            float: left; 
            width: 60%; 
        }
        .receipt-info { 
            float: right; 
            width: 35%; 
            text-align: right; 
        }
        .clear { clear: both; }
        .company-name { 
            font-size: 24px; 
            font-weight: bold; 
            color: #333; 
            margin-bottom: 5px; 
        }
        .company-tagline { 
            font-size: 14px; 
            color: #666; 
            margin-bottom: 10px; 
        }
        .company-details { 
            font-size: 11px; 
            color: #666; 
            line-height: 1.4; 
        }
        .receipt-title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #333; 
            margin-bottom: 10px; 
        }
        .receipt-number { 
            font-size: 16px; 
            font-weight: bold; 
            color: #2563eb; 
            margin-bottom: 5px; 
        }
        .section { 
            margin-bottom: 25px; 
        }
        .section-title { 
            font-size: 16px; 
            font-weight: bold; 
            color: #333; 
            margin-bottom: 15px; 
            border-bottom: 1px solid #ddd; 
            padding-bottom: 5px; 
        }
        .info-grid { 
            display: table; 
            width: 100%; 
            margin-bottom: 20px; 
        }
        .info-row { 
            display: table-row; 
        }
        .info-cell { 
            display: table-cell; 
            padding: 8px 15px; 
            border: 1px solid #ddd; 
            vertical-align: top; 
        }
        .info-label { 
            font-weight: bold; 
            background-color: #f8f9fa; 
            width: 25%; 
        }
        .info-value { 
            width: 75%; 
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .table th, .table td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        .table th { 
            background-color: #f8f9fa; 
            font-weight: bold; 
            font-size: 11px; 
        }
        .table td { 
            font-size: 11px; 
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }
        .total-row { 
            background-color: #f8f9fa; 
            font-weight: bold; 
        }
        .balance-highlight { 
            background-color: #fef2f2; 
            color: #dc2626; 
            font-weight: bold; 
        }
        .terms { 
            margin-top: 30px; 
            font-size: 10px; 
            color: #666; 
            line-height: 1.4; 
        }
        .terms-title { 
            font-weight: bold; 
            margin-bottom: 10px; 
            color: #333; 
        }
        .terms-list { 
            margin: 0; 
            padding-left: 20px; 
        }
        .terms-list li { 
            margin-bottom: 5px; 
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">Mentors Eduserv™</div>
            <div class="company-tagline">JEE | AIIMS | NEET | NTSE | KVPY | OLYMPIADS Get Started...</div>
            <div class="company-details">
                GST Regn No.: 10ADFPJ1214M1Z3<br>
                Service Type.: Commercial coaching & Training<br>
                SAC.: <br>
                Contact No.: 8709833138<br>
                Address: PURNIA<br>
                State Code: 10<br>
                Place of Supply: BIHAR
            </div>
        </div>
        <div class="receipt-info">
            <div class="receipt-title">FEE STRUCTURE</div>
            <div class="receipt-number">{{ $tx->receipt_number ?? 'TX-'.$tx->id }}</div>
            <div style="font-size: 11px; color: #666;">
                Date: {{ $tx->date?->format('d-M-Y') }}<br>
                Status: <span style="color: #059669; font-weight: bold;">SUCCESS</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Account Overview -->
    <div class="section">
        <div class="section-title">ACCOUNT OVERVIEW</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Name</div>
                <div class="info-cell info-value">{{ safe($student->name ?? '—') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Father's Name</div>
                <div class="info-cell info-value">{{ safe($student->father_name ?? '—') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Roll No</div>
                <div class="info-cell info-value">{{ safe($student->roll_no ?? '—') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">UID</div>
                <div class="info-cell info-value">{{ safe($student->student_uid ?? '—') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Plan Name</div>
                <div class="info-cell info-value">PLAN 1</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Address</div>
                <div class="info-cell info-value">{{ safe($student->address ?? '—') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Admission Date</div>
                <div class="info-cell info-value">{{ $admission->created_at?->format('d-M-Y') ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Mother's Name</div>
                <div class="info-cell info-value">{{ safe($student->mother_name ?? '—') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Course</div>
                <div class="info-cell info-value">{{ safe($course->name ?? '—') }} # {{ safe($batch->batch_name ?? '—') }} # ({{ date('Y') }}-{{ date('Y')+1 }})</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Batch</div>
                <div class="info-cell info-value">{{ safe($batch->batch_name ?? '—') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Status</div>
                <div class="info-cell info-value">{{ safe($admission->status ?? 'active') }}</div>
        </div>
        </div>
    </div>

    <!-- Fee Details -->
    <div class="section">
        <div class="section-title">FEE DETAILS</div>
        <table class="table">
        <thead>
        <tr>
            <th>Description</th>
                    <th class="text-right">Amount (₹)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
                    <td>Gross Fee</td>
                    <td class="text-right">{{ number_format($course->gross_fee ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Discount</td>
                    <td class="text-right text-red">-{{ number_format($admission->discount ?? 0, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Fee after Discount</td>
                    <td class="text-right">{{ number_format(($course->gross_fee ?? 0) - ($admission->discount ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>Taxable Fee</td>
                    <td class="text-right">{{ number_format(($course->gross_fee ?? 0) - ($admission->discount ?? 0), 2) }}</td>
        </tr>
        <tr>
                    <td>Tax* (18%)</td>
                    <td class="text-right text-blue">{{ number_format($tx->gst ?? 0, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Payable Fee With Tax</td>
                    <td class="text-right">{{ number_format((($course->gross_fee ?? 0) - ($admission->discount ?? 0)) + ($tx->gst ?? 0), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Breakdown -->
    <div class="section">
        <div class="section-title">PAYMENT BREAKDOWN</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Fee Component</th>
                    <th class="text-right">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @if($course->tution_fee > 0)
                <tr>
                    <td>Tuition Fee</td>
                    <td class="text-right">{{ number_format($course->tution_fee, 2) }}</td>
                </tr>
                @endif
                @if($course->admission_fee > 0)
                <tr>
                    <td>Admission Fee</td>
                    <td class="text-right">{{ number_format($course->admission_fee, 2) }}</td>
                </tr>
                @endif
                @if($course->exam_fee > 0)
                <tr>
                    <td>Exam Fee</td>
                    <td class="text-right">{{ number_format($course->exam_fee, 2) }}</td>
                </tr>
                @endif
                @if($course->infra_fee > 0)
                <tr>
                    <td>Infrastructure Fee</td>
                    <td class="text-right">{{ number_format($course->infra_fee, 2) }}</td>
                </tr>
                @endif
                @if($course->SM_fee > 0)
                <tr>
                    <td>Study Material Fee</td>
                    <td class="text-right">{{ number_format($course->SM_fee, 2) }}</td>
                </tr>
                @endif
                @if($course->tech_fee > 0)
                <tr>
                    <td>Technology Fee</td>
                    <td class="text-right">{{ number_format($course->tech_fee, 2) }}</td>
                </tr>
                @endif
                @if($course->other_fee > 0)
                <tr>
                    <td>Other Fee</td>
                    <td class="text-right">{{ number_format($course->other_fee, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td><strong>Subtotal</strong></td>
                    <td class="text-right"><strong>{{ number_format($course->gross_fee ?? 0, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Discount Applied</td>
                    <td class="text-right text-red">-{{ number_format($admission->discount ?? 0, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Net Fee</strong></td>
                    <td class="text-right"><strong>{{ number_format(($course->gross_fee ?? 0) - ($admission->discount ?? 0), 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Installment Schedule -->
    <div class="section">
        <div class="section-title">INSTALLMENT DETAILS</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Installment Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Installment Amount</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Paid Amount</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalInstallment = 0;
                    $totalPaid = 0;
                    $totalBalance = 0;
                    $schedulesToShow = !empty($allPaymentSchedules) ? $allPaymentSchedules : ($admission->schedules ?? []);
                @endphp
                @foreach($schedulesToShow as $schedule)
                    @php
                        $installmentAmount = (float)$schedule->amount;
                        $paidAmount = (float)$schedule->paid_amount;
                        $balance = max(0, $installmentAmount - $paidAmount);
                        
                        $totalInstallment += $installmentAmount;
                        $totalPaid += $paidAmount;
                        $totalBalance += $balance;
                        
                        // Determine row styling based on payment status
                        $rowStyle = '';
                        if ($paidAmount >= $installmentAmount) {
                            $rowStyle = 'background-color: #e8f5e8;'; // Green for fully paid
                        } elseif ($paidAmount > 0) {
                            $rowStyle = 'background-color: #fff3cd;'; // Yellow for partial
                        }
                    @endphp
                    <tr style="{{ $rowStyle }}">
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            {{ $schedule->due_date?->format('d-M-Y') ?? '—' }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            {{ number_format($installmentAmount, 2) }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            {{ number_format($paidAmount, 2) }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right; color: {{ $balance > 0 ? '#b91c1c' : '#046c4e' }};">
                            {{ number_format($balance, 2) }}
                        </td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f5f5f5;">
                    <td style="border: 1px solid #ddd; padding: 8px;">Total</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        {{ number_format($totalInstallment, 2) }}
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        {{ number_format($totalPaid, 2) }}
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right; color: {{ $totalBalance > 0 ? '#b91c1c' : '#046c4e' }};">
                        {{ number_format($totalBalance, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Transaction Details -->
    <div class="section">
        <div class="section-title">TRANSACTION DETAILS</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Amount Paid</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Inst Due date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Receipt No.</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Mode</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Details</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Chq Status</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Comment</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPaid = 0;
                    $transactionsToShow = $consolidatedTransactions;
                @endphp
                @foreach($transactionsToShow as $transaction)
                    @php
                        $totalPaid += (float)$transaction->amount;
                    @endphp
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            {{ $transaction->date?->format('d-M-Y') ?? '—' }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            {{ number_format($transaction->amount, 2) }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            {{ $transaction->schedule?->due_date?->format('d-M-Y') ?? '—' }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $transaction->receipt_number ?? '—' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ strtoupper($transaction->mode ?? '—') }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            @if($transaction->mode === 'cheque')
                                CHQ NO: {{ $transaction->reference_no ?? '—' }} DT: {{ $transaction->date?->format('Y-m-d') ?? '—' }}
                            @elseif($transaction->mode === 'online')
                                UTR {{ $transaction->reference_no ?? '—' }}
                            @else
                                {{ $transaction->reference_no ?? '—' }}
                            @endif
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            @if($transaction->mode === 'cheque')
                                {{ strtoupper($transaction->status ?? 'PENDING') }}
                            @else
                                {{ strtoupper($transaction->status ?? 'SUCCESS') }}
                            @endif
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $transaction->remarks ?? '—' }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f5f5f5;">
                    <td style="border: 1px solid #ddd; padding: 8px;">Total</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        {{ number_format($totalPaid, 2) }}
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;" colspan="6"></td>
                </tr>
            </tbody>
        </table>
    </div>    
        @if($isConsolidatedReceipt ?? false)
            <div style="margin-top: 15px; padding: 10px; background-color: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 5px;">
                <strong style="color: #0369a1;">Consolidated Receipt:</strong> 
                <span style="color: #0369a1;">This receipt covers {{ $consolidatedTransactions->count() }} installment payments made together on {{ $tx->date?->format('d-M-Y') }}.</span>
            </div>
        @endif

    <!-- Other Transaction Details -->
    <div class="section">
        <div class="section-title">OTHER TRANSACTION DETAILS</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Receipt No.</th>
                    <th class="text-right">Amount Paid</th>
                    <th>Received In</th>
                    <th>For</th>
                    <th>Details</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="text-center" style="color: #999;">No additional transactions</td>
        </tr>
            </tbody>
    </table>
    </div>

    <!-- Terms & Conditions -->
    <div class="terms">
        <div class="terms-title">TERMS & CONDITIONS:</div>
        <ol class="terms-list">
            <li>Tax will be charged as per applicable rate of payment date.</li>
            <li>Cheque/Draft is subject to Realization.</li>
            <li>In case of cheque dishonor, bank charges of Rs. 500 and late fine (upto Rs. 50/day) will be charged.</li>
            <li>Fee once paid will not be refunded/adjusted at any stage, under any circumstance.</li>
            <li>Subject to Patna Jurisdiction.</li>
        </ol>
    </div>
</div>
</body>
</html>
