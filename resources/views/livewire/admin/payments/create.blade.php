<div class="max-w-3xl mx-auto p-6 space-y-6">
    <h1 class="text-xl font-semibold">Create Payment</h1>

    @if (session('success'))
        <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    @error('amount')
        <div class="p-3 rounded bg-red-100 text-red-700">{{ $message }}</div>
    @enderror
    @if ($errors->any())
        <div class="p-3 rounded bg-red-100 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Admission (Student — Batch)</label>
            <select wire:model="admission_id" class="border rounded px-3 py-2 w-full">
                <option value="">Select Admission</option>
                @foreach ($admissions as $a)
                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                @endforeach
            </select>
            @error('admission_id')
                <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        @if ($admission_id)
            <div class="text-sm text-gray-700">
                Admission Due Now: <span class="font-semibold">₹ {{ $admission_fee_due }}</span>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Link to Installment (optional)</label>
                <select wire:model="payment_schedule_id" class="border rounded px-3 py-2 w-full">
                    <option value="">No specific installment</option>
                    @foreach ($schedules as $s)
                        <option value="{{ $s['id'] }}">{{ $s['label'] }}</option>
                    @endforeach
                </select>
                @error('payment_schedule_id')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Date</label>
                <input type="date" wire:model="date" class="border rounded px-3 py-2 w-full">
                @error('date')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Amount</label>
                <input type="number" step="0.01" wire:model="amount" class="border rounded px-3 py-2 w-full">
                @error('amount')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Mode</label>
                <select wire:model="mode" class="border rounded px-3 py-2 w-full">
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="online">Online</option>
                </select>
                @error('mode')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Reference No (optional)</label>
                <input type="text" wire:model="reference_no" class="border rounded px-3 py-2 w-full"
                    placeholder="CHQ/UTR">
                @error('reference_no')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select wire:model="status" class="border rounded px-3 py-2 w-full">
                    <option value="success">Success</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
                @error('status')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Payment</button>
        </div>
    </form>
</div>
