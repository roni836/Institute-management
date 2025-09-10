<div class="p-4 md:p-6 space-y-6">
    {{-- Top Stepper --}}
    <div class="bg-white border rounded-xl p-4">
        <div class="flex items-center justify-between">
            @php
                $steps = [
                    1 => 'Student',
                    2 => 'Education',
                    3 => 'Admission',
                    4 => 'Plan & Review',
                ];
            @endphp

            <div class="flex items-center w-full">
                @foreach ($steps as $i => $label)
                    <div class="flex-1 flex items-center">
                        <button type="button" wire:click="goToStep({{ $i }})" class="flex items-center gap-2">
                            <span
                                class="flex items-center justify-center h-8 w-8 rounded-full text-sm font-semibold
                                @if ($step > $i) bg-green-600 text-white
                                @elseif($step === $i) bg-primary-600 text-white
                                @else bg-gray-200 text-gray-700 @endif">
                                @if ($step > $i)
                                    ✓
                                @else
                                    {{ $i }}
                                @endif
                            </span>
                            <span
                                class="@if ($step === $i) text-primary-700 font-semibold @else text-gray-600 @endif">
                                {{ $label }}
                            </span>
                        </button>
                        @if ($i < 4)
                            <div
                                class="flex-1 h-0.5 mx-3
                                @if ($step > $i) bg-green-600 @else bg-gray-200 @endif">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4 h-2 w-full bg-gray-100 rounded">
            <div class="h-2 bg-primary-600 rounded" style="width: {{ $progress }}%"></div>
        </div>
    </div>

    {{-- MAIN FORM --}}
    <form wire:submit.prevent="save" class="space-y-6">
        {{-- STEP 1: Student --}}
        @if ($step === 1)
            <div class="grid md:grid-cols-3 gap-4">
                <div class="md:col-span-3 bg-white border rounded-xl p-4 space-y-4">
                    <h3 class="font-semibold">Student Details</h3>

                    {{-- Current Student Info Notice --}}
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    <strong>Editing:</strong> {{ $admission->student->name }} ({{ $admission->student->roll_no }})
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Student Form --}}
                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs">Name</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="name">
                            @error('name')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">Email</label>
                            <input type="email" class="w-full border rounded p-2" wire:model="email">
                            @error('email')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs">Phone</label>
                            <input type="tel" class="w-full border rounded p-2" wire:model="phone">
                            @error('phone')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">Alternative Phone</label>
                            <input type="tel" class="w-full border rounded p-2" wire:model="alt_phone">
                            @error('alt_phone')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div hidden>
                            <label class="text-xs">Status</label>
                            <select class="w-full border rounded p-2" wire:model="student_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="alumni">Alumni</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs">Gender</label>
                            <select class="w-full border rounded p-2 bg-white" wire:model="gender">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs">Category</label>
                            <select class="w-full border rounded p-2 bg-white" wire:model="category">
                                <option value="">Select</option>
                                <option value="sc">SC</option>
                                <option value="st">ST</option>
                                <option value="obc">OBC</option>
                                <option value="general">General</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs">Father's Name</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="father_name">
                        </div>
                        <div>
                            <label class="text-xs">Mother's Name</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="mother_name">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs">Father's Occupation</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="father_occupation">
                        </div>
                        <div>
                            <label class="text-xs">Mother's Occupation</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="mother_occupation">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs">Address</label>
                        <textarea class="w-full border rounded p-2" wire:model="address"></textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" wire:click="next"
                            class="px-4 py-2 rounded-lg bg-primary-600 text-white">Next</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 2: Education --}}
        @if ($step === 2)
            <div class="grid md:grid-cols-3 gap-4">
                <div class="md:col-span-4 bg-white border rounded-xl p-4 space-y-4">
                    <h3 class="font-semibold">Education Details</h3>

                    <div class="grid md:grid-cols-1 gap-3">
                        <div>
                            <label class="text-xs">School Name</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="school_name">
                            @error('school_name')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">School Address</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="school_address">
                            @error('school_address')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid md:grid-cols-1 gap-3">
                        <div>
                            <label class="text-xs">Previous/ Current Class </label>
                            <input type="text" class="w-full border rounded p-2" wire:model="class">
                            @error('class')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">Board </label>
                            <input type="text" class="w-full border rounded p-2" wire:model="board">
                            @error('board')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" wire:click="prev" class="px-4 py-2 rounded-lg border">Back</button>
                        <button type="button" wire:click="next"
                            class="px-4 py-2 rounded-lg bg-primary-600 text-white">Next</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 3: Admission --}}
        @if ($step === 3)
            <div class="grid md:grid-cols-3 gap-4">
                <div class="md:col-span-2 bg-white border rounded-xl p-4 space-y-4">
                    <h3 class="font-semibold">Admission Details</h3>

                    <div class="grid md:grid-cols-1 gap-3">
                        <div>
                            <label class="text-xs">Batch (Course)</label>
                            <select class="w-full border rounded p-2" wire:model.live="batch_id">
                                <option value="">Select batch</option>
                                @foreach ($batches as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->batch_name }} — {{ $b->course->name }}
                                        (₹{{ number_format($b->course->gross_fee, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('batch_id')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">Admission Date</label>
                            <input type="date" class="w-full border rounded p-2" wire:model="admission_date">
                            @error('admission_date')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">Mode</label>
                            <select class="w-full border rounded p-2" wire:model.change="mode">
                                <option value="full">Full</option>
                                <option value="installment">Installment</option>
                            </select>
                            @error('mode')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label class="text-xs">Discount (in Rs.)</label>
                            <input type="number" step="0.01" min="0" class="w-full border rounded p-2"
                                wire:model.live="discount">
                            @error('discount')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">Total Payable</label>
                            <input type="text" readonly class="w-full border rounded p-2 bg-gray-50"
                                value="₹{{ number_format($fee_total, 2) }}">
                            @error('fee_total')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @if ($mode === 'installment')
                            <div>
                                <label class="text-xs">No. of Installments</label>
                                <input type="number" min="2" class="w-full border rounded p-2"
                                    wire:model.live="installments">
                                @error('installments')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" wire:click="prev" class="px-4 py-2 rounded-lg border">Back</button>
                        <button type="button" wire:click="next"
                            class="px-4 py-2 rounded-lg bg-primary-600 text-white">Next</button>
                    </div>
                </div>

                <div class="bg-white border rounded-xl p-4 space-y-3">
                    <div class="bg-white border rounded-xl overflow-hidden">
                        <!-- Summary Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Summary</h3>
                            <p class="text-sm text-gray-600">Admission details overview</p>
                        </div>

                        <!-- Summary Content -->
                        <div class="divide-y">
                            <!-- Payment Details Section -->
                            <div class="p-6 space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Mode</span>
                                    <span
                                        class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $mode === 'full' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                        {{ ucfirst($mode) }} Payment
                                    </span>
                                </div>

                                <!-- Fee Breakdown -->
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    @if ($batch_id && ($b = $batches->firstWhere('id', (int) $batch_id)))
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Course Fee</span>
                                            <span
                                                class="font-medium">₹{{ number_format($b->course->gross_fee, 2) }}</span>
                                        </div>
                                    @endif

                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Discount Applied</span>
                                        <span class="text-green-600 font-medium">-
                                            ₹{{ number_format($discount, 2) }}</span>
                                    </div>

                                    <div class="pt-2 border-t flex justify-between">
                                        <span class="font-medium">Total Payable</span>
                                        <span
                                            class="text-lg font-bold text-orange-600">₹{{ number_format($fee_total, 2) }}</span>
                                    </div>
                                </div>

                                @if ($mode === 'installment')
                                    <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4">
                                        <div class="flex items-center gap-2 text-yellow-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm font-medium">{{ $installments }} installments</span>
                                        </div>
                                        <p class="mt-1 text-xs text-yellow-700">Monthly payments will be calculated
                                            based on the total amount.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Course & Batch Info -->
                            @if ($batch_id && ($b = $batches->firstWhere('id', (int) $batch_id)))
                                <div class="p-6 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Course</p>
                                            <p class="font-medium">{{ $b->course->name }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Batch</p>
                                            <p class="font-medium">{{ $b->batch_name }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 4: Plan & Review --}}
        @if ($step === 4)
            <div class="grid md:grid-cols-3 gap-4">
                <div class="md:col-span-2 bg-white border rounded-xl p-4 space-y-4">
                    <h3 class="font-semibold">Payment Plan</h3>

                    @if ($mode === 'installment')
                        <div class="space-y-2 max-h-80 overflow-auto">
                            @foreach ($plan as $row)
                                <div class="flex justify-between text-sm border rounded p-2">
                                    <span>#{{ $row['no'] }}</span>
                                    <span>₹{{ number_format($row['amount'], 2) }}</span>
                                    <span>Due: {{ $row['due_on'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-600">Full payment. Collect on first day or via Payments.</p>
                    @endif

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" wire:click="prev" class="px-4 py-2 rounded-lg border">Back</button>
                        <a href="{{ route('admin.admissions.index') }}"
                            class="px-4 py-2 rounded-lg border">Cancel</a>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-black text-white">
                            Update Admission
                        </button>
                    </div>
                </div>

                <div class="bg-white border rounded-xl p-4 space-y-3">
                    <div class="bg-white border rounded-xl overflow-hidden">
                        <!-- Review Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-900">Final Review</h3>
                            <p class="text-sm text-gray-600">Verify all details before submission</p>
                        </div>

                        <!-- Review Content -->
                        <div class="divide-y">
                            <!-- Student Details Section -->
                            <div class="p-6 space-y-4">
                                <h4 class="text-sm font-medium text-gray-700">Student Information</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Student Name</p>
                                            <p class="font-medium">{{ $name ?: '—' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Roll Number</p>
                                            <p class="font-medium text-green-600">{{ $admission->student->roll_no }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Student UID</p>
                                            <p class="font-medium text-green-600">{{ $admission->student->student_uid }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Course & Batch Details -->
                            @if ($batch_id && ($b = $batches->firstWhere('id', (int) $batch_id)))
                                <div class="p-6 space-y-4">
                                    <h4 class="text-sm font-medium text-gray-700">Course & Batch Details</h4>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-green-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Course</p>
                                                <p class="font-medium">{{ $b->course->name }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-orange-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Batch</p>
                                                <p class="font-medium">{{ $b->batch_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Payment Summary -->
                            <div class="p-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-4">Payment Details</h4>

                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Payment Mode</span>
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $mode === 'full' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($mode) }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Discount Applied</span>
                                        <span class="text-green-600">-₹{{ number_format($discount, 2) }}</span>
                                    </div>

                                    <div class="pt-2 border-t flex justify-between items-center">
                                        <span class="font-medium">Final Amount</span>
                                        <span
                                            class="text-lg font-bold text-orange-600">₹{{ number_format($fee_total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>

    @if (session('ok'))
        <div class="p-3 rounded bg-green-100 text-green-800">{{ session('ok') }}</div>
    @endif

    @if (session('error'))
        <div class="p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif
</div>