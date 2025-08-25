<div>
<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-xl font-semibold mb-4">Create New Exam</h1>

    @if(session()->has('message'))
        <div class="p-2 mb-4 bg-green-200 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Batch</label>
            <select wire:model="batch_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Select Batch --</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                @endforeach
            </select>
            @error('batch_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Exam Name</label>
            <input type="text" wire:model.defer="name"
                   class="w-full border rounded px-3 py-2"
                   placeholder="Enter exam name">
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Exam Date</label>
            <input type="date" wire:model.defer="exam_date"
                   class="w-full border rounded px-3 py-2">
            @error('exam_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Subjects & Max Marks</label>
            <div class="space-y-2">
                @foreach($subjects as $subject)
                    <div class="flex items-center space-x-3" 
                        x-data="{ checked: @entangle('selectedSubjects.' . $subject->id . '.checked') }">
                        
                        <!-- Checkbox -->
                        <input type="checkbox" 
                            x-model="checked"
                            wire:model="selectedSubjects.{{ $subject->id }}.checked"
                            value="1"
                            class="h-4 w-4">

                        <span class="w-32">{{ $subject->name }}</span>

                        <!-- Max Marks Input -->
                        <input type="number" 
                            wire:model="selectedSubjects.{{ $subject->id }}.max_marks"
                            class="w-28 border rounded px-2 py-1"
                            placeholder="Marks"
                            x-bind:disabled="!checked">
                    </div>
                @endforeach
            </div>
            @error('selectedSubjects') 
                <span class="text-red-500 text-sm">{{ $message }}</span> 
            @enderror
        </div>



        <div class="flex space-x-2 mt-4">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Create Exam
            </button>
            <a href="{{ route('admin.exams.index') }}"
               class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
               Cancel
            </a>
        </div>
    </form>
</div>
</div>
