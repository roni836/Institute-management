<div title="New Course">
    <form wire:submit="save" class="bg-white border rounded-xl p-4 space-y-3 max-w-2xl">
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
            <label class="text-xs">Name</label>
            <input type="text" class="w-full border rounded p-2" wire:model="name">
            @error('name')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="pt-2">
            <button type="submit" class="px-4 py-2 rounded-lg bg-black text-white">Save</button>
            <a href="{{ route('admin.courses.index') }}" class="ml-2 px-4 py-2 rounded-lg border">Cancel</a>
        </div>
    </form>
</div>
