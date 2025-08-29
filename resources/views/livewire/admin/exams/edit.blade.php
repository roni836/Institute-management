<body class="bg-gray-50 font-poppins min-h-screen py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-6">
                <h1 class="text-2xl font-semibold text-white flex items-center">
                    <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Exam
                </h1>
                <p class="text-primary-100 mt-1">Update examination details and settings</p>
            </div>

            <div class="p-6">
                @if(session()->has('message'))
                    <div class="p-4 mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start">
                        <svg class="h-5 w-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-medium">Success!</p>
                            <p class="text-sm mt-1">{{ session('message') }}</p>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="update" class="space-y-6">
                    <!-- Batch Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Batch
                        </label>
                        <select wire:model="batch_id" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white">
                            <option value="">-- Select Batch --</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                            @endforeach
                        </select>
                        @error('batch_id') 
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Exam Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Exam Name
                        </label>
                        <input type="text" 
                               wire:model.defer="name"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                               placeholder="Enter exam name (e.g., Mid-term Mathematics)">
                        @error('name') 
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Exam Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Exam Date
                        </label>
                        <input type="date" 
                               wire:model.defer="exam_date"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200">
                        @error('exam_date') 
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 pt-6 border-t border-gray-200">
                        <button type="submit"
                                class="flex-1 px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-all duration-200 font-medium flex items-center justify-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Exam
                        </button>
                        <a href="{{ route('admin.exams.index') }}"
                           class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-4 focus:ring-gray-200 transition-all duration-200 font-medium flex items-center justify-center">
                           <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                           </svg>
                           Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Back to List -->
        <div class="mt-6 text-center">
            <a href="{{ route('admin.exams.index') }}" 
               class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Exam List
            </a>
        </div>
    </div>
</body>