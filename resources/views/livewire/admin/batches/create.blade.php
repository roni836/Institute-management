<div class="max-w-4xl mx-auto p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Batch</h1>
            <p class="mt-1 text-sm text-gray-600">Schedule a new batch for your courses</p>
        </div>
        <a href="{{ route('admin.batches.index') }}" 
           class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border">
        <form wire:submit.prevent="save" class="p-6 space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Course Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                    <select class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                            wire:model.live="course_id">
                        <option value="">Select a course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">
                                {{ $course->name }} ({{ $course->duration_months }} months)
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batch Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batch Name</label>
                    <input type="text" wire:model="batch_name" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                           placeholder="Enter batch name">
                    @error('batch_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dates Section -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" wire:model.live="start_date"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        End Date
                        @if($selected_course)
                            <span class="text-xs text-gray-500">({{ $selected_course->duration_months }} months)</span>
                        @endif
                    </label>
                    <input type="date" wire:model="end_date" readonly
                           class="w-full bg-gray-50 border-gray-300 rounded-lg shadow-sm cursor-not-allowed">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t">
                <a href="{{ route('admin.batches.index') }}" 
                   class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 text-sm text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                    Create Batch
                </button>
            </div>
        </form>
    </div>
</div>
