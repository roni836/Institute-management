<div title="New Course">
    <form wire:submit="save" class="bg-white border rounded-xl p-4 space-y-3 max-w-2xl">
        <div>
            <label class="text-xs">Name</label>
            <input type="text" class="w-full border rounded p-2" wire:model="name">
            @error('name')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid md:grid-cols-3 gap-3">
            <div>
                <label class="text-xs">Code</label>
                <input type="text" class="w-full border rounded p-2" wire:model="batch_code">
            </div>
            <div>
                <label class="text-xs">Duration (months)</label>
                <input type="number" min="1" class="w-full border rounded p-2" wire:model="duration_months">
                @error('duration_months')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs">Gross Fee (₹)</label>
                <input type="number" step="0.01" min="0" class="w-full border rounded p-2"
                   wire:model.live="gross_fee">
                @error('gross_fee')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="text-xs">Discount (₹)</label>
                <input type="number" step="0.01" min="0" class="w-full border rounded p-2"
                    wire:model.live="discount">
                @error('discount')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs">Net Fee</label>
                <input type="text" readonly class="w-full border rounded p-2 bg-gray-50"
                    value="₹{{ number_format((float) ($gross_fee ?? 0) - (float) ($discount ?? 0), 2) }}">
            </div>
        </div>

        <!-- Fee Breakdown Section -->
        <div class="border-t pt-4">
            <h3 class="text-sm font-medium mb-3">Fee Breakdown</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                <div>
                    <label class="text-xs">Tuition Fee (₹)</label>
                    <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="tution_fee">
                    @error('tution_fee')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs">Admission Fee (₹)</label>
                    <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="admission_fee">
                    @error('admission_fee')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs">Exam Fee (₹)</label>
                    <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="exam_fee">
                    @error('exam_fee')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs">Infrastructure Fee (₹)</label>
                    <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="infra_fee">
                    @error('infra_fee')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs">SM Fee (₹)</label>
                    <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="SM_fee">
                    @error('SM_fee')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs">Tech Fee (₹)</label>
                    <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="tech_fee">
                    @error('tech_fee')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs">Other Fee (₹)</label>
                    <input type="number" step="0.01" min="0" class="w-full border rounded p-2" wire:model="other_fee">
                    @error('other_fee')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        <div class="pt-2">
            <button type="submit" class="px-4 py-2 rounded-lg bg-black text-white">Save</button>
            <a href="{{ route('admin.courses.index') }}" class="ml-2 px-4 py-2 rounded-lg border">Cancel</a>
        </div>
    </form>
</div>
