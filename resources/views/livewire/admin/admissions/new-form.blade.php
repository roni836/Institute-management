<div
    data-step="{{ $step ?? 1 }}"
    data-mode="{{ $mode ?? 'full' }}"
    data-same="{{ $same_as_permanent ? 1 : 0 }}"
    x-data="{
        step: Number($el.dataset.step) || 1,
        mode: $el.dataset.mode || 'full',
        same_as_permanent: $el.dataset.same === '1',
        init() {
            const setupLivewireListeners = () => {
                if (typeof Livewire === 'undefined' || !Livewire.on) return;
                Livewire.on('stepChanged', (event) => { 
                    console.debug('Livewire.on stepChanged', event); 
                    this.step = event.step || event || 1;
                });
                Livewire.on('courseDataLoaded', (event) => { 
                    console.debug('Livewire.on courseDataLoaded', event); 
                    this.showNotification(`Selected course: ${event.name}`, 'info'); 
                });
                Livewire.on('feeRecalculated', (data) => { /* no-op */ });
                Livewire.on('discountApplied', (event) => { 
                    console.debug('Livewire.on discountApplied', event); 
                    this.showNotification(`Discount of ₹${event.amount} applied successfully`, 'success'); 
                });
                Livewire.on('gstToggled', (event) => { 
                    console.debug('Livewire.on gstToggled', event); 
                    const message = event.applied ? `GST applied at ${event.rate}% (₹${event.amount})` : 'GST removed'; 
                    this.showNotification(message, 'info'); 
                });
                Livewire.on('propertyChanged', (event) => {
                    console.debug('Livewire.on propertyChanged', event);
                    const property = event.property;
                    const value = event.value;
                    if (property === 'mode') this.mode = value;
                    if (property === 'same_as_permanent') this.same_as_permanent = Boolean(value);
                    if (property === 'step') this.step = Number(value);
                });
            };

            if (typeof Livewire !== 'undefined') {
                setupLivewireListeners();
            } else {
                document.addEventListener('livewire:load', setupLivewireListeners);
            }

            // Also listen for browser events dispatched by the component
            window.addEventListener('step-changed', (e) => {
                console.debug('window step-changed', e && e.detail);
                if (e && e.detail && typeof e.detail.step !== 'undefined') this.step = Number(e.detail.step);
            });
            window.addEventListener('livewire-property-changed', (e) => {
                console.debug('window livewire-property-changed', e && e.detail);
                if (!e || !e.detail) return;
                const { property, value } = e.detail;
                if (property === 'mode') this.mode = value;
                if (property === 'same_as_permanent') this.same_as_permanent = Boolean(value);
                if (property === 'step') this.step = Number(value);
            });
        },
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500'}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }
    }" x-init="init()" x-cloak>
    <style>
        .step-indicator {
            transition: all 0.3s ease;
        }
        .step-indicator.active {
            background-color: #3b82f6;
            color: white;
        }
        .step-indicator.completed {
            background-color: #10b981;
            color: white;
        }
        .form-section {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        /* Fade transition for Alpine.js */
        [x-cloak] { display: none !important; }
    </style>
    
<div class=" mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
                <h1 class="text-3xl font-bold text-white text-center">Student Admission Form</h1>
                
                <!-- Step Indicator -->
                <div class="flex justify-center mt-6">
                    <div class="flex items-center space-x-4">
                        <div id="step1-indicator" class="step-indicator flex items-center justify-center w-10 h-10 rounded-full font-bold" :class="{ 'active': step === 1, 'completed': step > 1 }">1</div>
                        <div class="w-16 h-1 bg-blue-300"></div>
                        <div id="step2-indicator" class="step-indicator flex items-center justify-center w-10 h-10 rounded-full font-bold" :class="{ 'active': step === 2 }">2</div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <span class="text-blue-100 font-medium" id="step-title">Student Information</span>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-8">
                <form id="admissionForm" wire:submit.prevent="save">
                    <!-- Step 1: Student Information -->
                    <div id="step1" class="form-section" x-show="step === 1" x-cloak>
                        <!-- Admission Info -->
                        <div class="mb-8 p-6 border-2 border-blue-100 rounded-lg bg-blue-50">
                            <h3 class="text-xl font-semibold text-blue-800 mb-4">Admission Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Admission Date *</label>
                                    <input wire:model="admission_date" type="date" name="admission_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('admission_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Session *</label>
                                    <select wire:model="session" name="session" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Session</option>
                                        <option value="2024-25">2024-25</option>
                                        <option value="2025-26">2025-26</option>
                                    </select>
                                    @error('session') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Academic Session *</label>
                                    <select wire:model="academic_session" name="academic_session" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Academic Session</option>
                                        <option value="2024-25">2024-25</option>
                                        <option value="2025-26">2025-26</option>
                                    </select>
                                    @error('academic_session') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                    <select wire:model="course_id" wire:change="onCourseChange" name="course" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->name }} (₹{{ number_format($course->net_fee, 2) }})</option>
                                        @endforeach
                                    </select>
                                    @error('course_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch *</label>
                                    <select wire:model="batch_id" wire:change="recalculate" name="batch" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" {{ empty($course_id) ? 'disabled' : '' }}>
                                        <option value="">Select Batch</option>
                                        @foreach($batches->where('course_id', $course_id ?? 0) as $batch)
                                            <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('batch_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Student Info -->
                        <div class="mb-8 p-6 border-2 border-green-100 rounded-lg bg-green-50">
                            <h3 class="text-xl font-semibold text-green-800 mb-4">Student Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                                    <input wire:model="name" type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Father Name</label>
                                    <input wire:model="father_name" type="text" name="father_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('father_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mother Name</label>
                                    <input wire:model="mother_name" type="text" name="mother_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('mother_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile No.</label>
                                    <input wire:model="phone" type="tel" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp No.</label>
                                    <input wire:model="whatsapp_no" type="tel" name="whatsapp_no" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('whatsapp_no') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email ID</label>
                                    <input wire:model="email" type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                    <input wire:model="dob" type="date" name="dob" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('dob') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select wire:model="gender" name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="others">Other</option>
                                    </select>
                                    @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select wire:model="category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Category</option>
                                        <option value="general">General</option>
                                        <option value="obc">OBC</option>
                                        <option value="sc">SC</option>
                                        <option value="st">ST</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Father Occupation</label>
                                    <input wire:model="father_occupation" type="text" name="father_occupation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('father_occupation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mother Occupation</label>
                                    <input wire:model="mother_occupation" type="text" name="mother_occupation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('mother_occupation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="mb-8 p-6 border-2 border-purple-100 rounded-lg bg-purple-50">
                            <h3 class="text-xl font-semibold text-purple-800 mb-4">Address Information</h3>
                            
                            <!-- Permanent Address -->
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-700 mb-3">Permanent Address</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                                        <input wire:model="address_line1" type="text" name="address_line1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('address_line1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                        <input wire:model="address_line2" type="text" name="address_line2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('address_line2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                        <input wire:model="city" type="text" name="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">State/Union Territory</label>
                                        <input wire:model="state" type="text" name="state" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
                                        <input wire:model="district" type="text" name="district" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('district') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pin Code</label>
                                        <input wire:model="pincode" type="text" name="pincode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('pincode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                        <input wire:model="country" type="text" name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="India">
                                        @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Correspondence Address -->
                            <div class="mb-4">
                                <div class="flex items-center mb-3">
                                    <input wire:model.live="same_as_permanent" type="checkbox" id="sameAddress" class="mr-2">
                                    <label for="sameAddress" class="text-sm font-medium text-gray-700">Correspondence Address same as Permanent Address</label>
                                </div>
                                
                                <div x-show="!same_as_permanent" x-cloak>
                                    <h4 class="text-lg font-medium text-gray-700 mb-3">Correspondence Address</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                                            <input wire:model="corr_address_line1" type="text" name="corr_address_line1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('corr_address_line1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                            <input wire:model="corr_address_line2" type="text" name="corr_address_line2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('corr_address_line2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                            <input wire:model="corr_city" type="text" name="corr_city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('corr_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">State/Union Territory</label>
                                            <input wire:model="corr_state" type="text" name="corr_state" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('corr_state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
                                            <input wire:model="corr_district" type="text" name="corr_district" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('corr_district') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Pin Code</label>
                                            <input wire:model="corr_pincode" type="text" name="corr_pincode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('corr_pincode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                            <input wire:model="corr_country" type="text" name="corr_country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="India">
                                            @error('corr_country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Class Details -->
                        <div class="mb-8 p-6 border-2 border-orange-100 rounded-lg bg-orange-50">
                            <h3 class="text-xl font-semibold text-orange-800 mb-4">Class Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Stream *</label>
                                    <select wire:model="stream" name="stream" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Stream</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Foundation">Foundation</option>
                                        <option value="Medical">Medical</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('stream') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Student Status</label>
                                    <select wire:model="student_status" name="student_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="alumni">Alumni</option>
                                    </select>
                                    @error('student_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alternate Phone</label>
                                    <input wire:model="alt_phone" type="text" name="alt_phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('alt_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Enrollment ID Preview</label>
                                    <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                                        {{ $stream ? $this->getNextEnrollmentId() : 'Select stream first' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modules -->
                        <div class="mb-8 p-6 border-2 border-teal-100 rounded-lg bg-teal-50">
                            <h3 class="text-xl font-semibold text-teal-800 mb-4">Additional Modules</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Module 1</label>
                                    <input type="text" name="module1" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Module 2</label>
                                    <input type="text" name="module2" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="idCard" class="mr-2">
                                    <span class="text-sm font-medium text-gray-700">ID Card Required</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Fee Structure -->
                    <div id="step2" class="form-section" x-show="step === 2" x-cloak>
                        <div class="mb-8 p-6 border-2 border-blue-100 rounded-lg bg-blue-50">
                            <h3 class="text-xl font-semibold text-blue-800 mb-4">Fee Structure & Payment</h3>
                            
                            <!-- Contact Info Section -->
                            <div class="mb-6 p-4 bg-white rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-3">Contact Information</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs">👤</span>
                                        </div>
                                        <div>
                                            <div class="font-medium">Student</div>
                                            <div class="text-gray-600">Name</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs">👤</span>
                                        </div>
                                        <div>
                                            <div class="font-medium">Course</div>
                                            <div class="text-gray-600">Class</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs">👤</span>
                                        </div>
                                        <div>
                                            <div class="font-medium">Session</div>
                                            <div class="text-gray-600">Academic Year</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs">👤</span>
                                        </div>
                                        <div>
                                            <div class="font-medium">Father Name</div>
                                            <div class="text-gray-600">Guardian</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs">👤</span>
                                        </div>
                                        <div>
                                            <div class="font-medium">Stream</div>
                                            <div class="text-gray-600">Course Type</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs">👤</span>
                                        </div>
                                        <div>
                                            <div class="font-medium">Batch Type</div>
                                            <div class="text-gray-600">Schedule</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs">👤</span>
                                        </div>
                                        <div>
                                            <div class="font-medium">Admission Number</div>
                                            <div class="text-gray-600">ID</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs">👤</span>
                                        </div>
                                        <div>
                                            <div class="font-medium">Category</div>
                                            <div class="text-gray-600">Type</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- GST Application -->
                            <div class="mb-6 p-4 bg-blue-600 text-white rounded-lg">
                                <h4 class="font-bold text-lg mb-2">GST Application</h4>
                                <div class="flex items-center space-x-4 mt-2">
                                    <label class="inline-flex items-center">
                                        <input wire:model.live="applyGst" type="radio" name="gstOption" value="1" class="form-radio h-5 w-5 text-white border-white">
                                        <span class="ml-2 text-white">Apply 18% GST</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input wire:model.live="applyGst" type="radio" name="gstOption" value="0" class="form-radio h-5 w-5 text-white border-white" checked>
                                        <span class="ml-2 text-white">No GST</span>
                                    </label>
                                </div>
                                <div class="text-sm text-blue-100 mt-2" wire:loading wire:target="applyGst">
                                    Recalculating fees...
                                </div>
                            </div>

                            <!-- Fee Breakdown -->
                            <div class="mb-6">
                                <h4 class="font-semibold text-gray-700 mb-4">Fee Details</h4>
                                
                                <!-- Left side - Fee inputs -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center p-3 border rounded-lg">
                                            <label class="text-sm font-medium text-gray-700">Admission Type</label>
                                            <select name="admissionType" class="px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                                <option value="Regular">Regular</option>
                                                <option value="Late">Late Admission</option>
                                            </select>
                                        </div>
                                        
                                        <div class="flex justify-between items-center p-3 border rounded-lg">
                                            <span class="text-sm font-medium text-gray-700">Payment Mode</span>
                                            <div class="flex space-x-4">
                                                <label class="inline-flex items-center">
                                                    <input wire:model.live="mode" type="radio" name="paymentMode" value="full" class="form-radio h-4 w-4 text-blue-600" checked>
                                                    <span class="ml-2 text-sm">Full</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input wire:model.live="mode" type="radio" name="paymentMode" value="installment" class="form-radio h-4 w-4 text-blue-600">
                                                    <span class="ml-2 text-sm">Installment</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Discount Section -->
                                        <div class="p-3 border rounded-lg">
                                            <div class="mb-3">
                                                <h5 class="text-sm font-medium text-gray-700 mb-2">Discount</h5>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="text-xs text-gray-500">Type</label>
                                                        <select wire:model="discount_type" wire:change="recalculate" class="w-full px-2 py-1 text-sm border rounded">
                                                            <option value="fixed">Fixed Amount (₹)</option>
                                                            <option value="percentage">Percentage (%)</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="text-xs text-gray-500">Value</label>
                                                        <div class="flex items-center">
                                                            <input wire:model="discount_value" wire:change="recalculate" type="number" min="0" step="0.01" class="w-full px-2 py-1 text-sm text-right border rounded" placeholder="0.00">
                                                            <span class="ml-1 text-xs font-medium">{{ $discount_type == 'percentage' ? '%' : '₹' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Discount Amount</span>
                                                <div class="flex items-center">
                                                    <span class="text-sm font-medium">₹{{ number_format($discount, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Installment options - only visible when installment mode is selected -->
                                        <div class="space-y-2">
                                            <!-- Show installment settings when installment mode is selected -->
                                            <div class="p-3 border rounded-lg" x-show="mode === 'installment'">
                                                <div class="mb-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Installments</label>
                                                    <div class="flex items-center">
                                                        <input wire:model="installments" type="number" min="2" class="w-20 px-2 py-1 border rounded" placeholder="2">
                                                        <button type="button" wire:click="recalculate" class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                                            Generate Plan
                                                        </button>
                                                        <button type="button" wire:click="resetInstallments" class="ml-2 px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                                                            Reset
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Additional Fees -->
                                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                                <span class="text-sm">SRC-01</span>
                                                <span class="text-sm">Others</span>
                                                <input type="number" name="src01" placeholder="0" class="w-20 px-2 py-1 text-right border rounded">
                                            </div>
                                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                                <span class="text-sm">ABC-01</span>
                                                <span class="text-sm">Others</span>
                                                <input type="number" name="abc01" placeholder="0" class="w-20 px-2 py-1 text-right border rounded">
                                            </div>
                                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                                <span class="text-sm">ABC-02</span>
                                                <span class="text-sm">Others</span>
                                                <input type="number" name="abc02" placeholder="0" class="w-20 px-2 py-1 text-right border rounded">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right side - Fee calculations -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="font-semibold text-gray-700 mb-4">Fee Calculation</h4>
                                        <div class="space-y-3">
                                            <!-- Course details when available -->
                                            @if($selected_course)
                                                <div class="bg-blue-50 p-3 rounded-lg mb-3">
                                                    <h5 class="font-medium text-blue-800 text-sm mb-2">Course: {{ $selected_course->name }}</h5>
                                                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">Gross Fee:</span>
                                                            <span class="font-medium">₹{{ number_format($selected_course->gross_fee, 2) }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">Course Discount:</span>
                                                            <span class="font-medium text-red-600">-₹{{ number_format($selected_course->discount, 2) }}</span>
                                                        </div>
                                                        <div class="flex justify-between col-span-2 pt-1 border-t border-blue-200 mt-1">
                                                            <span class="text-gray-700 font-medium">Net Course Fee:</span>
                                                            <span class="font-medium">₹{{ number_format($selected_course->net_fee, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex justify-between">
                                                    <span class="text-sm">Course Fee</span>
                                                    <span class="font-medium">₹{{ number_format($selected_course->net_fee, 2) }}</span>
                                                </div>

                                                @if($selected_course->tution_fee ?? 0)
                                                <div class="flex justify-between text-xs text-gray-600 pl-2">
                                                    <span>- Tuition Fee</span>
                                                    <span>₹{{ number_format($selected_course->tution_fee, 2) }}</span>
                                                </div>
                                                @endif
                                                
                                                @if($selected_course->admission_fee ?? 0)
                                                <div class="flex justify-between text-xs text-gray-600 pl-2">
                                                    <span>- Admission Fee</span>
                                                    <span>₹{{ number_format($selected_course->admission_fee, 2) }}</span>
                                                </div>
                                                @endif
                                                
                                                @if($selected_course->exam_fee ?? 0)
                                                <div class="flex justify-between text-xs text-gray-600 pl-2">
                                                    <span>- Exam Fee</span>
                                                    <span>₹{{ number_format($selected_course->exam_fee, 2) }}</span>
                                                </div>
                                                @endif
                                                
                                                @if($selected_course->other_fee ?? 0)
                                                <div class="flex justify-between text-xs text-gray-600 pl-2">
                                                    <span>- Other Fees</span>
                                                    <span>₹{{ number_format($selected_course->other_fee, 2) }}</span>
                                                </div>
                                                @endif
                                            @else
                                                <div class="p-3 border border-gray-200 rounded-lg text-center">
                                                    <p class="text-gray-500 text-sm">Please select a course to view fee details</p>
                                                </div>
                                            @endif

                                            <!-- Additional Discount -->
                                            <div class="flex justify-between">
                                                <span class="text-sm">Additional Discount 
                                                    @if($discount_type == 'percentage' && $discount_value > 0)
                                                        ({{ $discount_value }}%)
                                                    @endif
                                                </span>
                                                <span class="font-medium text-red-600">-₹{{ number_format($discount ?? 0, 2) }}</span>
                                            </div>
                                            
                                            <div class="flex justify-between">
                                                <span class="text-sm">Late Fine + Pre charge</span>
                                                <span class="font-medium">₹{{ number_format($lateFee ?? 0, 2) }}</span>
                                            </div>
                                            
                                            <hr class="border-gray-300">
                                            
                                            <div class="flex justify-between">
                                                <span class="text-sm">Subtotal</span>
                                                <span class="font-medium">₹{{ number_format($subtotal ?? 0, 2) }}</span>
                                            </div>
                                            
                                            <!-- Show GST only if it's applied -->
                                            @if($applyGst)
                                            <div class="flex justify-between">
                                                <span class="text-sm">GST ({{ $gstRate }}%)</span>
                                                <span class="font-medium">₹{{ number_format($gstAmount ?? 0, 2) }}</span>
                                            </div>
                                            @endif
                                            
                                            <hr class="border-gray-300">
                                            
                                            <div class="flex justify-between text-lg font-bold text-blue-600">
                                                <span>Total</span>
                                                <span>₹{{ number_format($fee_total ?? 0, 2) }}</span>
                                            </div>
                                            
                                            <!-- Payment Plan Summary (only show if installment mode is selected) -->
                                            @if($mode == 'installment' && !empty($plan))
                                            <div class="mt-4 pt-4 border-t border-gray-300">
                                                <h5 class="font-medium text-gray-700 mb-2">Installment Plan</h5>
                                                <div class="space-y-1 text-xs">
                                                    @foreach($plan as $p)
                                                    <div class="flex justify-between">
                                                        <span>Installment #{{ $p['no'] }} ({{ \Carbon\Carbon::parse($p['due_on'])->format('d M Y') }})</span>
                                                        <span>₹{{ number_format($p['amount'], 2) }}</span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-8">
                        <button type="button" wire:click="prev" id="prevBtn" x-show="step > 1" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Previous
                        </button>
                        <div class="ml-auto">
                            <button type="button" wire:click="next" id="nextBtn" x-show="step < 2" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Next Step
                            </button>
                            <button type="button" wire:click="save" id="submitBtn" x-show="step === 2" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Submit Application
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>