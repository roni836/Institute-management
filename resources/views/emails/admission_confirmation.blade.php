<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Confirmation - Antra Institute</title>
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
        .welcome-title { 
            font-size: 24px; 
            font-weight: bold; 
            color: #2563eb; 
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
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
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
        .cta-button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">Antra Institute</div>
            <div class="company-tagline">Empowering Education, Building Futures</div>
        </div>

        <div class="welcome-title">🎉 Welcome to Antra Institute! 🎉</div>

        <div class="section">
            <p>Dear <strong>{{ $admission->student->name }}</strong>,</p>
            
            <p>Congratulations! Your admission to <strong>Antra Institute</strong> has been successfully confirmed. We're excited to have you as part of our learning community.</p>
        </div>

        <div class="section">
            <div class="section-title">Admission Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell info-label">Student Name:</div>
                    <div class="info-cell info-value">{{ $admission->student->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Roll Number:</div>
                    <div class="info-cell info-value">{{ $admission->student->roll_no }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Student UID:</div>
                    <div class="info-cell info-value">{{ $admission->student->student_uid }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Course:</div>
                    <div class="info-cell info-value">{{ $admission->batch->course->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Batch:</div>
                    <div class="info-cell info-value">{{ $admission->batch->batch_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Admission Date:</div>
                    <div class="info-cell info-value">{{ $admission->admission_date->format('d M Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Payment Mode:</div>
                    <div class="info-cell info-value">{{ ucfirst($admission->mode) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Total Fee:</div>
                    <div class="info-cell info-value">₹{{ number_format($admission->fee_total, 2) }}</div>
                </div>
                @if($admission->discount > 0)
                <div class="info-row">
                    <div class="info-cell info-label">Discount Applied:</div>
                    <div class="info-cell info-value">₹{{ number_format($admission->discount, 2) }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-cell info-label">Amount Due:</div>
                    <div class="info-cell info-value">₹{{ number_format($admission->fee_due, 2) }}</div>
                </div>
            </div>
        </div>

        @if($admission->mode === 'installment')
        <div class="section">
            <div class="section-title">Payment Schedule</div>
            <p>Your payment has been scheduled in installments. Please ensure timely payment of each installment to avoid any late fees.</p>
            
            <div class="info-grid">
                @foreach($admission->schedules as $schedule)
                <div class="info-row">
                    <div class="info-cell info-label">Installment {{ $schedule->installment_no }}:</div>
                    <div class="info-cell info-value">
                        ₹{{ number_format($schedule->amount, 2) }} - Due: {{ $schedule->due_date->format('d M Y') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="highlight-box">
            <h3 style="margin-top: 0; color: #0ea5e9;">📚 Next Steps:</h3>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Complete your student profile</li>
                <li>Attend orientation session (details will be shared separately)</li>
                <li>Download study materials from student portal</li>
                <li>Join your batch WhatsApp group (invitation will be sent)</li>
            </ul>
        </div>

        <div class="section">
            <div class="section-title">Important Information</div>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Class Timings:</strong> Will be communicated by your batch coordinator</li>
                <li><strong>Attendance:</strong> Minimum 75% attendance is mandatory</li>
                <li><strong>Study Material:</strong> Available in digital format</li>
                <li><strong>Support:</strong> Contact your batch coordinator for any queries</li>
            </ul>
        </div>

        <div class="section">
            <p>If you have any questions or need assistance, please don't hesitate to contact us:</p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Email:</strong> support@antrainstitute.com</li>
                <li><strong>Phone:</strong> +91-XXXXXXXXXX</li>
                <li><strong>Address:</strong> [Your Institute Address]</li>
            </ul>
        </div>

        <div class="footer">
            <p>Thank you for choosing Antra Institute!</p>
            <p>Best regards,<br>The Antra Institute Team</p>
            <p style="font-size: 10px; color: #999;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
