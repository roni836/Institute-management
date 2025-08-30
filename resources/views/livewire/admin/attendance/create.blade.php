<div>
    <div class="px-6 py-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Attendance
                    </a>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Add Attendance</h1>
                <p class="text-gray-600 text-sm font-medium">Mark attendance for students</p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="$set('showImportModal', true)" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Import Excel
                </button>
                <a href="#" 
                   wire:click.prevent="downloadSampleTemplate"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Template
                </a>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="selectedDate" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" 
                           id="selectedDate"
                           wire:model.live="selectedDate" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="selectedBatch" class="block text-sm font-medium text-gray-700 mb-2">Filter by Batch</label>
                    <select wire:model.live="selectedBatch" 
                            id="selectedBatch"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Batches</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">
                                {{ $batch->course->name ?? 'N/A' }} - {{ $batch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="searchStudent" class="block text-sm font-medium text-gray-700 mb-2">Search Student</label>
                    <input type="text" 
                           id="searchStudent"
                           wire:model.live="searchStudent" 
                           placeholder="Search by name or roll number"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex items-end">
                    <button wire:click="saveAttendance" 
                            class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Save Attendance
                    </button>
                </div>
            </div>
        </div>

        <!-- Attendance Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Attendance</h3>
                <p class="text-sm text-gray-600">Mark attendance for {{ $admissions->total() }} students</p>
            </div>

            @if($admissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Roll No
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Course & Batch
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Remarks
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($admissions as $admission)
                                @php
                                    $key = $admission->student_id . '_' . $admission->id;
                                    $attendance = $attendanceData[$key] ?? null;
                                @endphp
                                @if($attendance)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($admission->student->photo)
                                                        <img class="h-10 w-10 rounded-full object-cover" 
                                                             src="{{ asset('storage/' . $admission->student->photo) }}" 
                                                             alt="{{ $admission->student->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ substr($admission->student->name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $admission->student->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $admission->student->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $admission->student->roll_no }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $admission->batch->course->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Batch: {{ $admission->batch->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select wire:model="attendanceData.{{ $key }}.status" 
                                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                <option value="present">Present</option>
                                                <option value="absent">Absent</option>
                                                <option value="late">Late</option>
                                                <option value="excused">Excused</option>
                                            </select>
                                            @if($attendance['exists'])
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Already marked
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="text" 
                                                   wire:model="attendanceData.{{ $key }}.remarks"
                                                   placeholder="Optional remarks"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $admissions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($selectedBatch || $searchStudent)
                            No students found for the selected filters.
                        @else
                            No active students found in the system.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Excel Import Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="importModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Import Attendance from Excel</h3>
                        <button wire:click="$set('showImportModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    @if (session()->has('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label for="excelFile" class="block text-sm font-medium text-gray-700 mb-2">Select Excel/CSV File</label>
                        <input type="file" 
                               id="excelFile"
                               wire:model="excelFile" 
                               accept=".xlsx,.xls,.csv"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('excelFile') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Required Columns:</h4>
                        <div class="text-xs text-gray-600 space-y-1">
                            <p><strong>roll_no:</strong> Student's roll number (required)</p>
                            <p><strong>student_phone:</strong> Student's phone number (optional, for backup)</p>
                            <p><strong>date:</strong> Attendance date in YYYY-MM-DD format (required)</p>
                            <p><strong>status:</strong> present, absent, late, or excused (required)</p>
                            <p><strong>remarks:</strong> Optional remarks (optional)</p>
                        </div>
                    </div>

                    @if($importErrors)
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-red-800 mb-2">Import Errors:</h4>
                            <ul class="text-xs text-red-700 space-y-1">
                                @foreach($importErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($importSuccess)
                        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        Attendance imported successfully!
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-end space-x-3">
                        <button wire:click="$set('showImportModal', false)" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="importAttendance" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50">
                            <span wire:loading.remove>Import Attendance</span>
                            <span wire:loading>Importing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>