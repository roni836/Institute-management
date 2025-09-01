<div class="max-w-4xl mx-auto p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Edit Student</h1>
            <p class="text-gray-600">Update student information</p>
            @if($this->hasChanges())
                <div class="mt-2 flex items-center text-sm text-amber-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    You have unsaved changes
                </div>
            @endif
        </div>
        <button wire:click="confirmLeave" 
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Back to Students
        </button>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Warning Message -->
    @if (session()->has('warning'))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Form -->
    <form wire:submit.prevent="save" class="bg-white border rounded-xl p-6 space-y-6">
        <!-- Student Photo Section -->
        <div class="flex items-center space-x-6">
            <div class="flex-shrink-0">
                @if($photo)
                    <img src="{{ asset('storage/' . $photo) }}" alt="Student Photo" class="w-24 h-24 rounded-lg object-cover border">
                @else
                    <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Update Photo</label>
                <input type="file" wire:model="newPhoto" accept="image/*" 
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('newPhoto') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 1MB</p>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" wire:model="name" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('name') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="email" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('email') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" wire:model="phone" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('phone') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" wire:model="dob" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('dob') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select wire:model="status" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="completed">Completed</option>
                    </select>
                    @error('status') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Family Information -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Family Information</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
                    <input type="text" wire:model="father_name" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('father_name') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                    <input type="text" wire:model="mother_name" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('mother_name') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Address -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Address</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea wire:model="address" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                @error('address') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Read-only Information -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student UID</label>
                    <input type="text" value="{{ $student->student_uid ?? 'N/A' }}" readonly 
                           class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Roll Number</label>
                    <input type="text" value="{{ $student->roll_no ?? 'N/A' }}" readonly 
                           class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admission Date</label>
                    <input type="text" value="{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('M d, Y') : 'N/A' }}" readonly 
                           class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                    <input type="text" value="{{ $student->created_at ? \Carbon\Carbon::parse($student->created_at)->format('M d, Y H:i') : 'N/A' }}" readonly 
                           class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-gray-600">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="border-t pt-6 flex items-center justify-between">
            <button type="button" 
                    wire:click="resetForm"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Reset Form
            </button>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.students.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:click="save"
                        class="px-6 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="save">Update Student</span>
                    <span wire:loading wire:target="save" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
