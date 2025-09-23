<div class="p-4 md:p-6 space-y-6">
    {{-- Header --}}
    <div class="bg-white border rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Payment</h1>
                <p class="text-gray-600">Manage payment schedules and transactions for {{ $admission->student->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" wire:click="toggleEditMode" 
                    class="px-4 py-2 rounded-lg border {{ $editMode ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-700 border-gray-200' }}">
                    {{ $editMode ? 'Lock Editing' : 'Enable Editing' }}
                </button>
                <a href="{{ route('admin.admissions.index') }}" 
                    class="px-4 py-2 rounded-lg border bg-gray-50 text-gray-700">
                    Back to Admissions
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Student & Admission Info --}}
        <div class="bg-white border rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-4">Student Information</h3>
            
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium">{{ $admission->student->name }}</p>
                        <p class="text-sm text-gray-600">{{ $admission->student->roll_no }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Course</p>
                        <p class="font-medium">{{ $admission->batch->course->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Batch</p>
                        <p class="font-medium">{{ $admission->batch->batch_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Fee</p>
                        <p class="font-medium text-lg">₹{{ number_format($admission->fee_total, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Due Amount</p>
                        <p class="font-medium text-lg {{ $admission->fee_due > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₹{{ number_format($admission->fee_due, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Schedules --}}
        <div class="lg:col-span-2 bg-white border rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Payment Schedules</h3>
                @if ($editMode)
                    <button type="button" wire:click="addNewSchedule" 
                        class="px-3 py-1 text-sm rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                        + Add Schedule
                    </button>
                @endif
            </div>

            <div class="space-y-4">
                @forelse ($schedules as $schedule)
                    <div class="border rounded-lg p-4 {{ $selectedScheduleId == $schedule['id'] ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                        @if ($selectedScheduleId == $schedule['id'])
                            {{-- Edit Form --}}
                            <form wire:submit.prevent="updateSchedule" class="space-y-4">
                                <div class="grid grid-cols-4 gap-4">
                                    <div>
                                        <label class="text-xs text-gray-600">Installment #</label>
                                        <input type="number" min="1" 
                                            class="w-full border rounded p-2 text-sm" 
                                            wire:model="editingSchedule.installment_no">
                                        @error('editingSchedule.installment_no')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600">Amount (₹)</label>
                                        <input type="number" step="0.01" min="0" 
                                            class="w-full border rounded p-2 text-sm" 
                                            wire:model="editingSchedule.amount">
                                        @error('editingSchedule.amount')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600">Due Date</label>
                                        <input type="date" 
                                            class="w-full border rounded p-2 text-sm" 
                                            wire:model="editingSchedule.due_date">
                                        @error('editingSchedule.due_date')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex items-end gap-2">
                                        <button type="submit" 
                                            class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg">
                                            Save
                                        </button>
                                        <button type="button" wire:click="resetEditingForms" 
                                            class="px-3 py-2 text-sm border rounded-lg">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            {{-- Display Mode --}}
                            <div class="flex items-center justify-between">
                                <div class="grid grid-cols-4 gap-4 flex-1">
                                    <div>
                                        <p class="text-xs text-gray-600">Installment</p>
                                        <p class="font-medium">#{{ $schedule['installment_no'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Amount</p>
                                        <p class="font-medium">₹{{ number_format($schedule['amount'], 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Due Date</p>
                                        <p class="font-medium">{{ \Carbon\Carbon::parse($schedule['due_date'])->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Status</p>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $schedule['status'] === 'paid' ? 'bg-green-100 text-green-800' : 
                                               ($schedule['status'] === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($schedule['status']) }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if ($editMode)
                                    <div class="flex items-center gap-2 ml-4">
                                        <button type="button" wire:click="editSchedule({{ $schedule['id'] }})" 
                                            class="p-1 text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        @if (count($schedule['transactions']) === 0)
                                            <button type="button" wire:click="deleteSchedule({{ $schedule['id'] }})" 
                                                onclick="return confirm('Are you sure you want to delete this payment schedule?')"
                                                class="p-1 text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Progress Bar --}}
                            @if ($schedule['amount'] > 0)
                                <div class="mt-3">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Paid: ₹{{ number_format($schedule['paid_amount'], 2) }}</span>
                                        <span>{{ number_format(($schedule['paid_amount'] / $schedule['amount']) * 100, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" 
                                            style="width: {{ min(100, ($schedule['paid_amount'] / $schedule['amount']) * 100) }}%"></div>
                                    </div>
                                </div>
                            @endif

                            {{-- Transactions for this schedule --}}
                            @if (count($schedule['transactions']) > 0)
                                <div class="mt-4 border-t pt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Transactions:</p>
                                    <div class="space-y-2">
                                        @foreach ($schedule['transactions'] as $transaction)
                                            <div class="flex items-center justify-between text-sm bg-white rounded p-2 border">
                                                <div class="flex items-center gap-4">
                                                    <span class="font-medium">₹{{ number_format($transaction['amount'], 2) }}</span>
                                                    <span class="text-gray-600">{{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}</span>
                                                    <span class="text-gray-600">{{ $transaction['mode'] }}</span>
                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                        {{ $transaction['status'] === 'success' ? 'bg-green-100 text-green-800' : 
                                                           ($transaction['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ ucfirst($transaction['status']) }}
                                                    </span>
                                                </div>
                                                @if ($editMode)
                                                    <div class="flex items-center gap-1">
                                                        <button type="button" wire:click="editTransaction({{ $transaction['id'] }})" 
                                                            class="p-1 text-blue-600 hover:text-blue-800">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                        <button type="button" wire:click="deleteTransaction({{ $transaction['id'] }})" 
                                                            onclick="return confirm('Are you sure you want to delete this transaction?')"
                                                            class="p-1 text-red-600 hover:text-red-800">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>No payment schedules found.</p>
                        @if ($editMode)
                            <button type="button" wire:click="addNewSchedule" 
                                class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg">
                                Create First Schedule
                            </button>
                        @endif
                    </div>
                @endforelse

                {{-- New Schedule Form --}}
                @if ($selectedScheduleId === 'new')
                    <div class="border rounded-lg p-4 bg-blue-50 border-blue-200">
                        <form wire:submit.prevent="createSchedule" class="space-y-4">
                            <h4 class="font-medium text-blue-900">Add New Payment Schedule</h4>
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="text-xs text-gray-600">Installment #</label>
                                    <input type="number" min="1" 
                                        class="w-full border rounded p-2 text-sm" 
                                        wire:model="editingSchedule.installment_no">
                                    @error('editingSchedule.installment_no')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Amount (₹)</label>
                                    <input type="number" step="0.01" min="0.01" 
                                        class="w-full border rounded p-2 text-sm" 
                                        wire:model="editingSchedule.amount">
                                    @error('editingSchedule.amount')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Due Date</label>
                                    <input type="date" 
                                        class="w-full border rounded p-2 text-sm" 
                                        wire:model="editingSchedule.due_date">
                                    @error('editingSchedule.due_date')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex items-end gap-2">
                                    <button type="submit" 
                                        class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg">
                                        Create
                                    </button>
                                    <button type="button" wire:click="resetEditingForms" 
                                        class="px-3 py-2 text-sm border rounded-lg">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Transaction Edit Modal --}}
    @if ($showTransactionForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">
                    {{ $editingTransactionMode ? 'Edit Transaction' : 'Add Transaction' }}
                </h3>
                
                <form wire:submit.prevent="{{ $editingTransactionMode ? 'updateTransaction' : 'createTransaction' }}" class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-600">Amount (₹)</label>
                        <input type="number" step="0.01" min="0.01" 
                            class="w-full border rounded p-2" 
                            wire:model="editingTransaction.amount">
                        @error('editingTransaction.amount')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="text-sm text-gray-600">Date</label>
                        <input type="date" 
                            class="w-full border rounded p-2" 
                            wire:model="editingTransaction.date">
                        @error('editingTransaction.date')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="text-sm text-gray-600">Payment Mode</label>
                        <select class="w-full border rounded p-2" wire:model="editingTransaction.mode">
                            <option value="">Select Mode</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                        @error('editingTransaction.mode')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="text-sm text-gray-600">Reference Number</label>
                        <input type="text" 
                            class="w-full border rounded p-2" 
                            wire:model="editingTransaction.reference_no">
                    </div>
                    
                    <div>
                        <label class="text-sm text-gray-600">Status</label>
                        <select class="w-full border rounded p-2" wire:model="editingTransaction.status">
                            <option value="success">Success</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                        @error('editingTransaction.status')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center gap-3 pt-4">
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            {{ $editingTransactionMode ? 'Update' : 'Create' }}
                        </button>
                        <button type="button" wire:click="resetEditingForms" 
                            class="px-4 py-2 border rounded-lg">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
