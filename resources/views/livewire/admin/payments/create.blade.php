<div class="max-w-3xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-semibold text-gray-800">Create Payment</h1>

    <!-- Search Student (Only show if no student is pre-selected) -->
    @unless ($selectedStudentId)
        <div>
            <label class="block text-sm font-medium mb-1">Search Student (Name / Roll No)</label>
            <input type="text" wire:model.live.debounce.300ms="search"
                class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                placeholder="Enter student name or roll number">

            <!-- Loader -->
            <div wire:loading wire:target="search" class="text-xs text-gray-500 mt-1">Searching...</div>

            <!-- Results -->
            @if ($students)
                <div class="border rounded bg-white shadow mt-1 max-h-48 overflow-y-auto divide-y">
                    @foreach ($students as $s)
                        <div wire:click="selectStudent({{ $s['id'] }})"
                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                            {{ $s['label'] }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endunless

    <!-- NEW: Admission selector appears after a student is chosen -->
    @if ($selectedStudentId && $admissions)
        <div>
            <label class="block text-sm font-medium mb-1">Select Admission</label>
            <select wire:model.live="admission_id"
                class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500">
                <option value="">-- Choose an admission --</option>
                @foreach ($admissions as $a)
                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Admission + Installment -->
    @if ($admission_id)
        <div class="space-y-3 text-sm border rounded-lg p-4 bg-gray-50">
            <p class="font-medium">
                Admission Due:
                <span class="font-semibold text-red-600">₹ {{ $admission_fee_due }}</span>
            </p>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium">Link Installments (optional)</label>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="flexiblePayment" wire:model.live="flexiblePayment"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="flexiblePayment" class="text-sm text-gray-700">
                            Flexible Payment Mode
                        </label>
                    </div>
                </div>

                <div class="overflow-x-auto bg-white rounded-lg border">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr class="text-left">
                                <th class="px-3 py-2 w-10">
                                    <div class="text-xs text-gray-500">Select</div>
                                </th>
                                <th class="px-3 py-2">Inst #</th>
                                <th class="px-3 py-2">Due Date</th>
                                <th class="px-3 py-2 text-right">Amount (₹)</th>
                                <th class="px-3 py-2 text-right">Paid (₹)</th>
                                <th class="px-3 py-2 text-right">Left (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($schedules as $row)
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="checkbox" class="rounded border-gray-300"
                                            value="{{ $row['id'] }}" wire:model.live="selectedScheduleIds"
                                            {{ $row['left'] <= 0 ? 'disabled' : '' }} @class([
                                                'opacity-50 cursor-not-allowed' => $row['left'] <= 0,
                                                'cursor-pointer' => $row['left'] > 0,
                                            ])>
                                    </td>
                                    <td class="px-3 py-2 font-medium">#{{ $row['installment_no'] }}</td>
                                    <td class="px-3 py-2">{{ $row['due_date'] }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($row['amount'], 2) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($row['paid'], 2) }}</td>
                                    <td class="px-3 py-2 text-right">
                                        @if ($row['left'] <= 0)
                                            <span class="text-green-600 font-medium">Fully Paid</span>
                                        @else
                                            {{ number_format($row['left'], 2) }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                                        No installments found.
                                    </td>
                                </tr>
                            @endforelse

                            @if (!empty($schedules) && !$this->hasAvailableInstallments)
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-center text-green-600">
                                        <div class="flex items-center justify-center">
                                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            All installments are fully paid!
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        @if (!empty($schedules) && $this->hasAvailableInstallments)
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td class="px-3 py-2"></td>
                                    <td class="px-3 py-2 font-semibold" colspan="4">Total of selected (Left)</td>
                                    <td class="px-3 py-2 text-right font-semibold">
                                        ₹ {{ number_format($this->selected_total, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <div wire:loading wire:target="selectedScheduleIds,admission_id" class="text-xs text-gray-500 mt-2">
                    Updating selection...
                </div>

                <p class="text-xs text-gray-600 mt-2">
                    @if ($this->hasAvailableInstallments)
                        Tip:
                    @else
                        All installments are fully paid. No payment needed.
                    @endif checking rows auto-fills the <strong>Amount</strong> field below with
                    the total “Left” of
                    selected installments.
                    You can still override it.
    @endif
    </p>
    {{-- <p class="text-xs text-gray-500 mt-1">
        <span class="text-green-600">✓</span> Fully paid installments are disabled and cannot be selected.
    </p> --}}
</div>

<!-- Flexible Payment Section -->
@if ($flexiblePayment)
    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h4 class="text-sm font-medium text-blue-800 mb-3">Flexible Payment Mode</h4>

        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-blue-700 mb-1">Payment Amount</label>
                <input type="number" step="0.01" min="0" wire:model.live="flexibleAmount"
                    class="w-full border border-blue-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('flexibleAmount') border-red-500 @enderror"
                    placeholder="Enter payment amount">
                @error('flexibleAmount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if ($flexibleAmount > 0)
                @php
                    $allocation = $this->getSmartAllocationPreview();
                @endphp

                @if (!empty($allocation['allocation']))
                    <div class="bg-white rounded-lg p-3 border border-blue-200">
                        <h5 class="text-sm font-medium text-blue-800 mb-2">Payment Allocation Preview:</h5>
                        <div class="space-y-2">
                            @foreach ($allocation['allocation'] as $item)
                                <div class="flex justify-between text-sm">
                                    <span>Installment #{{ $item['installment_no'] }} (Due:
                                        {{ $item['due_date'] }})</span>
                                    <span class="font-medium">₹{{ number_format($item['amount'], 2) }}</span>
                                </div>
                            @endforeach

                            @if ($allocation['overpayment'] > 0.01)
                                <div class="flex justify-between text-sm text-green-600 border-t pt-2">
                                    <span>Overpayment (for future installments)</span>
                                    <span
                                        class="font-medium">₹{{ number_format($allocation['overpayment'], 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-sm font-medium border-t pt-2">
                                <span>Total Payment</span>
                                <span>₹{{ number_format($flexibleAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endif
</div>

<!-- Payment Form Instructions -->
@if ($admission_id && empty($selectedScheduleIds) && !$flexiblePayment)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Select installments above</strong> or <strong>enable Flexible Payment Mode</strong> to
                    proceed with payment creation.
                </p>
            </div>
        </div>
    </div>
@endif

<!-- Selected Installments Summary -->
@if ($admission_id && !empty($selectedScheduleIds))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ count($selectedScheduleIds) }} installment(s) selected
                    </p>
                    <p class="text-sm text-green-700">
                        Total amount to pay: ₹{{ number_format($this->selected_total, 2) }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Ready to proceed
                </span>
            </div>
        </div>
    </div>
@endif

<!-- Payment Form -->
@if ($admission_id && (!empty($selectedScheduleIds) || $flexiblePayment))
    <form wire:submit.prevent="save" class="space-y-5">

        <!-- Date & Amount -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Date</label>
                <input type="date" wire:model="date"
                    class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 @error('date') border-red-500 @enderror">
                @error('date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Amount</label>
                <input type="number" step="0.01"
                    @if ($flexiblePayment) wire:model.live="flexibleAmount" 
                        value="{{ $flexibleAmount }}"
                    @else 
                        wire:model.live="amount" @endif
                    class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror">
                @error('amount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('flexibleAmount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- GST Section -->
        {{-- <div class="bg-gray-50 rounded-lg p-4 space-y-3">
            <div class="flex items-center space-x-3">
                <input type="checkbox" id="applyGst" wire:model.live="applyGst"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="applyGst" class="text-sm font-medium text-gray-700">
                    Apply 18% GST
                </label>
            </div>

            @if ($applyGst)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-white rounded-lg p-3 border">
                        <span class="text-gray-600">Base Amount:</span>
                        <span
                            class="font-medium ml-2">₹{{ number_format((float) ($flexiblePayment ? $flexibleAmount : $amount), 2) }}</span>
                    </div>
                    <div class="bg-white rounded-lg p-3 border">
                        <span class="text-gray-600">GST (18%):</span>
                        <span class="font-medium ml-2 text-blue-600">₹{{ number_format((float)$gstAmount, 2) }}</span>
                    </div>
                    <div class="bg-white rounded-lg p-3 border">
                        <span class="text-gray-600">Total Amount:</span>
                        <span
                            class="font-medium ml-2 text-green-600">₹{{ number_format((float)($flexiblePayment ? $flexibleAmount : $amount) + $gstAmount, 2) }}</span>
                    </div>
                </div>
            @endif
        </div> --}}

        <!-- Receipt Number Display -->
        {{-- <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-medium text-blue-800 mb-1">Receipt Number</label>
                    <p class="text-lg font-bold text-blue-900">{{ $receipt_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-600">Status</p>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        Success
                    </span>
                </div>
            </div>
        </div> --}}

        <!-- Mode / Reference -->
        <div class="grid grid-cols-1 md:grid-cols-{{ $mode === 'cash' ? '2' : '3' }} gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Mode</label>
                <select wire:model.live="mode"
                    class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500">
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="online">Online</option>
                </select>
            </div>
            @if ($mode !== 'cash')
                <div>
                    <label class="block text-sm font-medium mb-1">Reference No</label>
                    <input type="text" wire:model="reference_no"
                        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                        placeholder="{{ $mode === 'cheque' ? 'CHQ' : 'UTR' }}">
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payments.index') }}"
                class="px-4 py-2 border rounded-lg bg-gray-100 hover:bg-gray-200">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save Payment</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
@endif
</div>
