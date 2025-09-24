<div class="max-w-4xl mx-auto p-4 md:p-6 space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Payment</h1>
            <p class="mt-1 text-sm text-gray-600">Process student payments and generate receipts</p>
        </div>
        <a href="{{ route('admin.payments.index') }}" 
           class="flex items-center gap-2 px-4 py-2 text-sm border rounded-lg hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Payments
        </a>
    </div>

    <!-- Step Indicator -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-6">
        <div class="bg-gray-50 border-b px-6 py-4">
            <div class="flex items-center space-x-4 justify-between max-w-4xl">
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full {{ $selectedStudentId ? 'bg-green-100 text-green-700' : 'bg-primary-100 text-primary-700' }} font-semibold">1</div>
                    <div class="ml-2">
                        <p class="font-medium text-gray-900">Select Student</p>
                        <p class="text-xs text-gray-500">Search and select a student</p>
                    </div>
                </div>
                
                <div class="h-0.5 w-12 bg-gray-200 hidden md:block {{ $selectedStudentId ? 'bg-green-300' : '' }}"></div>
                
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full {{ $admission_id ? 'bg-green-100 text-green-700' : ($selectedStudentId ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-400') }} font-semibold">2</div>
                    <div class="ml-2">
                        <p class="font-medium text-gray-900">Select Admission</p>
                        <p class="text-xs text-gray-500">Choose course admission</p>
                    </div>
                </div>
                
                <div class="h-0.5 w-12 bg-gray-200 hidden md:block {{ $admission_id ? 'bg-green-300' : '' }}"></div>
                
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full {{ $admission_id && (!empty($selectedScheduleIds) || $flexiblePayment) ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-400' }} font-semibold">3</div>
                    <div class="ml-2">
                        <p class="font-medium text-gray-900">Payment Details</p>
                        <p class="text-xs text-gray-500">Process payment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Student (Only show if no student is pre-selected) -->
    @unless ($selectedStudentId)
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Search for Student</h2>
                
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" 
                            wire:model.live.debounce.300ms="search"
                            class="pl-10 border rounded-lg px-3 py-3 w-full focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Enter student name, roll number or phone">
                    </div>
                    
                    <!-- Loader -->
                    <div wire:loading wire:target="search" class="flex items-center text-sm text-primary-600 mt-2">
                        <svg class="animate-spin h-4 w-4 mr-2 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Searching...
                    </div>

                    <!-- Results -->
                    @if ($students)
                        <div class="border rounded-lg bg-white shadow mt-2 max-h-60 overflow-y-auto divide-y">
                            @forelse ($students as $s)
                                <div wire:click="selectStudent({{ $s['id'] }})"
                                    class="px-4 py-3 hover:bg-gray-50 cursor-pointer flex items-center justify-between transition-colors">
                                    <div>
                                        <p class="font-medium">{{ $s['label'] }}</p>
                                    </div>
                                    <span class="text-primary-600 text-sm">Select →</span>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                    <p>No students found matching your search</p>
                                    <p class="text-sm mt-1">Try a different name or roll number</p>
                                </div>
                            @endforelse
                        </div>
                    @endif
                    
                    <p class="text-sm text-gray-500 mt-3">
                        Search by student name, roll number, or phone number. Click on a student to proceed.
                    </p>
                </div>
            </div>
        </div>
    @endunless

    <!-- Admission selector appears after a student is chosen -->
    @if ($selectedStudentId && $admissions)
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-1">Select Course Admission</h2>
                        <p class="text-sm text-gray-600 mb-4">Choose which course admission to process payment for</p>
                    </div>
                    <button type="button" 
                        wire:click="$set('selectedStudentId', null)" 
                        class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Change Student
                    </button>
                </div>
                
                <div class="relative">
                    <select 
                        wire:model.live="admission_id"
                        class="border rounded-lg px-3 py-3 w-full focus:ring-2 focus:ring-primary-500 focus:border-primary-500 appearance-none">
                        <option value="">-- Choose an admission --</option>
                        @foreach ($admissions as $a)
                            <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Admission + Installment -->
    @if ($admission_id)
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="bg-gray-50 border-b px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">Payment Details</h2>
                <div class="flex items-center justify-between mt-2">
                    <div class="flex items-center">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Admission Due:</span>
                            <span class="font-bold text-red-600 ml-1">₹{{ $admission_fee_due }}</span>
                        </div>
                    </div>
                    <div class="flex items-center bg-primary-50 rounded-lg px-3 py-1.5 border border-primary-100">
                        <input type="checkbox" id="flexiblePayment" wire:model.live="flexiblePayment"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="flexiblePayment" class="text-sm text-primary-700 font-medium ml-2">
                            Flexible Payment Mode
                        </label>
                        <div class="ml-2 group relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary-500 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            <div class="hidden group-hover:block absolute z-10 w-64 bg-gray-900 text-white text-xs rounded py-2 px-3 right-0 bottom-full mb-2">
                                Flexible payment allows allocating any amount across installments automatically, instead of selecting specific installments.
                                <div class="absolute bottom-0 right-0 w-3 h-3 -mb-2 transform rotate-45 bg-gray-900"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div>
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            {{ $flexiblePayment ? 'Available Installments (Smart Allocation)' : 'Select Installments to Pay' }}
                        </h3>

                        <div class="overflow-x-auto bg-white rounded-lg border">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr class="text-left">
                                        <th class="px-4 py-3 w-10">
                                            <div class="text-xs font-medium text-gray-500">
                                                {{ $flexiblePayment ? 'Status' : 'Select' }}
                                            </div>
                                        </th>
                                        <th class="px-4 py-3">Installment</th>
                                        <th class="px-4 py-3">Due Date</th>
                                        <th class="px-4 py-3 text-right">Amount (₹)</th>
                                        <th class="px-4 py-3 text-right">Paid (₹)</th>
                                        <th class="px-4 py-3 text-right">Remaining (₹)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($schedules as $row)
                                        <tr class="hover:bg-gray-50 {{ $row['left'] <= 0 ? 'bg-green-50' : '' }}">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($flexiblePayment)
                                                    @if($row['left'] <= 0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Paid
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Due
                                                        </span>
                                                    @endif
                                                @else
                                                    <div class="flex items-center">
                                                        <input type="checkbox" 
                                                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                            value="{{ $row['id'] }}" 
                                                            wire:model.live="selectedScheduleIds"
                                                            {{ $row['left'] <= 0 ? 'disabled' : '' }} 
                                                            @class([
                                                                'opacity-50 cursor-not-allowed' => $row['left'] <= 0,
                                                                'cursor-pointer' => $row['left'] > 0,
                                                            ])>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 font-medium">#{{ $row['installment_no'] }}</td>
                                            <td class="px-4 py-3">{{ $row['due_date'] }}</td>
                                            <td class="px-4 py-3 text-right">{{ number_format($row['amount'], 2) }}</td>
                                            <td class="px-4 py-3 text-right">{{ number_format($row['paid'], 2) }}</td>
                                            <td class="px-4 py-3 text-right">
                                                @if ($row['left'] <= 0)
                                                    <span class="text-green-600 font-medium">Fully Paid</span>
                                                @else
                                                    <span class="font-medium">{{ number_format($row['left'], 2) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM7 9H5v2h2V9zm8 0h-2v2h2V9zM7 5h6a1 1 0 010 2H7a1 1 0 110-2zM7 13h6a1 1 0 010 2H7a1 1 0 110-2z" clip-rule="evenodd" />
                                                </svg>
                                                <p class="font-medium">No installments found</p>
                                                <p class="text-sm mt-1">There are no payment schedules for this admission</p>
                                            </td>
                                        </tr>
                                    @endforelse

                                    @if (!empty($schedules) && !$this->hasAvailableInstallments)
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center bg-green-50">
                                                <div class="flex items-center justify-center">
                                                    <svg class="h-5 w-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span class="font-medium text-green-700">All installments are fully paid!</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if (!empty($schedules) && $this->hasAvailableInstallments)
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td class="px-4 py-3">
                                                @if(!$flexiblePayment && !empty($selectedScheduleIds))
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary-100 text-primary-800">
                                                        {{ count($selectedScheduleIds) }} selected
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 font-semibold" colspan="4">Total {{ $flexiblePayment ? 'due' : 'of selected' }}</td>
                                            <td class="px-4 py-3 text-right font-semibold">
                                                <span class="text-lg">₹{{ number_format($flexiblePayment ? array_sum(array_column($schedules, 'left')) : $this->selected_total, 2) }}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        <div wire:loading wire:target="selectedScheduleIds,admission_id" class="flex items-center text-sm text-primary-600 mt-3">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating selection...
                        </div>
                        
                        @unless($flexiblePayment)
                            <div class="bg-gray-50 rounded-lg p-3 mt-4 border">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-600">
                                            @if ($this->hasAvailableInstallments)
                                                Select one or more installments above. The <strong>Amount</strong> field will be auto-filled with the total remaining amount of selected installments. You can still adjust it if needed.
                                            @else
                                                All installments are fully paid. No further payment is needed.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endunless
                        
                        <p class="text-xs text-gray-600 mt-2">
                            @if ($this->hasAvailableInstallments)
                                Tip: checking rows auto-fills the <strong>Amount</strong> field below with
                                the total "Left" of selected installments.
                                You can still override it.
                            @else
                                All installments are fully paid. No payment needed.
                            @endif
                        </p>
                    </div>

                    <!-- Flexible Payment Section -->
                    @if ($flexiblePayment)
                        <div class="mt-6 p-5 bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-100 rounded-lg">
                            <h4 class="text-base font-medium text-primary-800 mb-4">Smart Payment Allocation</h4>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-primary-700 mb-1">Payment Amount</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">₹</span>
                                        </div>
                                        <input type="number" 
                                            step="0.01" 
                                            min="0.01" 
                                            wire:model.live="flexibleAmount"
                                            class="w-full pl-8 px-3 py-3 border border-primary-200 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('flexibleAmount') border-red-500 @enderror"
                                            placeholder="Enter payment amount (min ₹0.01)"
                                            lang="en"
                                            inputmode="decimal">
                                    </div>
                                    @error('flexibleAmount')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if ($flexibleAmount > 0)
                                    @php
                                        $allocation = $this->getSmartAllocationPreview();
                                    @endphp

                                    @if (!empty($allocation['allocation']))
                                        <div class="bg-white rounded-lg p-4 border border-primary-200 shadow-sm">
                                            <h5 class="text-sm font-semibold text-primary-800 mb-3">Payment Allocation Preview</h5>
                                            <div class="space-y-2.5">
                                                @foreach ($allocation['allocation'] as $item)
                                                    <div class="flex justify-between text-sm items-center">
                                                        <div class="flex items-center">
                                                            <div class="h-2 w-2 rounded-full bg-primary-400 mr-2"></div>
                                                            <span>Installment #{{ $item['installment_no'] }}</span>
                                                            <span class="text-gray-500 ml-1">(Due: {{ $item['due_date'] }})</span>
                                                        </div>
                                                        <span class="font-medium">₹{{ number_format($item['amount'], 2) }}</span>
                                                    </div>
                                                @endforeach

                                                @if ($allocation['overpayment'] > 0.01)
                                                    <div class="flex justify-between text-sm border-t border-dashed border-green-200 pt-3 mt-3 items-center">
                                                        <div class="flex items-center text-green-600">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                            <span>Advance Payment (for future installments)</span>
                                                        </div>
                                                        <span class="font-medium text-green-600">₹{{ number_format($allocation['overpayment'], 2) }}</span>
                                                    </div>
                                                @endif

                                                <div class="flex justify-between text-sm font-medium bg-gray-50 p-3 rounded-lg mt-2">
                                                    <span>Total Payment</span>
                                                    <span class="text-lg text-primary-700">₹{{ number_format($flexibleAmount, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                
                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                                    <div class="flex">
                                        <div class="flex-shrink-0 text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">
                                                Smart allocation automatically distributes your payment across pending installments in chronological order. This helps ensure earlier dues are cleared first.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Form Instructions -->
    @if ($admission_id && empty($selectedScheduleIds) && !$flexiblePayment)
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden mt-6">
            <div class="p-5">
                <div class="flex items-center bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-blue-800 mb-1">Action Required</h3>
                        <p class="text-sm text-blue-700">
                            You have two options to proceed:
                        </p>
                        <ul class="mt-2 space-y-2">
                            <li class="flex items-center text-sm text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <strong>Select specific installments</strong> from the table above
                            </li>
                            <li class="flex items-center text-sm text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <strong>Enable Flexible Payment Mode</strong> for smart allocation
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Selected Installments Summary -->
    @if ($admission_id && !empty($selectedScheduleIds))
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden mt-6">
            <div class="p-5">
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800 mb-1">Installments Selected</h3>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($selectedScheduleIds as $id)
                                        @php
                                            $schedule = collect($schedules)->firstWhere('id', $id);
                                        @endphp
                                        @if($schedule)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                Installment #{{ $schedule['installment_no'] }}
                                                <span class="ml-1 text-green-600">₹{{ number_format($schedule['left'], 2) }}</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                                <p class="text-sm text-green-700 mt-2">
                                    Total: <span class="font-medium">₹{{ number_format($this->selected_total, 2) }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Ready to proceed
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Form -->
    @if ($admission_id && (!empty($selectedScheduleIds) || $flexiblePayment))
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden mt-6">
            <div class="bg-gradient-to-r from-primary-50 to-primary-100 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-primary-800">Complete Payment Details</h2>
                <p class="text-sm text-primary-600 mt-1">Fill in the payment information to finalize the transaction</p>
            </div>
            
            <div class="p-6">
                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Receipt Number Preview -->
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200 flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-medium text-yellow-800 mb-1">Receipt Number (Auto-generated)</label>
                            <p class="text-lg font-bold text-yellow-900">{{ $receipt_number }}</p>
                            <p class="text-xs text-yellow-700 mt-1">{{ now()->format('F j, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium border border-yellow-200">
                                Pending
                            </span>
                        </div>
                    </div>

                    <!-- Date & Amount -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-1.5 text-gray-700">Payment Date</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="date" wire:model="date"
                                    class="border rounded-lg pl-10 px-3 py-3 w-full focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('date') border-red-500 @enderror">
                            </div>
                            @error('date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5 text-gray-700">Payment Amount</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">₹</span>
                                </div>
                                <input type="number" step="0.01" min="0.01"
                                    @if ($flexiblePayment) 
                                        wire:model.live="flexibleAmount"
                                    @else 
                                        wire:model.live="amount" 
                                    @endif
                                    class="border rounded-lg pl-8 px-3 py-3 w-full focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('amount') border-red-500 @enderror @error('flexibleAmount') border-red-500 @enderror"
                                    placeholder="Enter amount (min ₹0.01)"
                                    lang="en"
                                    inputmode="decimal">
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('flexibleAmount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- GST Section (Uncommented and Styled) -->
                    {{-- <div class="bg-gray-50 rounded-lg p-4 border">
                        <div class="flex items-center space-x-3 mb-3">
                            <input type="checkbox" id="applyGst" wire:model.live="applyGst"
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="applyGst" class="text-sm font-medium text-gray-700">
                                Apply 18% GST to this payment
                            </label>
                        </div>

                        @if ($applyGst)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm animate-fade-in">
                                <div class="bg-white rounded-lg p-3 border">
                                    <span class="text-gray-600">Base Amount:</span>
                                    <span class="font-medium ml-2">₹{{ number_format((float) ($flexiblePayment ? $flexibleAmount : $amount), 2) }}</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 border">
                                    <span class="text-gray-600">GST (18%):</span>
                                    <span class="font-medium ml-2 text-primary-600">₹{{ number_format((float)$gstAmount, 2) }}</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 border">
                                    <span class="text-gray-600">Total Amount:</span>
                                    <span class="font-medium ml-2 text-green-600">₹{{ number_format((float)($flexiblePayment ? $flexibleAmount : $amount) + $gstAmount, 2) }}</span>
                                </div>
                            </div>
                        @endif
                    </div> --}}

                    <!-- Payment Method with Cards -->
                    <div>
                        <label class="block text-sm font-medium mb-3 text-gray-700">Payment Method</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="paymentMode" value="cash" wire:model.live="mode" class="sr-only">
                                <div class="border rounded-lg p-4 flex items-center {{ $mode === 'cash' ? 'bg-primary-50 border-primary-500 ring-2 ring-primary-500' : 'hover:bg-gray-50' }}">
                                    <div class="bg-{{ $mode === 'cash' ? 'primary' : 'gray' }}-100 rounded-full p-2 mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $mode === 'cash' ? 'text-primary-600' : 'text-gray-500' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium {{ $mode === 'cash' ? 'text-primary-700' : 'text-gray-700' }}">Cash</p>
                                        <p class="text-xs {{ $mode === 'cash' ? 'text-primary-600' : 'text-gray-500' }}">Physical payment</p>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="paymentMode" value="cheque" wire:model.live="mode" class="sr-only">
                                <div class="border rounded-lg p-4 flex items-center {{ $mode === 'cheque' ? 'bg-primary-50 border-primary-500 ring-2 ring-primary-500' : 'hover:bg-gray-50' }}">
                                    <div class="bg-{{ $mode === 'cheque' ? 'primary' : 'gray' }}-100 rounded-full p-2 mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $mode === 'cheque' ? 'text-primary-600' : 'text-gray-500' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium {{ $mode === 'cheque' ? 'text-primary-700' : 'text-gray-700' }}">Cheque</p>
                                        <p class="text-xs {{ $mode === 'cheque' ? 'text-primary-600' : 'text-gray-500' }}">Bank cheque payment</p>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="paymentMode" value="online" wire:model.live="mode" class="sr-only">
                                <div class="border rounded-lg p-4 flex items-center {{ $mode === 'online' ? 'bg-primary-50 border-primary-500 ring-2 ring-primary-500' : 'hover:bg-gray-50' }}">
                                    <div class="bg-{{ $mode === 'online' ? 'primary' : 'gray' }}-100 rounded-full p-2 mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $mode === 'online' ? 'text-primary-600' : 'text-gray-500' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium {{ $mode === 'online' ? 'text-primary-700' : 'text-gray-700' }}">Online</p>
                                        <p class="text-xs {{ $mode === 'online' ? 'text-primary-600' : 'text-gray-500' }}">Bank/UPI transfer</p>
                                    </div>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="paymentMode" value="head_office" wire:model.live="mode" class="sr-only">
                                <div class="border rounded-lg p-4 flex items-center {{ $mode === 'head_office' ? 'bg-primary-50 border-primary-500 ring-2 ring-primary-500' : 'hover:bg-gray-50' }}">
                                    <div class="bg-{{ $mode === 'head_office' ? 'primary' : 'gray' }}-100 rounded-full p-2 mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $mode === 'head_office' ? 'text-primary-600' : 'text-gray-500' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 8a1 1 0 011-1h4a1 1 0 011 1v4H7v-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium {{ $mode === 'head_office' ? 'text-primary-700' : 'text-gray-700' }}">Head Office</p>
                                        <p class="text-xs {{ $mode === 'head_office' ? 'text-primary-600' : 'text-gray-500' }}">Head office payment</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Reference Number (Conditional) -->
                    @if ($mode !== 'cash')
                        <div class="animate-fade-in">
                            <label class="block text-sm font-medium mb-1.5 text-gray-700">
                                {{ $mode === 'cheque' ? 'Cheque Number' : 'Transaction/UTR Number' }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 10a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM9 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1V4zm2 2V5h1v1h-1zM9 10a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-3zm2 2v-1h1v1h-1zM16 3a1 1 0 011 1v7.586l-2-2L16.586 8l-2-2L16 4.414 14.586 3 16 3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" wire:model="reference_no"
                                    class="border rounded-lg pl-10 px-3 py-3 w-full focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                                    placeholder="{{ $mode === 'cheque' ? 'Enter cheque number' : 'Enter transaction/UTR number' }}">
                            </div>
                        </div>
                    @endif

                    <!-- Payment Summary -->
                    <div class="bg-gray-50 rounded-lg border p-4 mt-6">
                        <h3 class="font-medium text-gray-800 mb-3">Payment Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Student Name:</span>
                                <span class="font-medium">{{ $student_name ?? 'Selected Student' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Course:</span>
                                <span class="font-medium">{{ $course_name ?? 'Selected Course' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Mode:</span>
                                <span class="font-medium capitalize">{{ $mode }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Date:</span>
                                <span class="font-medium">{{ $date }}</span>
                            </div>
                            @if($mode !== 'cash' && $reference_no)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Reference Number:</span>
                                <span class="font-medium">{{ $reference_no }}</span>
                            </div>
                            @endif
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between text-base">
                                    <span class="font-medium text-gray-700">Total Amount:</span>
                                    <span class="font-bold text-primary-700">
                                        ₹{{ number_format($applyGst ? (($flexiblePayment ? $flexibleAmount : $amount) + $gstAmount) : ($flexiblePayment ? $flexibleAmount : $amount), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-2">
                        <a href="{{ route('admin.payments.index') }}"
                            class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors text-center text-gray-700">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
                            wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span wire:loading.remove wire:target="save">Complete Payment</span>
                            <span wire:loading wire:target="save">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>