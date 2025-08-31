<body class="bg-gray-50 font-poppins min-h-screen py-8">
    <div class="p-6 space-y-6">

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800 shadow-sm flex items-start max-w-2xl mx-auto">
                <svg class="h-5 w-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="font-medium">Success!</p>
                    <p class="text-sm mt-1">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        <!-- Marks Form -->
        <div class="mx-auto max-w-2xl rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Enter Marks
                </h2>
                <p class="text-primary-100 mt-1">Record student performance for each subject</p>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="saveMarks" class="space-y-6">
                    @foreach ($subjects as $subject)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <svg class="h-5 w-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $subject->subject->name ?? 'Subject' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <p class="text-xs text-gray-500">
                                            Max Marks: <span class="font-medium text-primary-600">{{ $subject->max_marks }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="w-32">
                                    <label for="marks-{{ $subject->id }}" class="block text-xs font-medium text-gray-700 mb-1">
                                        Marks Obtained
                                    </label>
                                    <input type="number" id="marks-{{ $subject->id }}"
                                           wire:model="marks.{{ $subject->id }}"
                                           min="0" max="{{ $subject->max_marks }}"
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white"
                                           placeholder="0">
                                    @error('marks.' . $subject->id)
                                        <span class="text-xs text-red-600 mt-1 flex items-center">
                                            <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 rounded-lg bg-primary-600 text-white text-sm font-medium shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-200 transition-all duration-200 hover:shadow-md">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Marks
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Text -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-primary-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-primary-800">
                        <p class="font-medium mb-1">Marking Guidelines</p>
                        <ul class="text-xs space-y-1 text-primary-700">
                            <li>• Enter marks obtained by the student in each subject</li>
                            <li>• Maximum marks for each subject are displayed</li>
                            <li>• All fields are required to save the marks</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>