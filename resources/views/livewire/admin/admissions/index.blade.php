<div class="p-4 md:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Admissions</h1>
            <p class="text-gray-600">Manage student applications and admissions</p>
        </div>
        <div class="flex gap-2">
            {{-- IMPORT --}}
            {{-- <form wire:submit.prevent="import" class="flex items-center gap-2">
                <input type="file" wire:model="importFile"
                    class="block text-sm text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-gray-200 file:bg-gray-50 file:text-gray-700 file:text-sm"
                    accept=".xlsx,.xls,.csv" />
                @error('importFile')
                    <span class="text-xs text-red-600">{{ $message }}</span>
                @enderror

                <button type="submit" class="px-4 py-2 rounded-lg bg-primary-600 text-white disabled:opacity-60"
                    wire:loading.attr="disabled" wire:target="import,importFile">
                    <span wire:loading.remove wire:target="import">Import</span>
                    <span wire:loading wire:target="import">Importing…</span>
                </button>
            </form> --}}
            {{-- Export trigger --}}
            <button type="button" wire:click="openExport"
                class="px-4 py-2 rounded-lg border bg-orange-500 hover:bg-orange-600 text-white"
                wire:loading.attr="disabled" wire:target="openExport">
                <span wire:loading.remove wire:target="openExport">Export</span>
                <span wire:loading wire:target="openExport">Opening…</span>
            </button>
            <a href="{{ route('admin.admissions.create') }}"
                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center gap-2">
                <span>+</span> New Admission
            </a>
        </div>
    </div>

    {{-- Export Modal --}}
    @if ($showExportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40" wire:click="closeExport"></div>

            {{-- Dialog --}}
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-4">Export Admissions by Date</h3>

                <div class="space-y-4">
                    <!-- Quick Date Range Selection -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Quick Date Range</label>
                        <select wire:model.live="dateRange" 
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 p-2  ">
                            <option value="">-- Select a date range --</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="last_week">Last Week</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_3_months">Last 3 Months</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="last_year">Last Year</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Choose a predefined range or set custom dates below</p>
                    </div>

                    <!-- Custom Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">From date</label>
                            <input type="date" wire:model.defer="fromDate"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 p-2 ">
                            @error('fromDate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">To date</label>
                            <input type="date" wire:model.defer="toDate"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 p-2 ">
                            @error('toDate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Selected Date Range Display -->
                    @if($fromDate && $toDate)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-blue-800">
                                    <span class="font-medium">Selected Range:</span>
                                    <span class="ml-2">{{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</span>
                                </div>
                                <button type="button" wire:click="clearDateRange" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Clear
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- (Optional) show current list filters summary --}}
                    @if ($status || $batchId || $q)
                        <div class="text-xs text-gray-600 bg-gray-50 rounded-lg p-3">
                            <div class="font-semibold mb-1">Current Filters Applied:</div>
                            @if ($q)
                                <div>Search: <span class="font-medium">{{ $q }}</span></div>
                            @endif
                            @if ($status)
                                <div>Status: <span class="font-medium">{{ $status }}</span></div>
                            @endif
                            @if ($batchId)
                                <div>Batch ID: <span class="font-medium">{{ $batchId }}</span></div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeExport"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>

                    <button type="button" wire:click="export"
                        class="px-4 py-2 rounded-lg border bg-orange-600 hover:bg-orange-700 text-white"
                        wire:loading.attr="disabled" wire:target="export">
                        <span wire:loading.remove wire:target="export">Export</span>
                        <span wire:loading wire:target="export">Preparing…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Admissions -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Total Admissions</p>
                    <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-orange-100 text-orange-500 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Admissions -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Active Admissions</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="p-2 bg-green-100 text-green-500 rounded-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Admissions -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Completed Admissions</p>
                    <p class="text-2xl font-semibold text-gray-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="p-2 bg-gray-100 text-gray-500 rounded-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cancelled Admissions -->
        <div class="bg-white p-4 rounded-xl border">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Cancelled Admissions</p>
                    <p class="text-2xl font-semibold text-red-600">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="p-2 bg-red-100 text-red-500 rounded-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search and filters -->
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="q"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg" placeholder="Search by name, email, or phone">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
            </div>

            <!-- Status and Batch filters -->
            <select wire:model.live="status" class="border rounded-lg px-4 py-2">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <select wire:model.live="batchId" class="border rounded-lg px-4 py-2">
                <option value="">All Batches</option>
                @foreach ($batches as $b)
                    <option value="{{ $b->id }}">{{ $b->batch_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded-xl">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">S.no</th>
                    <th class="text-left p-3">Name & Mobile</th>
                    <th class="text-left p-3">Entrollement Id</th>
                    <th class="text-left p-3">Batch & Course</th>
                    <th class="text-left p-3">Admission Date</th>
                    <th class="text-left p-3">Fee Details</th>
                    {{-- <th class="text-left p-3">Status</th> --}}
                    <th class=" p-3 ">Actions</th>
                    {{--<th class=" p-3 ">Other</th>--}}
                </tr>
            </thead>
            <tbody>
                @forelse($admissions as $i => $admission)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">
                            <div class="font-medium">{{ $i+1 }}.</div>
                        </td>
                        <td class="p-3">
                            <div class="font-medium">{{ $admission->student->name }}</div>
                            <div class="text-xs text-gray-500">{{ $admission->student->phone }}</div>
                        </td>
                        <td class="p-3">
                            <div class="font-medium">{{ $admission->student->enrollment_id }}</div>
                        </td>
                        <td class="p-3">
                            <div>{{ $admission->batch->batch_name }}</div>
                            <div class="text-xs text-gray-500">{{ $admission->batch->course->name }}</div>
                        </td>
                        <td class="p-3">{{ $admission->admission_date->format('d M Y') }}</td>
                        <td class="p-3">
                            <div>Total: ₹{{ number_format($admission->fee_total, 2) }}</div>
                            <div class="text-xs text-gray-500">Due: ₹{{ number_format($admission->fee_due, 2) }}</div>
                        </td>
                        {{-- <td class="p-3">
                            <span @class([
                                'px-2 py-1 rounded-full text-xs font-medium',
                                'bg-green-100 text-green-700' => $admission->status === 'active',
                                'bg-gray-100 text-gray-700' => $admission->status === 'completed',
                                'bg-red-100 text-red-700' => $admission->status === 'cancelled',
                            ])>
                                {{ ucfirst($admission->status) }}
                            </span>
                        </td> --}}
                        <td class="p-2 text-center space-x-2">
                            <a href="{{ route('admin.admissions.show', $admission) }}"
                                class="px-2 py-1 rounded bg-blue-50 hover:bg-blue-100 text-blue-700">View</a>
                            <a href="{{ route('admin.admissions.edit', $admission) }}"
                                class="px-2 py-1 rounded bg-orange-50 hover:bg-orange-100 text-orange-700">Edit</a>
                            <!-- <a href="{{ route('admin.payments.create', ['admission_id' => $admission->id]) }}"
                                class="px-2 py-1 rounded bg-green-50 hover:bg-green-100 text-green-700">Payment</a> -->
                        </td>
                {{--        <td class="p-3">
                        @if($admission->status === 'cancelled')
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-500">
                                Cancelled
                            </span>
                        @else
                            <a href="{{ route('admin.admissions.cancel', $admission) }}" 
                            class="px-2 py-1 rounded bg-red-50 hover:bg-red-100 text-red-700">
                                Cancel Admission
                            </a>
                        @endif
                    </td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-3 text-center text-gray-500">No admissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $admissions->links() }}
    </div>
</div>
