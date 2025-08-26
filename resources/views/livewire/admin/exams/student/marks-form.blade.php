<div class="p-6 space-y-6">

<!-- Success Message -->
@if (session()->has('message'))
    <div class="mb-4 rounded-lg bg-green-100 px-4 py-2 text-green-800 shadow">
        {{ session('message') }}
    </div>
@endif

<!-- Marks Form -->
<div class="mx-auto max-w-2xl rounded-xl bg-white p-6 shadow">
    <h2 class="mb-6 text-lg font-bold text-gray-800">Enter Marks</h2>

    <form wire:submit.prevent="saveMarks" class="space-y-5">
        @foreach ($subjects as $subject)
            <div class="flex items-center justify-between gap-4 border-b pb-4 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-700">
                        {{ $subject->subject->name ?? 'Subject' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Max Marks: {{ $subject->max_marks }}
                    </p>
                </div>

                <div class="w-32">
                    <input type="number" id="marks-{{ $subject->id }}"
                           wire:model="marks.{{ $subject->id }}"
                           min="0" max="{{ $subject->max_marks }}"
                           class="w-full rounded-md border-blue-600 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Marks">
                    @error('marks.' . $subject->id)
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @endforeach

        <!-- Submit Button -->
        <div class="pt-4 text-right">
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Save Marks
            </button>
        </div>
    </form>
</div>

</div>
