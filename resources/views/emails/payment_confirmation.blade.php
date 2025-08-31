<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Confirmation - Antra Institute</title>
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
        .success-title { 
            font-size: 24px; 
            font-weight: bold; 
            color: #059669; 
            margin-bottom: 20px; 
            text-align: center;
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
        .highlight-box {
            background-color: #f0fdf4;
            border: 1px solid #22c55e;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .amount-highlight {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-text {
            font-size: 24px;
            font-weight: bold;
            color: #d97706;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">Antra Institute</div>
            <div class="company-tagline">Empowering Education, Building Futures</div>
        </div>

        <div class="success-title">✅ Payment Confirmed Successfully! ✅</div>

        <div class="section">
            <p>Dear <strong>{{ $transaction->student->name }}</strong>,</p>
            
            <p>Thank you for your payment! Your transaction has been processed successfully. Here are the details of your payment:</p>
        </div>

        <div class="amount-highlight">
            <div class="amount-text">₹{{ number_format($transaction->amount, 2) }}</div>
            <div style="color: #92400e; margin-top: 5px;">Amount Paid</div>
        </div>

        <div class="section">
            <div class="section-title">Payment Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell info-label">Receipt Number:</div>
                    <div class="info-cell info-value">{{ $transaction->receipt_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Transaction Date:</div>
                    <div class="info-cell info-value">{{ $transaction->date->format('d M Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Payment Mode:</div>
                    <div class="info-cell info-value">{{ ucfirst($transaction->mode) }}</div>
                </div>
                @if($transaction->reference_no)
                <div class="info-row">
                    <div class="info-cell info-label">Reference Number:</div>
                    <div class="info-cell info-value">{{ $transaction->reference_no }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-cell info-label">Student Name:</div>
                    <div class="info-cell info-value">{{ $transaction->student->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Roll Number:</div>
                    <div class="info-cell info-value">{{ $transaction->student->roll_no }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Course:</div>
                    <div class="info-cell info-value">{{ $transaction->admission->batch->course->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Batch:</div>
                    <div class="info-cell info-value">{{ $transaction->admission->batch->batch_name }}</div>
                </div>
            </div>
        </div>

        @if($transaction->gst > 0)
        <div class="section">
            <div class="section-title">Tax Breakdown</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell info-label">Base Amount:</div>
                    <div class="info-cell info-value">₹{{ number_format($transaction->amount - $transaction->gst, 2) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">GST (18%):</div>
                    <div class="info-cell info-value">₹{{ number_format($transaction->gst, 2) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Total Amount:</div>
                    <div class="info-cell info-value">₹{{ number_format($transaction->amount, 2) }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($transaction->schedule)
        <div class="section">
            <div class="section-title">Installment Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell info-label">Installment No:</div>
                    <div class="info-cell info-value">{{ $transaction->schedule->installment_no }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Due Date:</div>
                    <div class="info-cell info-value">{{ $transaction->schedule->due_date->format('d M Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Installment Amount:</div>
                    <div class="info-cell info-value">₹{{ number_format($transaction->schedule->amount, 2) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Amount Paid:</div>
                    <div class="info-cell info-value">₹{{ number_format($transaction->schedule->paid_amount, 2) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Remaining Amount:</div>
                    <div class="info-cell info-value">₹{{ number_format(max(0, $transaction->schedule->amount - $transaction->schedule->paid_amount), 2) }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="highlight-box">
            <h3 style="margin-top: 0; color: #16a34a;">📊 Payment Summary:</h3>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Total Course Fee:</strong> ₹{{ number_format($transaction->admission->fee_total, 2) }}</li>
                <li><strong>Total Paid:</strong> ₹{{ number_format($transaction->admission->transactions()->where('status', 'success')->sum('amount'), 2) }}</li>
                <li><strong>Amount Due:</strong> ₹{{ number_format($transaction->admission->fee_due, 2) }}</li>
            </ul>
        </div>

        <div class="section">
            <div class="section-title">What's Next?</div>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Keep this receipt for your records</li>
                <li>Continue attending classes regularly</li>
                <li>Pay remaining installments on time (if applicable)</li>
                <li>Contact us if you have any payment-related queries</li>
            </ul>
        </div>

        <div class="section">
            <p>If you have any questions about this payment or need assistance, please contact us:</p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Email:</strong> accounts@antrainstitute.com</li>
                <li><strong>Phone:</strong> +91-XXXXXXXXXX</li>
                <li><strong>Address:</strong> [Your Institute Address]</li>
            </ul>
        </div>

        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>Best regards,<br>The Antra Institute Team</p>
            <p style="font-size: 10px; color: #999;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
