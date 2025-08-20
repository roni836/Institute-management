<div>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label>Student</label>
            <select wire:model="admission.student_id" class="border rounded p-2 w-full">
                @foreach($students as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Batch</label>
            <select wire:model="admission.batch_id" class="border rounded p-2 w-full">
                @foreach($batches as $b)
                    <option value="{{ $b->id }}">{{ $b->batch_name }} ({{ $b->course->name }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Admission Date</label>
            <input type="date" wire:model="admission.admission_date" class="border rounded p-2 w-full">
        </div>

        <div>
            <label>Discount</label>
            <input type="number" wire:model="admission.discount" class="border rounded p-2 w-full">
        </div>

        <div>
            <label>Mode</label>
            <select wire:model="admission.mode" class="border rounded p-2 w-full">
                <option value="full">Full</option>
                <option value="installment">Installment</option>
            </select>
        </div>

        <button class="px-4 py-2 bg-black text-white rounded">Update</button>
    </form>
</div>
