<div>
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Media Management</h1>
                <p class="mt-2 text-sm text-gray-600">View and manage all uploaded photos and Aadhaar cards</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">Total Files: {{ $totalFiles }}</span>
                    @if(count($selectedFiles) > 0)
                        <button wire:click="downloadSelected" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Selected ({{ count($selectedFiles) }})
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Students</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live="search" 
                               type="text" 
                               id="search"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Search by name, enrollment ID, student UID, or roll number...">
                    </div>
                </div>
                
                <!-- Filter Type -->
                <div>
                    <label for="filterType" class="block text-sm font-medium text-gray-700 mb-2">Filter by Type</label>
                    <select wire:model.live="filterType" 
                            id="filterType"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Files</option>
                        <option value="photo">Photos Only</option>
                        <option value="aadhaar">Aadhaar Cards Only</option>
                    </select>
                </div>
            </div>
            
            <!-- Select All Checkbox -->
            @if($students->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               wire:model.live="selectAll" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Select all visible files</span>
                    </label>
                </div>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Media Grid -->
    @if($students->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($students as $student)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Selection Checkbox -->
                    <div class="p-3 bg-gray-50 border-b">
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   wire:click="toggleFileSelection({{ $student->id }})"
                                   @if(in_array($student->id, $selectedFiles)) checked @endif
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium text-gray-700">Select</span>
                        </label>
                    </div>

                    <!-- Student Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $student->name }}</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            @if($student->enrollment_id)
                                <p><span class="font-medium">Enrollment:</span> {{ $student->enrollment_id }}</p>
                            @endif
                            <p><span class="font-medium">Student UID:</span> {{ $student->student_uid }}</p>
                            <p><span class="font-medium">Roll No:</span> {{ $student->roll_no }}</p>
                        </div>
                    </div>

                    <!-- Media Files -->
                    <div class="px-4 pb-4">
                        <div class="space-y-3">
                            <!-- Photo Section -->
                            @if($student->photo)
                                <div class="border rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-medium text-gray-700 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Photo
                                        </h4>
                                        <button wire:click="downloadSingle({{ $student->id }}, 'photo')"
                                                class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                            Download
                                        </button>
                                    </div>
                                    <div class="aspect-w-16 aspect-h-12">
                                        <img src="{{ Storage::url($student->photo) }}"
                                             alt="{{ $student->name }}'s photo"
                                             class="w-full h-32 object-cover rounded border">
                                    </div>
                                </div>
                            @endif

                            <!-- Aadhaar Section -->
                            @if($student->aadhaar_document_path)
                                <div class="border rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-medium text-gray-700 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Aadhaar Card
                                        </h4>
                                        <button wire:click="downloadSingle({{ $student->id }}, 'aadhaar')"
                                                class="text-green-600 hover:text-green-800 text-xs font-medium">
                                            Download
                                        </button>
                                    </div>
                                    @php
                                        $extension = pathinfo($student->aadhaar_document_path, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp
                                    
                                    @if($isImage)
                                        <div class="aspect-w-16 aspect-h-12">
                                            <img src="{{ Storage::url($student->aadhaar_document_path) }}"
                                                 alt="{{ $student->name }}'s Aadhaar card"
                                                 class="w-full h-32 object-cover rounded border">
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center h-32 bg-gray-100 rounded border">
                                            <div class="text-center">
                                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-sm text-gray-500 uppercase">{{ $extension }} Document</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- No Files Message -->
                            @if(!$student->photo && !$student->aadhaar_document_path)
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h4zM9 3v1h6V3H9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14l-1 9H6L5 7z"></path>
                                    </svg>
                                    <p class="text-sm">No files available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $students->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No media files found</h3>
            <p class="text-gray-600 mb-4">
                @if($search)
                    No students found matching "{{ $search }}" with the selected filter.
                @else
                    No students have uploaded photos or Aadhaar cards yet.
                @endif
            </p>
            @if($search)
                <button wire:click="$set('search', '')" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Clear Search
                </button>
            @endif
        </div>
    @endif
</div>

<!-- Loading Indicator -->
<div wire:loading class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">Processing...</span>
        </div>
    </div>
</div>
</div>
