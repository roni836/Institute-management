<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $transaction->receipt_number ?? 'TX-'.$transaction->id }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            color: #111; 
            font-size: 14px; 
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #333; 
            padding-bottom: 20px; 
            margin-bottom: 25px; 
        }
        .company-name { 
            font-size: 28px; 
            font-weight: bold; 
            color: #333; 
            margin-bottom: 10px; 
        }
        .company-tagline { 
            font-size: 16px; 
            color: #666; 
            margin-bottom: 15px; 
        }
        .receipt-title { 
            font-size: 20px; 
            font-weight: bold; 
            color: #333; 
            margin-bottom: 10px; 
        }
        .receipt-number { 
            font-size: 18px; 
            font-weight: bold; 
            color: #2563eb; 
            margin-bottom: 15px; 
        }
        .section { 
            margin-bottom: 25px; 
        }
        .section-title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #333; 
            margin-bottom: 15px; 
            border-bottom: 1px solid #ddd; 
            padding-bottom: 8px; 
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
            padding: 10px; 
            border: 1px solid #ddd; 
            vertical-align: top; 
        }
        .info-label { 
            font-weight: bold; 
            background-color: #f8f9fa; 
            width: 30%; 
        }
        .info-value { 
            width: 70%; 
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .table th, .table td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: left; 
        }
        .table th { 
            background-color: #f8f9fa; 
            font-weight: bold; 
            font-size: 12px; 
        }
        .table td { 
            font-size: 12px; 
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
        .footer { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #ddd; 
            text-align: center; 
            color: #666; 
            font-size: 12px; 
        }
        .highlight-box { 
            background-color: #f0f9ff; 
            border: 1px solid #0ea5e9; 
            border-radius: 6px; 
            padding: 15px; 
            margin: 20px 0; 
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="company-name">Mentors Eduserv™</div>
        <div class="company-tagline">JEE | AIIMS | NEET | NTSE | KVPY | OLYMPIADS Get Started...</div>
        <div class="receipt-title">PAYMENT RECEIPT</div>
        <div class="receipt-number">{{ $transaction->receipt_number ?? 'TX-'.$transaction->id }}</div>
        <div style="font-size: 14px; color: #666;">
            Date: {{ $transaction->date?->format('d-M-Y') }}<br>
            Status: <span style="color: #059669; font-weight: bold;">SUCCESS</span>
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="highlight-box">
        <h3 style="margin: 0 0 10px 0; color: #0c4a6e;">Payment Summary</h3>
        <p style="margin: 5px 0;"><strong>Student:</strong> {{ $transaction->admission?->student?->name ?? 'Student' }}</p>
        <p style="margin: 5px 0;"><strong>Amount Paid:</strong> ₹{{ number_format($transaction->amount, 2) }}</p>
        @if($transaction->gst > 0)
            <p style="margin: 5px 0;"><strong>GST (18%):</strong> ₹{{ number_format($transaction->gst, 2) }}</p>
            <p style="margin: 5px 0;"><strong>Total Amount:</strong> ₹{{ number_format($transaction->amount + $transaction->gst, 2) }}</p>
        @endif
        <p style="margin: 5px 0;"><strong>Payment Mode:</strong> {{ strtoupper($transaction->mode ?? '—') }}</p>
        @if($transaction->reference_no)
            <p style="margin: 5px 0;"><strong>Reference:</strong> {{ $transaction->reference_no }}</p>
        @endif
    </div>

    <!-- Account Overview -->
    <div class="section">
        <div class="section-title">ACCOUNT OVERVIEW</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Student Name</div>
                <div class="info-cell info-value">{{ $transaction->admission?->student?->name ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Roll No</div>
                <div class="info-cell info-value">{{ $transaction->admission?->student?->roll_no ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Batch</div>
                <div class="info-cell info-value">{{ $transaction->admission?->batch?->batch_name ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Course</div>
                <div class="info-cell info-value">{{ $transaction->admission?->batch?->course?->name ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Admission Date</div>
                <div class="info-cell info-value">{{ $transaction->admission?->created_at?->format('d-M-Y') ?? '—' }}</div>
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
                    <td class="text-right">{{ number_format($transaction->admission?->total_fee ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Discount</td>
                    <td class="text-right">{{ number_format($transaction->admission?->discount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Fee after Discount</td>
                    <td class="text-right">{{ number_format(($transaction->admission?->total_fee ?? 0) - ($transaction->admission?->discount ?? 0), 2) }}</td>
                </tr>
                @if($transaction->gst > 0)
                    <tr>
                        <td>Tax (GST)</td>
                        <td class="text-right text-blue">{{ number_format($transaction->gst, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Total Payable Fee</td>
                    <td class="text-right">{{ number_format((($transaction->admission?->total_fee ?? 0) - ($transaction->admission?->discount ?? 0)) + ($transaction->gst ?? 0), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Installment Details -->
    <div class="section">
        <div class="section-title">INSTALLMENT DETAILS</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Due Date</th>
                    <th class="text-right">Instalment Amount</th>
                    <th class="text-right">Paid Amount</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalInstalment = 0;
                    $totalPaid = 0;
                    $totalBalance = 0;
                @endphp
                @foreach($transaction->admission?->schedules ?? [] as $schedule)
                    @php
                        $instalmentAmount = (float) $schedule->amount;
                        $paidAmount = (float) $schedule->paid_amount;
                        $balance = max(0, $instalmentAmount - $paidAmount);
                        
                        $totalInstalment += $instalmentAmount;
                        $totalPaid += $paidAmount;
                        $totalBalance += $balance;
                    @endphp
                    <tr>
                        <td>{{ $schedule->due_date?->format('d-M-Y') ?? '—' }}</td>
                        <td class="text-right">{{ number_format($instalmentAmount, 2) }}</td>
                        <td class="text-right">{{ number_format($paidAmount, 2) }}</td>
                        <td class="text-right {{ $balance > 0 ? 'text-red' : 'text-green' }}">
                            {{ $balance > 0 ? number_format($balance, 2) : '0.00' }}
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalInstalment, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalPaid, 2) }}</strong></td>
                    <td class="text-right balance-highlight"><strong>{{ number_format($totalBalance, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Transaction Details -->
    <div class="section">
        <div class="section-title">TRANSACTION DETAILS</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Amount Paid</th>
                    <th>Receipt No.</th>
                    <th>Mode</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPaid = 0;
                @endphp
                @foreach($transaction->admission?->transactions ?? [] as $tx)
                    @php
                        $totalPaid += (float) $tx->amount;
                    @endphp
                    <tr>
                        <td>{{ $tx->date?->format('d-M-Y') ?? '—' }}</td>
                        <td class="text-right">{{ number_format($tx->amount, 2) }}</td>
                        <td>{{ $tx->receipt_number ?? '—' }}</td>
                        <td>{{ strtoupper($tx->mode ?? '—') }}</td>
                        <td>
                            @if($tx->mode === 'cheque')
                                CHQ NO: {{ $tx->reference_no ?? '—' }} DT: {{ $tx->date?->format('Y-m-d') ?? '—' }}
                            @elseif($tx->mode === 'online')
                                UTR {{ $tx->reference_no ?? '—' }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="1"><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalPaid, 2) }}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Terms & Conditions -->
    <div class="section">
        <div class="section-title">TERMS & CONDITIONS</div>
        <ol style="margin: 0; padding-left: 20px; color: #666; font-size: 12px; line-height: 1.6;">
            <li>Tax will be charged as per applicable rate of payment date.</li>
            <li>Cheque/Draft is subject to Realization.</li>
            <li>In case of cheque dishonor, bank charges of Rs. 500 and late fine (upto Rs. 50/day) will be charged.</li>
            <li>Fee once paid will not be refunded/adjusted at any stage, under any circumstance.</li>
            <li>Subject to Patna Jurisdiction.</li>
        </ol>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Mentors Eduserv™</strong></p>
        <p>GST Regn No.: 10ADFPJ1214M1Z3 | Contact: 8709833138 | Address: PURNIA</p>
        <p>This is a computer-generated receipt. Thank you for choosing Mentors Eduserv™.</p>
    </div>
</div>
  </body>
</html>
