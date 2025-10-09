<div class="p-4 md:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Payments</h1>
            <p class="text-gray-600">Track and manage student fee payments</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.due-payments.index') }}"
                class="px-4 py-2 bg-red-500 text-white rounded-lg flex items-center gap-2">
                Due Payment
            </a>
            <a href="{{ route('admin.payments.create') }}"
                class="px-4 py-2 bg-orange-500 text-white rounded-lg flex items-center gap-2">
                <span>+</span> Record Payment
            </a>

        </div>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Monthly Revenue Card -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Monthly Revenue</p>
                    <p class="text-2xl font-semibold">₹{{ number_format($stats['monthlyRevenue']['amount'], 2) }}</p>
                    <p
                        class="text-xs {{ $stats['monthlyRevenue']['percentChange'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stats['monthlyRevenue']['percentChange'] >= 0 ? '↑' : '↓' }}
                        {{ abs($stats['monthlyRevenue']['percentChange']) }}% from last month
                    </p>
                </div>
                <div class="p-2 bg-orange-100 text-orange-500 rounded-lg">₹</div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Pending Payments</p>
                    <p class="text-2xl font-semibold text-yellow-600">₹{{ number_format($stats['pendingPayments'], 2) }}
                    </p>
                </div>
                <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">₹</div>
            </div>
        </div>

        <!-- Completed Payments -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Completed</p>
                    <p class="text-2xl font-semibold text-green-600">
                        ₹{{ number_format($stats['completedPayments'], 2) }}</p>
                </div>
                <div class="p-2 bg-green-100 text-green-500 rounded-lg">₹</div>
            </div>
        </div>

        <!-- Overdue Payments -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Overdue</p>
                    <p class="text-2xl font-semibold text-red-600">₹{{ number_format($stats['overduePayments'], 2) }}
                    </p>
                </div>
                <div class="p-2 bg-red-100 text-red-500 rounded-lg">₹</div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border mb-6">
        <h2 class="text-xl font-semibold mb-4">Payment Management</h2>
        <p class="text-gray-600 mb-6">Comprehensive payment tracking and fee management system</p>

        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <input type="text" wire:model.debounce.400ms="search"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg" placeholder="Search Payments">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
            </div>
            <div class="flex-shrink-0">
                <select wire:model="status" class="border rounded-lg px-4 py-2">
                    <option value="">All Status</option>
                    <option value="success">Success</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div class="flex-shrink-0">
                <select wire:model="quickRange" class="border rounded-lg px-4 py-2">
                    <option value="">Date Range</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="this_year">This Year</option>
                </select>
            </div>
            <div class="flex-shrink-0">
                <input type="date" wire:model="fromDate" class="border rounded-lg px-4 py-2" placeholder="From date">
            </div>
            <div class="flex-shrink-0">
                <input type="date" wire:model="toDate" class="border rounded-lg px-4 py-2" placeholder="To date">
            </div>
            <div class="flex-shrink-0">
                <button wire:click="exportExcel" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Export Excel
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">S.no</th>
                    <th class="text-left p-3">Date</th>
                    <th class="text-left p-3">Student</th>
                    <th class="text-left p-3">Enrollment Id</th>
                    <th class="text-left p-3">Batch</th>
                    <th class="text-left p-3">Amount</th>
                    <th class="text-left p-3">GST</th>
                    <th class="text-left p-3">Mode</th>
                    <th class="text-left p-3">Receipt No</th>
                    <th class="text-left p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $i=>$t)
                    @php
                        // Get consolidated data for this receipt if it has multiple transactions
                        $consolidatedData = null;
                        if ($t->receipt_number) {
                            $consolidatedTransactions = \App\Models\Transaction::where('receipt_number', $t->receipt_number)
                                ->where('admission_id', $t->admission_id)
                                ->get();
                            
                            if ($consolidatedTransactions->count() > 1) {
                                $consolidatedData = [
                                    'total_amount' => $consolidatedTransactions->sum('amount'),
                                    'total_gst' => $consolidatedTransactions->sum('gst'),
                                    'count' => $consolidatedTransactions->count()
                                ];
                            }
                        }
                    @endphp
                    <tr class="border-t">
                        <td class="p-3">{{ $i+1 }}.</td>
                        <td class="p-3">{{ $t->date?->format('d-M-Y') }}</td>
                        <td class="p-3">{{ $t->admission?->student?->name }}</td>
                        <td class="p-3">{{ $t->admission?->student?->enrollment_id}}</td>
                        <td class="p-3">{{ $t->admission?->batch?->batch_name }}</td>
                        <td class="p-3 font-medium">
                            @if ($consolidatedData)
                                <div class="flex flex-col">
                                    <span>₹ {{ number_format($consolidatedData['total_amount'], 2) }}</span>
                                    <span class="text-xs text-gray-500">({{ $consolidatedData['count'] }} transactions)</span>
                                </div>
                            @else
                                ₹ {{ number_format($t->amount, 2) }}
                            @endif
                        </td>
                        <td class="p-3">
                            @if ($consolidatedData)
                                @if ($consolidatedData['total_gst'] > 0)
                                    <span class="text-blue-600 font-medium">₹ {{ number_format($consolidatedData['total_gst'], 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            @else
                                @if ($t->gst > 0)
                                    <span class="text-blue-600 font-medium">₹ {{ number_format($t->gst, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            @endif
                        </td>
                        <td class="p-3 capitalize">{{ $t->mode }}</td>
                        <td class="p-3">
                            <span class="font-mono text-sm text-blue-600">{{ $t->receipt_number ?? '—' }}</span>
                        </td>
                        <td class="p-3 no-print gap-2">
                            <a href="{{ route('admin.payments.receipt', $t->id) }}"
                                class="px-3 py-1 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700">
                                Receipt
                            </a>
                            <a href="{{ route('admin.payments.edit', $t->id) }}"
                                class="px-3 py-1 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-6 text-center text-gray-500" colspan="7">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $transactions->links() }}</div>
</div>
