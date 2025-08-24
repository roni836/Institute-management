<div class="p-4 md:p-6 space-y-6">
    {{-- Top Stepper --}}
    <div class="bg-white border rounded-xl p-4">
        <div class="flex items-center justify-between">
            @php
                $steps = [
                    1 => 'Student',
                    2 => 'Admission',
                    3 => 'Plan & Review',
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
                        @if ($i < 3)
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

                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs">Name</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="name">
                            @error('name')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">Roll No</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="roll_no">
                            @error('roll_no')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs">Student UID</label>
                            <input type="text" class="w-full border rounded p-2" wire:model="student_uid">
                            @error('student_uid')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div hidden>
                            <label class="text-xs">Status</label>
                            <select class="w-full border rounded p-2" wire:model="student_status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="alumni">Alumni</option>
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
                            <label class="text-xs">Email</label>
                            <input type="email" class="w-full border rounded p-2" wire:model="email">
                            @error('email')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs">Phone</label>
                            <input type="tel" class="w-full border rounded p-2" wire:model="phone">
                            @error('phone')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
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

        {{-- STEP 2: Admission --}}
        @if ($step === 2)
            <div class="grid md:grid-cols-3 gap-4">
                <div class="md:col-span-2 bg-white border rounded-xl p-4 space-y-4">
                    <h3 class="font-semibold">Admission Details</h3>

                    <div class="grid md:grid-cols-3 gap-3">
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
                    <h3 class="font-semibold">Quick Summary</h3>
                    <div class="text-sm text-gray-700 space-y-1">
                        <div><span class="text-gray-500">Mode:</span> <span
                                class="font-medium capitalize">{{ $mode }}</span></div>
                        <div><span class="text-gray-500">Total Payable:</span> <span class="font-semibold">₹
                                {{ number_format($fee_total, 2) }}</span></div>
                        @if ($mode === 'installment')
                            <div><span class="text-gray-500">Installments:</span> <span
                                    class="font-medium">{{ $installments }}</span></div>
                        @endif
                        @if ($batch_id)
                            @php $b = $batches->firstWhere('id', (int)$batch_id); @endphp
                            @if ($b)
                                <div><span class="text-gray-500">Batch:</span> <span
                                        class="font-medium">{{ $b->batch_name }}</span></div>
                                <div><span class="text-gray-500">Course:</span> <span
                                        class="font-medium">{{ $b->course->name }}</span></div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 3: Plan & Review --}}
        @if ($step === 3)
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
                        <button class="px-4 py-2 rounded-lg bg-black text-white">
                            {{ $this->admission ? 'Update Admission' : 'Save Admission' }}
                        </button>
                    </div>
                </div>

                <div class="bg-white border rounded-xl p-4 space-y-3">
                    <h3 class="font-semibold">Final Review</h3>
                    <div class="text-sm text-gray-700 space-y-1">
                        <div><span class="text-gray-500">Student:</span> <span
                                class="font-medium">{{ $name ?: '—' }}</span></div>
                        <div><span class="text-gray-500">Roll No:</span> <span
                                class="font-medium">{{ $roll_no ?: '—' }}</span></div>
                        <div><span class="text-gray-500">UID:</span> <span
                                class="font-medium">{{ $student_uid ?: '—' }}</span></div>
                        <div><span class="text-gray-500">Date:</span> <span
                                class="font-medium">{{ $admission_date }}</span></div>
                        @if ($batch_id)
                            @php $b = $batches->firstWhere('id', (int)$batch_id); @endphp
                            @if ($b)
                                <div><span class="text-gray-500">Batch:</span> <span
                                        class="font-medium">{{ $b->batch_name }}</span></div>
                                <div><span class="text-gray-500">Course:</span> <span
                                        class="font-medium">{{ $b->course->name }}</span></div>
                            @endif
                        @endif
                        <div class="pt-2 border-t"></div>
                        <div><span class="text-gray-500">Mode:</span> <span
                                class="font-medium capitalize">{{ $mode }}</span></div>
                        <div><span class="text-gray-500">Discount:</span> <span class="font-medium">₹
                                {{ number_format((float) $discount, 2) }}</span></div>
                        <div><span class="text-gray-500">Total Payable:</span> <span class="font-semibold">₹
                                {{ number_format($fee_total, 2) }}</span></div>
                    </div>
                </div>
            </div>
        @endif
    </form>

    @if (session('ok'))
        <div class="p-3 rounded bg-green-100 text-green-800">{{ session('ok') }}</div>
    @endif
</div>
