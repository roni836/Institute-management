<div title="New Course">
    <form wire:submit.prevent="save" class="bg-white border rounded-xl p-4 space-y-3 max-w-2xl">
        <div>
            <label class="text-xs">Name</label>
            <input type="text" class="w-full border rounded p-2" wire:model="name">
            @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid md:grid-cols-3 gap-3">
            <div>
                <label class="text-xs">Code</label>
                <input type="text" class="w-full border rounded p-2" wire:model="batch_code">
            </div>
            <div>
                <label class="text-xs">Duration (months)</label>
                <input type="number" min="1" class="w-full border rounded p-2" wire:model="duration_months">
                @error('duration_months')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs">Gross Fee (₹)</label>
                <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="gross_fee">
                @error('gross_fee')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="text-xs">Discount (₹)</label>
                <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="discount">
                @error('discount')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs">Net Fee</label>
                <input type="text" readonly class="w-full border rounded p-2 bg-gray-50"
                       value="₹{{ number_format((float)($gross_fee ?? 0) - (float)($discount ?? 0), 2) }}">
            </div>
        </div>

        <div class="pt-2">
            <button class="px-4 py-2 rounded-lg bg-black text-white">Save</button>
            <a href="{{ route('admin.courses.index') }}" class="ml-2 px-4 py-2 rounded-lg border">Cancel</a>
        </div>
    </form>
</div>
