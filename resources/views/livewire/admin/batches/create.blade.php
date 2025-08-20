<div>
    <form wire:submit.prevent="save" class="bg-white border rounded-xl p-4 space-y-3 max-w-2xl">
        <div>
            <label class="text-xs">Course</label>
            <select class="w-full border rounded p-2" wire:model="course_id">
                <option value="">Select course</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
            @error('course_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-xs">Batch Name</label>
            <input type="text" class="w-full border rounded p-2" wire:model="batch_name">
            @error('batch_name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="text-xs">Start Date</label>
                <input type="date" class="w-full border rounded p-2" wire:model="start_date">
                @error('start_date')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs">End Date</label>
                <input type="date" class="w-full border rounded p-2" wire:model="end_date">
                @error('end_date')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="pt-2">
            <button class="px-4 py-2 rounded-lg bg-black text-white">Save</button>
            <a href="{{ route('admin.batches.index') }}" class="ml-2 px-4 py-2 rounded-lg border">Cancel</a>
        </div>
    </form>
</div>
