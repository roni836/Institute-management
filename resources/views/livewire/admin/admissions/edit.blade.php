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
            notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-primary-500'}`;
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
            background-color: var(--primary-600, #3b82f6);
            color: white;
        }
        .step-indicator.completed {
            background-color: var(--primary-600, #10b981);
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
    
<div class=" mx-auto px-3 py-4">
        <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-white p-4">
                <h1 class="text-2xl font-bold text-primary-700 text-center">Edit Student Admission</h1>
                
                <!-- Step Indicator -->
                <div class="flex justify-center mt-3">
                    <div class="flex items-center space-x-3">
                        <div id="step1-indicator" class="step-indicator flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm" :class="{ 'active': step === 1, 'completed': step > 1 }">1</div>
                        <div class="w-12 h-1 bg-primary-300"></div>
                        <div id="step2-indicator" class="step-indicator flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm" :class="{ 'active': step === 2 }">2</div>
                    </div>
                </div>
                
                <div class="text-center mt-2">
                    <span class="text-primary-500 text-sm font-medium" id="step-title">Student Information</span>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-4">
                <form id="admissionForm" wire:submit.prevent="save" enctype="multipart/form-data">
                    <!-- Step 1: Student Information -->
                    <div id="step1" class="form-section" x-show="step === 1" x-cloak>
                        <!-- Admission Info -->
                        <div class="mb-3 p-3 border border-primary-100 rounded bg-primary-50">
                            <h3 class="text-lg font-semibold text-primary-800 mb-3">Admission Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Admission Date *</label>
                                    <input wire:model="admission_date" type="date" name="admission_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('admission_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Stream *</label>
                                    <select wire:model.live="stream" name="stream" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Stream</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Foundation">Foundation</option>
                                        <option value="Medical">Medical</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('stream') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Academic Session *</label>
                                    <select wire:model.live="academic_session" name="academic_session" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Academic Session</option>
                                        <option value="2024-25">2024-25</option>
                                        <option value="2025-26">2025-26</option>
                                    </select>
                                    @error('academic_session') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Course *</label>
                                    <select wire:model="course_id" wire:change="onCourseChange" name="course" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->name }} (₹{{ number_format($course->net_fee, 2) }})</option>
                                        @endforeach
                                    </select>
                                    @error('course_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Batch *</label>
                                    <select wire:model="batch_id" wire:change="recalculate" name="batch" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500" {{ empty($course_id) ? 'disabled' : '' }}>
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
                        <div class="mb-3 p-3 border border-primary-100 rounded bg-primary-50">
                            <h3 class="text-lg font-semibold text-primary-800 mb-3">Student Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
                                    <input wire:model.live="name" type="text" name="name" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Father Name</label>
                                    <input wire:model.live="father_name" type="text" name="father_name" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('father_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Mother Name</label>
                                    <input wire:model.live="mother_name" type="text" name="mother_name" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('mother_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile No.</label>
                                    <input wire:model.live="phone" type="tel" name="phone" maxlength="15" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" inputmode="numeric">
                                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp No.</label>
                                    <input wire:model.live="whatsapp_no" type="tel" name="whatsapp_no" maxlength="15" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" inputmode="numeric">
                                    @error('whatsapp_no') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email ID</label>
                                    <input wire:model.live="email" type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                    <input wire:model.live="dob" type="date" name="dob" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('dob') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select wire:model.live="gender" name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="others">Other</option>
                                    </select>
                                    @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select wire:model.live="category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
                                    <input wire:model.live="father_occupation" type="text" name="father_occupation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('father_occupation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mother Occupation</label>
                                    <input wire:model.live="mother_occupation" type="text" name="mother_occupation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('mother_occupation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Class * 
                                        <small class="text-gray-500">(Current: "{{ $class }}")</small>
                                    </label>
                                    <select wire:model.live="class" name="class" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
                                    @error('class') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">School Name</label>
                                    <input wire:model.live="school_name" type="text" name="school_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('school_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Board</label>
                                    <input wire:model.live="board" type="text" name="board" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('board') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="mb-3 p-3 border border-primary-100 rounded bg-primary-50">
                            <h3 class="text-lg font-semibold text-primary-800 mb-3">Address Information</h3>
                            
                            <!-- Permanent Address -->
                            <div class="mb-6">
                                <h4 class="text-base font-medium text-gray-700 mb-2">Permanent Address</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                                        <input wire:model.live="address_line1" type="text" name="address_line1" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        @error('address_line1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                        <input type="text" wire:model.live="address_line2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        @error('address_line2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                        <input type="text" wire:model.live="city" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                        <input type="text" wire:model.live="state" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">District *</label>
                                        <input type="text" wire:model.live="district" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        @error('district') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pincode *</label>
                                        <input type="text" wire:model.live="pincode" required maxlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" inputmode="numeric">
                                        @error('pincode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                        <input type="text" wire:model.live="country" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Correspondence Address -->
                            <div class="mt-6">
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" wire:model.live="same_as_permanent" id="same_as_permanent"
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="same_as_permanent" class="ml-2 text-sm font-medium text-gray-700">
                                        Correspondence address same as permanent address
                                    </label>
                                </div>

                                @if (!$same_as_permanent)
                                    <div class="bg-primary-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-700 mb-3">Correspondence Address</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                                                <input type="text" wire:model.live="corr_address_line1" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                @error('corr_address_line1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                                <input type="text" wire:model.live="corr_address_line2"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                @error('corr_address_line2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                                <input type="text" wire:model.live="corr_city" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                @error('corr_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                                <input type="text" wire:model.live="corr_state" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                @error('corr_state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">District *</label>
                                                <input type="text" wire:model.live="corr_district" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                @error('corr_district') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Pincode *</label>
                                                <input type="text" wire:model.live="corr_pincode" required maxlength="6" inputmode="numeric"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                @error('corr_pincode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                                <input type="text" wire:model.live="corr_country" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                @error('corr_country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons for Step 1 -->
                        <div class="mt-6 flex justify-end">
                            <button type="button" wire:click="next"
                                class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                Next: Payment Details
                            </button>
                        </div>
                    </div>

    <!-- Step 2: Payment & Course Details -->
    <div id="step2" class="form-section" x-show="step === 2" x-cloak>
        <div class="mb-3 p-3 border border-primary-100 rounded bg-primary-50">
            <h3 class="text-lg font-semibold text-primary-800 mb-3">Course & Payment Details</h3>
            
            <!-- Course Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Course *</label>
                    <select wire:model.live="course_id" wire:change="onCourseChange" name="course" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }} (₹{{ number_format($course->net_fee, 2) }})</option>
                        @endforeach
                    </select>
                    @error('course_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Batch *</label>
                    <select wire:model.live="batch_id" wire:change="recalculate" name="batch" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500" {{ empty($course_id) ? 'disabled' : '' }}>
                        <option value="">Select Batch</option>
                        @foreach($batches->where('course_id', $course_id ?? 0) as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                    @error('batch_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Payment Mode -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Mode *</label>
                <select wire:model.live="mode" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="full">Full Payment</option>
                    <option value="installment">Installment</option>
                </select>
                @error('mode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Fee Calculation -->
            @if ($course_id)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Fee Calculation</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tuition Fee</label>
                            <input type="number" wire:model.live="tuitionFee" step="0.01" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Other Fee</label>
                            <input type="number" wire:model.live="otherFee" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount (₹)</label>
                            <input type="number" wire:model.live="discount_value" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Fee</label>
                            <input type="number" wire:model="fee_total" step="0.01" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-bold text-primary-600">
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Navigation Buttons for Step 2 -->
        <div class="mt-6 flex justify-between">
            <button type="button" wire:click="prev"
                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                Previous
            </button>
            <button type="submit"
                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                Update Admission
            </button>
        </div>
    </div>

                </form>
            </div>
        </div>
    </div>
</div>
