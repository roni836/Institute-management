<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Payments</h1>
        <a href="{{ route('admin.payments.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">New Payment</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" wire:model.debounce.400ms="search"
               placeholder="Search by student, batch or reference"
               class="border rounded px-3 py-2">

        <select wire:model="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="success">Success</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
        </select>

        <select wire:model="mode" class="border rounded px-3 py-2">
            <option value="">All Modes</option>
            <option value="cash">Cash</option>
            <option value="cheque">Cheque</option>
            <option value="online">Online</option>
        </select>

        <select wire:model="perPage" class="border rounded px-3 py-2">
            <option>10</option><option>15</option><option>25</option><option>50</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Date</th>
                    <th class="text-left p-3">Student</th>
                    <th class="text-left p-3">Batch</th>
                    <th class="text-left p-3">Amount</th>
                    <th class="text-left p-3">Mode</th>
                    <th class="text-left p-3">Ref</th>
                    <th class="text-left p-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr class="border-t">
                        <td class="p-3">{{ $t->date?->format('d-M-Y') }}</td>
                        <td class="p-3">{{ $t->admission?->student?->name }}</td>
                        <td class="p-3">{{ $t->admission?->batch?->batch_name }}</td>
                        <td class="p-3 font-medium">₹ {{ number_format($t->amount,2) }}</td>
                        <td class="p-3 capitalize">{{ $t->mode }}</td>
                        <td class="p-3">{{ $t->reference_no ?? '—' }}</td>
                        <td class="p-3">
                            <span @class([
                                'px-2 py-1 rounded text-xs',
                                'bg-green-100 text-green-700' => $t->status === 'success',
                                'bg-yellow-100 text-yellow-800' => $t->status === 'pending',
                                'bg-red-100 text-red-700' => $t->status === 'failed',
                            ])>{{ $t->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-500" colspan="7">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $transactions->links() }}</div>
</div>
