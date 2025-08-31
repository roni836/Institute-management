<div class="max-w-3xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-semibold text-gray-800">Create Payment</h1>

    <!-- Search Student (Only show if no student is pre-selected) -->
    @unless($selectedStudentId)
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
                <label class="block text-sm font-medium mb-2">Link Installments (optional)</label>

                <div class="overflow-x-auto bg-white rounded-lg border">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr class="text-left">
                                <th class="px-3 py-2 w-10">
                                    <!-- spacer for checkbox column -->
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
                                            value="{{ $row['id'] }}" wire:model.live="selectedScheduleIds">
                                    </td>
                                    <td class="px-3 py-2 font-medium">#{{ $row['installment_no'] }}</td>
                                    <td class="px-3 py-2">{{ $row['due_date'] }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($row['amount'], 2) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($row['paid'], 2) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($row['left'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                                        No installments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (!empty($schedules))
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
                    Tip: checking rows auto-fills the <strong>Amount</strong> field below with the total “Left” of
                    selected installments.
                    You can still override it.
                </p>
            </div>
        </div>
    @endif

    <!-- Payment Form -->
    @if ($admission_id)
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
                    <input type="number" step="0.01" wire:model.live="amount"
                        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror">
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- GST Section -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="applyGst" wire:model.live="applyGst" 
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="applyGst" class="text-sm font-medium text-gray-700">
                        Apply 18% GST
                    </label>
                </div>
                
                @if($applyGst)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-white rounded-lg p-3 border">
                            <span class="text-gray-600">Base Amount:</span>
                            <span class="font-medium ml-2">₹{{ number_format($amount, 2) }}</span>
                        </div>
                        <div class="bg-white rounded-lg p-3 border">
                            <span class="text-gray-600">GST (18%):</span>
                            <span class="font-medium ml-2 text-blue-600">₹{{ number_format($gstAmount, 2) }}</span>
                        </div>
                        <div class="bg-white rounded-lg p-3 border">
                            <span class="text-gray-600">Total Amount:</span>
                            <span class="font-medium ml-2 text-green-600">₹{{ number_format($amount + $gstAmount, 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Mode / Reference / Status -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Mode</label>
                    <select wire:model="mode"
                        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Reference No</label>
                    <input type="text" wire:model="reference_no"
                        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                        placeholder="CHQ / UTR">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select wire:model="status"
                        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500">
                        <option value="success">Success</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
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
