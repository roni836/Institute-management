@php
    $tx = $tx ?? null;
    $admission = $tx->admission;
    $student   = $admission?->student;
    $batch     = $admission?->batch;

    $afterDue  = (float)($admission->fee_due ?? 0);
    $paid      = (float)$tx->amount;
    $beforeDue = in_array($tx->status, ['success','pending']) ? $afterDue + $paid : $afterDue;

    function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt TX-{{ $tx->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111; font-size: 12px; }
        .wrap { width: 100%; max-width: 750px; margin: 0 auto; }
        .row { display: flex; justify-content: space-between; align-items: flex-start; }
        .mt-8 { margin-top: 24px; } .mt-4 { margin-top: 12px; } .mb-6 { margin-bottom: 18px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
        .muted { color: #6b7280; } .bold { font-weight: 700; } .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        thead th { background: #f3f4f6; text-align: left; }
        tfoot td { background: #f9fafb; }
        .badge { display:inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; }
        .success { background:#dcfce7; color:#166534; }
        .pending { background:#fef9c3; color:#854d0e; }
        .failed  { background:#fee2e2; color:#991b1b; }
        .footnote { color:#6b7280; font-size: 10px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="row" style="border-bottom:1px solid #e5e7eb; padding-bottom:12px; margin-bottom:12px;">
        <div>
            <div style="font-size:20px;" class="bold">{{ safe($org['name']) }}</div>
            <div class="muted">GST: {{ safe($org['gst']) }}</div>
            <div class="muted">Contact: {{ safe($org['contact']) }}</div>
            <div class="muted">Address: {{ safe($org['address']) }}</div>
        </div>
        <div class="right">
            <div class="muted" style="font-size:11px;">Receipt No.</div>
            <div class="bold">TX-{{ $tx->id }}</div>
            <div class="muted mt-4">Date: {{ $tx->date?->format('d-M-Y') }}</div>
            @php $st = $tx->status; @endphp
            <div class="mt-4">
                <span class="badge {{ $st==='success'?'success':($st==='pending'?'pending':'failed') }}">
                    {{ strtoupper($st) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row mb-6">
        <div class="card" style="width:48%;">
            <div class="muted" style="text-transform:uppercase; font-size:10px;">Student</div>
            <div class="bold">{{ safe($student->name ?? '—') }}</div>
            <div class="muted">Admission #{{ $tx->admission_id }}</div>
        </div>
        <div class="card" style="width:48%;">
            <div class="muted" style="text-transform:uppercase; font-size:10px;">Batch</div>
            <div class="bold">{{ safe($batch->batch_name ?? '—') }}</div>
            @if($tx->schedule)
                <div class="muted">Installment #{{ $tx->schedule->installment_no }}
                    (Due {{ $tx->schedule->due_date?->format('d-M-Y') }})</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th>Mode</th>
            <th>Reference</th>
            <th class="right">Amount (₹)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Tuition Fee Payment @if($tx->schedule) — Installment #{{ $tx->schedule->installment_no }} @endif</td>
            <td style="text-transform:capitalize;">{{ safe($tx->mode) }}</td>
            <td>{{ safe($tx->reference_no ?? '—') }}</td>
            <td class="right"><span class="bold">{{ number_format($tx->amount, 2) }}</span></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="right muted">Due before this payment</td>
            <td class="right">₹ {{ number_format($beforeDue, 2) }}</td>
        </tr>
        <tr>
            <td colspan="3" class="right muted">Paid now</td>
            <td class="right">₹ {{ number_format($paid, 2) }}</td>
        </tr>
        <tr>
            <td colspan="3" class="right bold">Balance due</td>
            <td class="right bold">₹ {{ number_format($afterDue, 2) }}</td>
        </tr>
        </tfoot>
    </table>

    <div class="mt-8 footnote">
        This is a computer-generated receipt. Thank you for choosing {{ safe($org['name']) }}.
    </div>
</div>
</body>
</html>
