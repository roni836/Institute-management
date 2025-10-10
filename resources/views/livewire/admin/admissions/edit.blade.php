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
                @foreach ($steps as $index => $stepName)
                    <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                    {{ $step >= $index ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                            {{ $index }}
                        </div>
                        <span class="ml-2 text-sm font-medium 
                                    {{ $step >= $index ? 'text-gray-900' : 'text-gray-500' }}">{{ $stepName }}</span>
                    </div>
                    @if (!$loop->last)
                        <div class="flex-1 h-0.5 mx-4 
                                    {{ $step > $index ? 'bg-blue-600' : 'bg-gray-300' }}">
                        </div>
                    @endif
                @endforeach
            </div>
    </div>
</div>

{{-- MAIN FORM --}}
<form wire:submit.prevent="save" class="space-y-6">
    {{-- STEP 1: Student --}}
    @if ($step === 1)
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Personal Information</h3>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" wire:model="name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Father's Name *</label>
                    <input type="text" wire:model="father_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('father_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mother's Name *</label>
                    <input type="text" wire:model="mother_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('mother_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" wire:model="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                    <input type="tel" wire:model="phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                    <input type="tel" wire:model="whatsapp_no"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('whatsapp_no')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alternative Phone</label>
                    <input type="tel" wire:model="alt_phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('alt_phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" wire:model="dob"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('dob')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                    <select wire:model="gender"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select wire:model="category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        <option value="General">General</option>
                        <option value="OBC">OBC</option>
                        <option value="SC">SC</option>
                        <option value="ST">ST</option>
                        <option value="EWS">EWS</option>
                    </select>
                    @error('category')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Father's Occupation</label>
                    <input type="text" wire:model="father_occupation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('father_occupation')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mother's Occupation</label>
                    <input type="text" wire:model="mother_occupation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('mother_occupation')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Address Section -->
            <div class="mt-8">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Address Information</h4>

                <!-- Permanent Address -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h5 class="text-sm font-medium text-gray-700 mb-3">Permanent Address</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                            <input type="text" wire:model="address_line1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('address_line1')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                            <input type="text" wire:model="address_line2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('address_line2')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <input type="text" wire:model="city"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('city')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                            <input type="text" wire:model="state"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('state')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">District *</label>
                            <input type="text" wire:model="district"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('district')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pincode *</label>
                            <input type="text" wire:model="pincode"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('pincode')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                            <input type="text" wire:model="country"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('country')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Correspondence Address -->
                <div class="mt-6">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" wire:model.live="same_as_permanent" id="same_as_permanent"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="same_as_permanent" class="ml-2 text-sm font-medium text-gray-700">
                            Correspondence address same as permanent address
                        </label>
                    </div>

                    @if (!$same_as_permanent)
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Correspondence Address</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1
                                        *</label>
                                    <input type="text" wire:model="corr_address_line1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('corr_address_line1')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                    <input type="text" wire:model="corr_address_line2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('corr_address_line2')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                    <input type="text" wire:model="corr_city"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('corr_city')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                    <input type="text" wire:model="corr_state"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('corr_state')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">District *</label>
                                    <input type="text" wire:model="corr_district"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('corr_district')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pincode *</label>
                                    <input type="text" wire:model="corr_pincode"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('corr_pincode')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                    <input type="text" wire:model="corr_country"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('corr_country')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 2: Education --}}
    @if ($step === 2)
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Education Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- School Information -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">School Name</label>
                    <input type="text" wire:model="school_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('school_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Board</label>
                    <input type="text" wire:model="board"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('board')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class *</label>
                    <select wire:model="class"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Class</option>
                        <option value="5">5th</option>
                        <option value="6">6th</option>
                        <option value="7">7th</option>
                        <option value="8">8th</option>
                        <option value="9">9th</option>
                        <option value="10">10th</option>
                        <option value="11">11th</option>
                        <option value="12">12th</option>
                    </select>
                    @error('class')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stream *</label>
                    <select wire:model="stream"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Stream</option>
                        <option value="Foundation">Foundation</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Medical">Medical</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('stream')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session</label>
                    <input type="text" wire:model="session"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., 2024-25">
                    @error('session')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Academic Session</label>
                    <input type="text" wire:model="academic_session"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., 2024-25">
                    @error('academic_session')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- School Address -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">School Address</label>
                <textarea wire:model="school_address" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                @error('school_address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>
    @endif

    {{-- STEP 3: Admission Details --}}
    @if ($step === 3)
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Admission Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Course Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                    <select wire:model.live="course_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }} -
                                ₹{{ number_format($course->fee) }}</option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Batch Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch *</label>
                    <select wire:model.live="batch_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        {{ !$course_id ? 'disabled' : '' }}>
                        <option value="">Select Batch</option>
                        @if ($course_id)
                            @foreach ($batches->where('course_id', $course_id) as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('batch_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Admission Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admission Date *</label>
                    <input type="date" wire:model="admission_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('admission_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Payment Mode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Mode *</label>
                    <select wire:model.live="mode"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="full">Full Payment</option>
                        <option value="installment">Installment</option>
                    </select>
                    @error('mode')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Fee Calculation Section -->
            @if ($course_id)
                <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Fee Calculation</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tuition Fee</label>
                            <input type="number" wire:model.live="tuitionFee" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Other Fee</label>
                            <input type="number" wire:model.live="otherFee" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Late Fee</label>
                            <input type="number" wire:model.live="lateFee" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subtotal</label>
                            <input type="number" wire:model="subtotal" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                    </div>

                    <!-- Discount Section -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label>
                            <select wire:model.live="discount_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Discount {{ $discount_type === 'percentage' ? '(%)' : '(₹)' }}
                            </label>
                            <input type="number" wire:model.live="discount_value" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Amount</label>
                            <input type="number" wire:model="discount" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                    </div>

                    <!-- GST Section -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model.live="applyGst" id="applyGst"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="applyGst" class="ml-2 text-sm font-medium text-gray-700">Apply GST</label>
                        </div>

                        @if ($applyGst)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">GST Rate (%)</label>
                                <input type="number" wire:model.live="gstRate" step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">GST Amount</label>
                                <input type="number" wire:model="gstAmount" step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                            </div>
                        @endif
                    </div>

                    <!-- Total Fee -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900">Total Fee:</span>
                            <span class="text-2xl font-bold text-blue-600">₹{{ number_format($fee_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Status Section -->
            <div class="mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admission Status *</label>
                    <select wire:model.live="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                    @error('status')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if ($status === 'suspended')
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <label class="block text-sm font-medium text-yellow-800 mb-2">Reason for Suspension *</label>
                        <textarea wire:model="reason" rows="3"
                            class="w-full px-3 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Please provide a reason for suspending this admission..."></textarea>
                        @error('reason')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- STEP 4: Review & Submit --}}
    @if ($step === 4)
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Review & Submit</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Student Information Summary -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Student Information</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium">{{ $name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ $email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone:</span>
                            <span class="font-medium">{{ $phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Gender:</span>
                            <span class="font-medium">{{ ucfirst($gender) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-medium">{{ $category }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Class:</span>
                            <span class="font-medium">{{ $class }}{{ $class ? 'th' : '' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Stream:</span>
                            <span class="font-medium">{{ $stream }}</span>
                        </div>
                    </div>
                </div>

                <!-- Admission Information Summary -->
                <div class="bg-blue-50 p-6 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Admission Information</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Course:</span>
                            <span class="font-medium">{{ $selected_course->name ?? 'Not Selected' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Batch:</span>
                            <span class="font-medium">{{ $selected_batch->batch_name ?? 'Not Selected' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Admission Date:</span>
                            <span
                                class="font-medium">{{ $admission_date ? \Carbon\Carbon::parse($admission_date)->format('d M Y') : 'Not Set' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Mode:</span>
                            <span class="font-medium">{{ ucfirst($mode) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium">{{ ucfirst($status) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Summary -->
            <div class="mt-8 bg-green-50 p-6 rounded-lg">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Fee Summary</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tuition Fee:</span>
                            <span class="font-medium">₹{{ number_format($tuitionFee, 2) }}</span>
                        </div>
                        @if ($otherFee > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Other Fee:</span>
                                <span class="font-medium">₹{{ number_format($otherFee, 2) }}</span>
                            </div>
                        @endif
                        @if ($lateFee > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Late Fee:</span>
                                <span class="font-medium">₹{{ number_format($lateFee, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        @if ($discount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Discount:</span>
                                <span class="font-medium text-red-600">-₹{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
                        @if ($applyGst && $gstAmount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">GST ({{ $gstRate }}%):</span>
                                <span class="font-medium">₹{{ number_format($gstAmount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-t pt-2 text-lg font-bold">
                            <span class="text-gray-900">Total Fee:</span>
                            <span class="text-blue-600">₹{{ number_format($fee_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Schedule (if installment) -->
            @if ($mode === 'installment' && !empty($plan))
                <div class="mt-8 bg-yellow-50 p-6 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Payment Schedule</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Installment</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Due Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($plan as $index => $installment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($installment['due_on'])->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₹{{ number_format($installment['amount'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ucfirst($installment['status'] ?? 'pending') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Navigation Buttons -->
    <div class="px-6 py-4 bg-gray-50 border-t flex justify-between">
        <div>
            @if ($step > 1)
                <button type="button" wire:click="prev"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Previous
                </button>
            @endif
        </div>

        <div class="flex space-x-3">
            @if ($step < 4)
                <button type="button" wire:click="next"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Next
                </button>
            @else
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Update Admission
                </button>
            @endif
        </div>
    </div>
</form>
</div>
</div>
