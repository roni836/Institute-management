<!doctype html>
<html>
  <body style="font-family:Arial, sans-serif; color:#111; font-size:14px;">
    <p>Dear {{ $transaction->admission?->student?->name ?? 'Student' }},</p>
    <p>Please find attached your payment receipt <strong>#TX-{{ $transaction->id }}</strong>
       from <strong>Antra Institutions</strong>.</p>
    @if($transaction->gst > 0)
        <p><strong>Payment Details:</strong><br>
        Base Amount: ₹{{ number_format($transaction->amount, 2) }}<br>
        GST (18%): ₹{{ number_format($transaction->gst, 2) }}<br>
        Total Amount: ₹{{ number_format($transaction->amount + $transaction->gst, 2) }}</p>
    @endif
    <p>Thank you.</p>
    <p style="color:#555; font-size:12px;">
        Antra Institutions<br>
        GST: 5451515121 | Contact: 615112123 | Address: abcd
    </p>
  </body>
</html>
