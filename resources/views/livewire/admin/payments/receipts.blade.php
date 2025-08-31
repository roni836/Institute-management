<div class="max-w-4xl mx-auto p-6">
    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center gap-3 mb-4 no-print">
        <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 border rounded">Back</a>

        <button wire:click="downloadPdf"
                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            Download PDF
        </button>

        <button onclick="window.print()"
                class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
            Print
        </button>

        <button wire:click="openEmailModal"
                class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
            Email PDF
        </button>
    </div>

    {{-- Receipt Card --}}
    <div id="receipt" class="bg-white shadow rounded-lg p-6 print:p-0 print:shadow-none">
        <div class="flex items-start justify-between border-b pb-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold">Antra Institutions</h1>
                <div class="text-sm text-gray-600 mt-1">
                    GST: 5451515121<br>
                    Contact: 615112123<br>
                    Address: abcd
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Receipt No.</div>
                <div class="text-lg font-semibold">TX-{{ $tx->id }}</div>
                <div class="text-sm mt-2 text-gray-700">
                    Date: {{ $tx->date?->format('d-M-Y') }}
                </div>
                <div class="text-sm text-gray-700">
                    Status:
                    <span class="@class([
                        'px-2 py-1 rounded text-xs',
                        'bg-green-100 text-green-700' => $tx->status === 'success',
                        'bg-yellow-100 text-yellow-800' => $tx->status === 'pending',
                        'bg-red-100 text-red-700' => $tx->status === 'failed',
                    ])">
                        {{ ucfirst($tx->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded p-4">
                <div class="text-xs text-gray-500 uppercase tracking-wider">Student</div>
                <div class="font-medium">
                    {{ $tx->admission?->student?->name ?? '—' }}
                </div>
                <div class="text-sm text-gray-600">
                    Admission #{{ $tx->admission_id }}
                </div>
            </div>
            <div class="bg-gray-50 rounded p-4">
                <div class="text-xs text-gray-500 uppercase tracking-wider">Batch</div>
                <div class="font-medium">
                    {{ $tx->admission?->batch?->batch_name ?? '—' }}
                </div>
                @if($tx->schedule)
                    <div class="mt-1 text-sm text-gray-600">
                        Installment #{{ $tx->schedule->installment_no }}
                        (Due {{ $tx->schedule->due_date?->format('d-M-Y') }})
                    </div>
                @endif
            </div>
        </div>

        @php
            // Approximate "due before" and "after" for display (based on current DB state)
            $afterDue = (float)($tx->admission->fee_due ?? 0);
            $paid     = (float)$tx->amount;
            $beforeDue = in_array($tx->status, ['success','pending']) ? $afterDue + $paid : $afterDue;
            $amountInWords = function($n) {
                $fmt = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
                return strtoupper($fmt->format((int)$n)) . ' RUPEES' . (fmod($n,1) ? ' AND ' . strtoupper($fmt->format((int)round(fmod($n,1)*100))) . ' PAISA' : '') . ' ONLY';
            };
        @endphp

        <div class="overflow-x-auto">
            <table class="min-w-full border rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-3">Description</th>
                        <th class="text-left p-3">Mode</th>
                        <th class="text-left p-3">Reference</th>
                        <th class="text-right p-3">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="p-3">
                            Tuition Fee Payment
                            @if($tx->schedule)
                                — Installment #{{ $tx->schedule->installment_no }}
                            @endif
                        </td>
                        <td class="p-3 capitalize">{{ $tx->mode }}</td>
                        <td class="p-3">{{ $tx->reference_no ?? '—' }}</td>
                        <td class="p-3 text-right font-semibold">{{ number_format($tx->amount, 2) }}</td>
                    </tr>
                    @if($tx->gst > 0)
                        <tr class="border-t">
                            <td class="p-3" colspan="3">
                                <span class="text-blue-600">GST (18%)</span>
                            </td>
                            <td class="p-3 text-right font-semibold text-blue-600">{{ number_format($tx->gst, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="p-3 text-right text-sm text-gray-600">Due before this payment</td>
                        <td class="p-3 text-right text-sm text-gray-800">₹ {{ number_format($beforeDue, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="p-3 text-right text-sm text-gray-600">Paid now</td>
                        <td class="p-3 text-right text-sm text-gray-800">₹ {{ number_format($paid, 2) }}</td>
                    </tr>
                    @if($tx->gst > 0)
                        <tr>
                            <td colspan="3" class="p-3 text-right text-sm text-gray-600">Total (including GST)</td>
                            <td class="p-3 text-right text-sm text-gray-800 font-medium">₹ {{ number_format($paid + $tx->gst, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="p-3 text-right font-semibold">Balance due</td>
                        <td class="p-3 text-right font-semibold">₹ {{ number_format($afterDue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-4 text-sm text-gray-700">
            <span class="font-medium">Amount in words:</span>
            {{-- Fallback if intl extension missing --}}
            @php
                $totalAmount = $tx->amount + $tx->gst;
                try {
                    $words = (new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format((int)$totalAmount);
                    $words = strtoupper($words).' RUPEES ONLY';
                } catch (\Throwable $e) {
                    $words = '';
                }
            @endphp
            {{ $words ?: '—' }}
        </div>

        <div class="mt-8 text-xs text-gray-500 border-t pt-3">
            This is a computer-generated receipt. No signature required.
            Thank you for choosing Antra Institutions.
        </div>
    </div>

    {{-- Email Modal --}}
    @if($showEmailModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Send Receipt via Email</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Recipient Email</label>
                    <input type="email" wire:model.defer="email"
                           class="border rounded px-3 py-2 w-full" placeholder="student@example.com">
                    @error('email') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button class="px-4 py-2 border rounded" wire:click="closeEmailModal">Cancel</button>
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700"
                            wire:click="sendEmail">Send</button>
                </div>
            </div>
        </div>
    @endif

    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</div>
