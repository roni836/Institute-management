<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Receipt' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <style>
        /* Minimal print-focused styles */
        html,body { margin:0; padding:10px; font-family: Arial, Helvetica, sans-serif; color:#000; }
        a { color:inherit; text-decoration:none }
        .receipt-printable { width:100%; max-width:800px; margin:0 auto; }
        @media print { a { color: #000; text-decoration:none } }
    </style>
    @livewireStyles
</head>
<body>
    <div class="receipt-printable">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
