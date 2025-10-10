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
                <h1 class="text-2xl font-bold text-primary-700 text-center">Student Admission Form</h1>
                
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
                                    <input wire:model="name" type="text" name="name" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Father Name</label>
                                    <input wire:model="father_name" type="text" name="father_name" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('father_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Mother Name</label>
                                    <input wire:model="mother_name" type="text" name="mother_name" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('mother_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile No.</label>
                                    <input wire:model="phone" type="tel" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp No.</label>
                                    <input wire:model="whatsapp_no" type="tel" name="whatsapp_no" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('whatsapp_no') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email ID</label>
                                    <input wire:model="email" type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                    <input wire:model="dob" type="date" name="dob" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('dob') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select wire:model="gender" name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="others">Other</option>
                                    </select>
                                    @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select wire:model="category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
                                    <input wire:model="father_occupation" type="text" name="father_occupation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('father_occupation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mother Occupation</label>
                                    <input wire:model="mother_occupation" type="text" name="mother_occupation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('mother_occupation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="mb-3 p-3 border border-primary-100 rounded bg-primary-50">
                            <h3 class="text-lg font-semibold text-primary-800 mb-3">Address Information</h3>
                            
                            <!-- Permanent Address -->
                            <div class="mb-6">
                                <h4 class="text-base font-medium text-gray-700 mb-2">Permanent Address</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    x-data="addressDropdown('perm')"
                                    x-init="init()"
                                >
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                                        <input wire:model="address_line1" type="text" name="address_line1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        @error('address_line1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                        <input wire:model="address_line2" type="text" name="address_line2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        @error('address_line2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">State/Union Territory</label>
                                        <select wire:model="state" x-model="selectedState" @change="onStateChange()" name="state" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            <option value="">Select State</option>
                                            <template x-for="s in states" :key="s.name">
                                                <option :value="s.name" x-text="s.name"></option>
                                            </template>
                                        </select>
                                        @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
                                        <select wire:model="district" x-model="selectedDistrict" @change="onDistrictChange()" name="district" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" :disabled="!selectedState">
                                            <option value="">Select District</option>
                                            <template x-for="d in districts" :key="d.name">
                                                <option :value="d.name" x-text="d.name"></option>
                                            </template>
                                        </select>
                                        @error('district') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                        <select wire:model="city" x-model="selectedCity" @change="onCityChange()" name="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" :disabled="!selectedDistrict">
                                            <option value="">Select City</option>
                                            <template x-for="c in cities" :key="c">
                                                <option :value="c" x-text="c"></option>
                                            </template>
                                        </select>
                                        @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pin Code</label>
                                        <input wire:model="pincode" type="text" name="pincode" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        @error('pincode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                        <input wire:model="country" type="text" name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="India">
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
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                        x-data="addressDropdown('corr')"
                                        x-init="init()"
                                    >
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                                            <input wire:model="corr_address_line1" x-ref="corr_address_line1" :disabled="same_as_permanent" type="text" name="corr_address_line1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            @error('corr_address_line1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                            <input wire:model="corr_address_line2" x-ref="corr_address_line2" :disabled="same_as_permanent" type="text" name="corr_address_line2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            @error('corr_address_line2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">State/Union Territory</label>
                                            <select wire:model="corr_state" x-model="selectedState" @change="onStateChange()" name="corr_state" :disabled="same_as_permanent" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                <option value="">Select State</option>
                                                <template x-for="s in states" :key="s.name">
                                                    <option :value="s.name" x-text="s.name"></option>
                                                </template>
                                            </select>
                                            @error('corr_state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
                                            <select wire:model="corr_district" x-model="selectedDistrict" @change="onDistrictChange()" name="corr_district" :disabled="!selectedState || same_as_permanent" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                <option value="">Select District</option>
                                                <template x-for="d in districts" :key="d.name">
                                                    <option :value="d.name" x-text="d.name"></option>
                                                </template>
                                            </select>
                                            @error('corr_district') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                            <select wire:model="corr_city" x-model="selectedCity" @change="onCityChange()" name="corr_city" :disabled="!selectedDistrict || same_as_permanent" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                <option value="">Select City</option>
                                                <template x-for="c in cities" :key="c">
                                                    <option :value="c" x-text="c"></option>
                                                </template>
                                            </select>
                                            @error('corr_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
<script>
function addressDropdown(prefix) {
    return {
        states: [],
        districts: [],
        cities: [],
        selectedState: '',
        selectedDistrict: '',
        selectedCity: '',
        async init() {
            const res = await fetch('/india-states-cities.json');
            this.states = await res.json();
            // Set initial values from Livewire
            if (prefix === 'perm') {
                this.selectedState = this.$wire.state || '';
                this.selectedDistrict = this.$wire.district || '';
                this.selectedCity = this.$wire.city || '';
            } else {
                this.selectedState = this.$wire.corr_state || '';
                this.selectedDistrict = this.$wire.corr_district || '';
                this.selectedCity = this.$wire.corr_city || '';
            }
            this.updateDistricts();
            this.updateCities();
        },
        onStateChange() {
            if (prefix === 'perm') this.$wire.state = this.selectedState;
            else this.$wire.corr_state = this.selectedState;
            this.selectedDistrict = '';
            this.selectedCity = '';
            this.updateDistricts();
            this.updateCities();
        },
        onDistrictChange() {
            if (prefix === 'perm') this.$wire.district = this.selectedDistrict;
            else this.$wire.corr_district = this.selectedDistrict;
            this.selectedCity = '';
            this.updateCities();
        },
        onCityChange() {
            if (prefix === 'perm') this.$wire.city = this.selectedCity;
            else this.$wire.corr_city = this.selectedCity;
        },
        updateDistricts() {
            const state = this.states.find(s => s.name === this.selectedState);
            this.districts = state ? state.districts : [];
        },
        updateCities() {
            const district = this.districts.find(d => d.name === this.selectedDistrict);
            this.cities = district ? district.cities : [];
        }
    }
}
</script>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Pin Code</label>
                                            <input wire:model="corr_pincode" x-ref="corr_pincode" :disabled="same_as_permanent" type="text" name="corr_pincode" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            @error('corr_pincode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                            <input wire:model="corr_country" x-ref="corr_country" :disabled="same_as_permanent" type="text" name="corr_country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="India">
                                            @error('corr_country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Class Details -->
                        <div class="mb-3 p-3 border border-primary-100 rounded bg-primary-50">
                            <h3 class="text-lg font-semibold text-primary-800 mb-3">Class Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Student Status</label>
                                    <select wire:model="student_status" name="student_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="alumni">Alumni</option>
                                    </select>
                                    @error('student_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">School Name</label>
                                    <input wire:model="school_name" type="text" name="school_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Enter current school name">
                                    @error('school_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">School Address</label>
                                    <input wire:model="school_address" type="text" name="school_address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Enter school address">
                                    @error('school_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Board</label>
                                    <select wire:model="board" name="board" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Board</option>
                                        <option value="CBSE">CBSE</option>
                                        <option value="ICSE">ICSE</option>
                                        <option value="CISCE">CISCE</option>
                                        <option value="State Board">State Board</option>
                                        <option value="IB">IB</option>
                                        <option value="IGCSE">IGCSE</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('board') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                <label class="text-xs">Class <span class="text-red-500">*</span></label>
                                <select class="w-full border rounded p-2 bg-white" wire:model.live="class">
                                    <option value="">Select class</option>
                                    <option value="5">5th</option>
                                    <option value="6">6th</option>
                                    <option value="7">7th</option>
                                    <option value="8">8th</option>
                                    <option value="9">9th</option>
                                    <option value="10">10th</option>
                                    <option value="11">11th</option>
                                    <option value="12">12th</option>
                                    <option value="13">13th</option>
                                </select>
                                @error('class')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Enrollment ID Preview</label>
                                    <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                                        {{ $stream ? $this->getNextEnrollmentId() : 'Select stream first' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modules & Documents -->
                        <div class="mb-3 p-3 border border-primary-100 rounded bg-primary-50">
                            <h3 class="text-lg font-semibold text-primary-800 mb-3">Additional Modules & Documents</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-white border border-primary-100 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-primary-700 mb-3">Uploads</h4>
                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Student Photo</label>
                                            <input type="file" wire:model="photo_upload" accept="image/*" class="w-full text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                            <div wire:loading wire:target="photo_upload" class="text-xs text-primary-600 mt-1">Uploading photo...</div>
                                            @error('photo_upload') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            <div class="mt-3">
                                                @if($photo_upload)
                                                   <img src="{{ $photo_upload->temporaryUrl() }}" alt="Photo preview" class="h-32 w-32 object-cover rounded-lg shadow" />
                                                @elseif($this->existingPhotoUrl)
                                                    <img src="{{ $this->existingPhotoUrl }}" alt="Uploaded photo" class="h-32 w-32 object-cover rounded-lg shadow" />
                                                @else
                                                    <p class="text-xs text-gray-500">No photo uploaded yet.</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Aadhaar Card</label>
                                            <input type="file" wire:model="aadhaar_upload" accept="image/*,.pdf" class="w-full text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                            <div wire:loading wire:target="aadhaar_upload" class="text-xs text-primary-600 mt-1">Uploading Aadhaar...</div>
                                            @error('aadhaar_upload') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            <div class="mt-3 space-y-2 text-sm text-gray-700">
                                                @if($aadhaar_upload)
                                                    <p class="font-medium">{{ $aadhaar_upload->getClientOriginalName() }}</p>
                                                    @if(str_contains($aadhaar_upload->getMimeType(), 'image'))
                                                            <img src="{{ $aadhaar_upload->temporaryUrl() }}" alt="Aadhaar preview" class="h-32 w-32 object-cover rounded-lg shadow" />
                                                    @endif
                                                @elseif($this->existingAadhaarUrl)
                                                    <a href="{{ $this->existingAadhaarUrl }}" target="_blank" rel="noopener" class="text-primary-600 underline">
                                                        View Aadhaar{{ $this->existingAadhaarFilename ? ' ('.$this->existingAadhaarFilename.')' : '' }}
                                                    </a>
                                                @else
                                                    <p class="text-xs text-gray-500">No Aadhaar document uploaded yet.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white border border-primary-100 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-primary-700 mb-3">Optional Modules</h4>
                                    <div class="grid grid-cols-1 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Module 1</label>
                                            <input type="text" wire:model.defer="module1" name="module1" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            @error('module1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Module 2</label>
                                            <input type="text" wire:model.defer="module2" name="module2" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            @error('module2') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Module 3</label>
                                            <input type="text" wire:model.defer="module3" name="module3" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            @error('module3') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Module 4</label>
                                            <input type="text" wire:model.defer="module4" name="module4" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            @error('module4') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Module 5</label>
                                            <input type="text" wire:model.defer="module5" name="module5" placeholder="Optional" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            @error('module5') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label class="flex items-center">
                                            <input type="checkbox" wire:model.live="id_card_required" name="idCard" class="mr-2">
                                            <span class="text-sm font-medium text-gray-700">ID Card Required</span>
                                        </label>
                                        @error('id_card_required') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Fee Structure -->
                    <div id="step2" class="form-section" x-show="step === 2" x-cloak>
                        <div class="mb-3 p-3 border border-primary-100 rounded bg-primary-50">
                            <h3 class="text-lg font-semibold text-primary-800 mb-3">Fee Structure & Payment</h3>
                            
                            <!-- Contact Info Section -->
                            <div class="mb-3 p-3 bg-white rounded">
                                <h4 class="font-semibold text-sm text-gray-700 mb-2">Contact Information</h4>
                                @php
                                    $selectedStudent = null;
                                    if ($selectedStudentId && !empty($foundStudents)) {
                                        $selectedStudent = collect($foundStudents)->firstWhere('id', $selectedStudentId);
                                    }

                                    $admissionIdentifier = '—';
                                    if ($selectedStudent) {
                                        $admissionIdentifier = data_get($selectedStudent, 'student_uid')
                                            ?? data_get($selectedStudent, 'enrollment_id')
                                            ?? 'Existing Student';
                                    } elseif ($stream) {
                                        $admissionIdentifier = $this->getNextEnrollmentId();
                                    }

                                    $contactSummary = [
                                        ['icon' => '👤', 'label' => 'Student', 'value' => $name ?: '—'],
                                        ['icon' => '📘', 'label' => 'Course', 'value' => data_get($selected_course, 'name', '—')],
                                        ['icon' => '📅', 'label' => 'Session', 'value' => $academic_session ?: ($session ?: '—')],
                                        ['icon' => '👨‍👧', 'label' => 'Father Name', 'value' => $father_name ?: '—'],
                                        ['icon' => '🎓', 'label' => 'Stream', 'value' => $stream ?: '—'],
                                        ['icon' => '🗓️', 'label' => 'Batch Type', 'value' => data_get($selected_batch, 'batch_name', '—')],
                                        ['icon' => '🆔', 'label' => 'Admission Number', 'value' => $admissionIdentifier],
                                        ['icon' => '🏷️', 'label' => 'Category', 'value' => $category ? strtoupper($category) : '—'],
                                    ];
                                @endphp
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    @foreach($contactSummary as $item)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-xs">{{ $item['icon'] }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $item['label'] }}</div>
                                                <div class="text-gray-600">{{ $item['value'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- GST Application -->
                            <div class="mb-3 p-3 bg-primary-600 text-white rounded">
                                <h4 class="font-bold text-base mb-1">GST Application</h4>
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
                                <div class="text-sm text-primary-100 mt-2" wire:loading wire:target="applyGst">
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
                                            <select name="admissionType" class="px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-primary-500">
                                                <option value="Regular">Regular</option>
                                                <option value="Late">Late Admission</option>
                                            </select>
                                        </div>
                                        
                                        <div class="flex justify-between items-center p-3 border rounded-lg">
                                            <span class="text-sm font-medium text-gray-700">Payment Mode</span>
                                            <div class="flex space-x-4">
                                                <label class="inline-flex items-center">
                                                    <input wire:model.live="mode" type="radio" name="paymentMode" value="full" class="form-radio h-4 w-4 text-primary-600" checked>
                                                    <span class="ml-2 text-sm">Full</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input wire:model.live="mode" type="radio" name="paymentMode" value="installment" class="form-radio h-4 w-4 text-primary-600">
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
                                        
                                        <!-- Installment Section - only visible when installment mode is selected -->
                                        <div x-show="mode === 'installment'" class="bg-white border rounded-lg p-4">
                                            <div class="bg-primary-600 text-white p-3 rounded-t-lg -m-4 mb-4">
                                                <h4 class="font-semibold text-lg">Add Installment</h4>
                                            </div>
                                            
                                            <div class="space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-sm font-medium text-gray-700">Select No. Installment</label>
                                                    <div class="flex items-center space-x-2">
                                                        <input wire:model="installments" type="number" min="2" max="12" class="w-16 px-2 py-1 border border-gray-300 rounded text-center" placeholder="3">
                                                        <button type="button" wire:click="recalculate" class="px-3 py-1 bg-primary-500 text-white text-xs rounded hover:bg-primary-600">
                                                            Generate
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Installment Plan Table -->
                                                @if($mode == 'installment' && !empty($plan))
                                                <div class="mt-4">
                                                    <div class="bg-gray-50 rounded-lg p-3">
                                                        @foreach($plan as $index => $p)
                                                        <div class="flex items-center justify-between py-2 {{ $index > 0 ? 'border-t border-gray-200' : '' }}">
                                                            <span class="text-sm font-medium">{{ $p['no'] }}</span>
                                                            <div class="flex items-center space-x-2">
                                                                <input 
                                                                    type="date" 
                                                                    value="{{ $p['due_on'] }}" 
                                                                    wire:change="updateInstallmentDate({{ $index }}, $event.target.value)"
                                                                    class="px-2 py-1 border border-gray-300 rounded text-xs"
                                                                >
                                                                <input 
                                                                    type="number" 
                                                                    value="{{ $p['amount'] }}" 
                                                                    wire:change="updateInstallmentAmount({{ $index }}, $event.target.value)"
                                                                    class="w-20 px-2 py-1 border border-gray-300 rounded text-right text-xs" 
                                                                    step="0.01"
                                                                >
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    
                                                    @error('plan')
                                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right side - Fee Structure -->
                                    <div class="bg-white border rounded-lg">
                                        <div class="bg-primary-600 text-white p-3 rounded-t-lg">
                                            <h4 class="font-semibold text-lg">Fee Structure</h4>
                                        </div>
                                        
                                        <div class="p-4 space-y-3">
                                            <!-- Fee breakdown -->
                                            @if($selected_course)
                                                <div class="space-y-2">
                                                    <div class="flex justify-between py-2">
                                                        <span class="text-sm text-gray-700">Tuition Fee</span>
                                                        <span class="font-medium">{{ number_format($selected_course->tution_fee ?? 18000, 0) }}</span>
                                                    </div>
                                                    
                                                    <div class="flex justify-between py-2">
                                                        <span class="text-sm text-gray-700">Other Fee</span>
                                                        <span class="font-medium">{{ number_format($selected_course->other_fee ?? 17000, 0) }}</span>
                                                    </div>
                                                    
                                                    <div class="flex justify-between py-2">
                                                        <span class="text-sm text-gray-700">Discount</span>
                                                        <span class="font-medium text-red-600">{{ number_format($discount ?? 0, 2) }}</span>
                                                    </div>
                                                    
                                                    <div class="flex justify-between py-2">
                                                        <span class="text-sm text-gray-700">Late Fine + Pre charge</span>
                                                        <span class="font-medium">{{ number_format($lateFee ?? 0, 2) }}</span>
                                                    </div>
                                                    
                                                    <hr class="border-gray-200 my-2">
                                                    
                                                    <div class="flex justify-between py-2">
                                                        <span class="text-sm font-medium text-gray-700">Taxable Amt.</span>
                                                        <span class="font-semibold">{{ number_format($subtotal ?? 35000, 2) }}</span>
                                                    </div>
                                                    
                                                    @if($applyGst)
                                                    <div class="flex justify-between py-2">
                                                        <span class="text-sm text-gray-700">Total Tax</span>
                                                        <span class="font-medium">{{ number_format($gstAmount ?? 6300, 0) }}</span>
                                                    </div>
                                                    @endif
                                                    
                                                    <hr class="border-gray-200 my-2">
                                                    
                                                    <div class="flex justify-between py-3 bg-primary-50 px-3 rounded">
                                                        <span class="text-lg font-bold text-gray-800">Total</span>
                                                        <span class="text-lg font-bold text-primary-600">{{ number_format($fee_total ?? 41300, 0) }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-8">
                                                    <p class="text-gray-500">Please select a course to view fee details</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-4">
                        <button type="button" wire:click="prev" id="prevBtn" x-show="step > 1" class="px-4 py-2 text-sm bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                            Previous
                        </button>
                        <div class="ml-auto">
                            <button type="button" wire:click="next" id="nextBtn" x-show="step < 2" class="px-4 py-2 text-sm bg-primary-600 text-white rounded hover:bg-primary-700 transition-colors">
                                Next Step
                            </button>
                            <button type="button" wire:click="save" id="submitBtn" x-show="step === 2" class="px-4 py-2 text-sm bg-primary-600 text-white rounded hover:bg-primary-700 transition-colors">
                                Submit Application
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
