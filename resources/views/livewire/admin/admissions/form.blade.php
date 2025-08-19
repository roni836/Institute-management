<x-layouts.admin :title="$this->admission ? 'Edit Admission' : 'New Admission'">
<form wire:submit.prevent="save" class="grid md:grid-cols-3 gap-4">
    <div class="md:col-span-2 bg-white border rounded-xl p-4 space-y-3">
        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="text-xs">Student</label>
                <select class="w-full border rounded p-2" wire:model="student_id">
                    <option value="">Select student</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->full_name }}</option>
                    @endforeach
                </select>
                @error('student_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs">Batch (Course)</label>
                <select class="w-full border rounded p-2" wire:model="batch_id">
                    <option value="">Select batch</option>
                    @foreach($batches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }} — {{ $b->course->name }} (₹{{ number_format($b->course->fee) }})</option>
                    @endforeach
                </select>
                @error('batch_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-3">
            <div>
                <label class="text-xs">Admission Date</label>
                <input type="date" class="w-full border rounded p-2" wire:model="admission_date">
            </div>
            <div>
                <label class="text-xs">Discount</label>
                <input type="number" min="0" class="w-full border rounded p-2" wire:model.debounce.300ms="discount">
            </div>
            <div>
                <label class="text-xs">Mode</label>
                <select class="w-full border rounded p-2" wire:model="mode">
                    <option value="full">Full</option>
                    <option value="installment">Installment</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="text-xs">Total Payable (after discount)</label>
                <input type="text" readonly class="w-full border rounded p-2 bg-gray-50" value="₹{{ number_format($fee_total) }}">
            </div>
            @if($mode === 'installment')
                <div>
                    <label class="text-xs">No. of Installments</label>
                    <input type="number" min="2" class="w-full border rounded p-2" wire:model="installments">
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white border rounded-xl p-4 space-y-3">
        <h3 class="font-semibold">Payment Plan</h3>
        @if($mode === 'installment')
            <div class="space-y-2 max-h-80 overflow-auto">
                @foreach($plan as $row)
                    <div class="flex justify-between text-sm border rounded p-2">
                        <span>#{{ $row['no'] }}</span>
                        <span>₹{{ number_format($row['amount']) }}</span>
                        <span>Due: {{ $row['due_on'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-600">Full payment. Collect on first day or via Payments.</p>
        @endif

        <div class="pt-2">
            <button class="px-4 py-2 rounded-lg bg-black text-white">Save Admission</button>
            <a href="{{ route('admin.admissions.index') }}" class="ml-2 px-4 py-2 rounded-lg border">Cancel</a>
        </div>
    </div>
</form>
</x-layouts.admin>
