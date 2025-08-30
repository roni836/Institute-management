<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
    {{-- Loader --}}
    <div wire:loading wire:target="updateTab"
        class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
        <div class=" flex flex-col gap-3 h-screen items-center justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
            <span class=" flex items-center justify-center text-white text-sm font-medium">Loading...</span>
        </div>
    </div>

    {{-- Student Header --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="md:flex items-center p-6 sm:p-8">
            <div
                class="h-20 w-20 sm:h-24 sm:w-24 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-2xl sm:text-3xl font-bold text-white shadow-md">
                {{ Str::of($student->name)->substr(0, 1)->upper() }}
            </div>
            <div class="ml-0 mt-4 md:mt-0 md:ml-6 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $student->roll_no }} • {{ $student->student_uid }}
                </div>
                <div class="mt-3">
                    <span
                        class="px-3 py-1 rounded-full text-xs font-medium
                        @class([
                            'bg-green-100 text-green-800' => $student->status === 'active',
                            'bg-gray-100 text-gray-800',
                        ])">
                        {{ ucfirst($student->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-2xl border shadow-sm hover:shadow-md transition-shadow">
            <p class="text-sm text-gray-500">Total Fees</p>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900">₹{{ number_format($stats['totalFees'], 2) }}</p>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl border shadow-sm hover:shadow-md transition-shadow">
            <p class="text-sm text-gray-500">Paid Amount</p>
            <p class="text-xl sm:text-2xl font-semibold text-green-600">₹{{ number_format($stats['paidFees'], 2) }}</p>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl border shadow-sm hover:shadow-md transition-shadow">
            <p class="text-sm text-gray-500">Courses</p>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900">{{ $stats['coursesEnrolled'] }}</p>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl border shadow-sm hover:shadow-md transition-shadow">
            <p class="text-sm text-gray-500">Attendance</p>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900">{{ $stats['attendance'] }}%</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="border-b px-4 sm:px-6">
            <nav class="flex flex-wrap gap-2 sm:gap-4" role="tablist">
                @foreach (['overview', 'courses', 'payments', 'performance','attendance'] as $tab)
                    <button wire:click.debounce.500ms="updateTab('{{ $tab }}')"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition-colors
                            @class([
                                'border-blue-500 text-blue-600' => $selectedTab === $tab,
                                'border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-200',
                            ])"
                        role="tab" aria-selected="{{ $selectedTab === $tab ? 'true' : 'false' }}"
                        aria-controls="{{ $tab }}-panel">
                        {{ ucfirst($tab) }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-4 sm:p-6" wire:loading.remove wire:target="updateTab" x-data="{ show: false }"
            x-init="setTimeout(() => show = true, 100)" x-show="show" x-transition.opacity>
            @if ($selectedTab === 'overview')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                        <div class="lg:col-span-2 bg-white p-4 sm:p-6 rounded-2xl border shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h3>
                            <div class="space-y-4">
                                @foreach ($stats['recentActivities'] as $activity)
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 text-sm">{{ $activity['icon'] }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-700">{{ $activity['description'] }}</p>
                                            <p class="text-xs text-gray-400">{{ $activity['time'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="bg-white p-4 sm:p-6 rounded-2xl border shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Batch Progress</h3>
                            <div class="space-y-4">
                                @foreach ($stats['batchProgress'] as $batch)
                                    <div class="border rounded-xl p-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $batch['batch'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $batch['course'] }}</p>
                                            </div>
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-medium
                                                @class([
                                                    'bg-green-100 text-green-800' => $batch['status'] === 'active',
                                                    'bg-blue-100 text-blue-800' => $batch['status'] === 'completed',
                                                ])">
                                                {{ ucfirst($batch['status']) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-600 mb-2">
                                            <span>Progress</span>
                                            <span>{{ $batch['progress'] }}%</span>
                                        </div>
                                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full transition-all duration-300"
                                                style="width: {{ $batch['progress'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($selectedTab === 'payments')
                <div class="space-y-6">
                    <div class="bg-white p-4 sm:p-6 rounded-2xl border shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Payments</h3>
                        <canvas id="paymentChart" class="max-h-80"></canvas>
                    </div>
                    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                        <div class="p-4 sm:p-6 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Payment Transactions</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Date</th>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Batch</th>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Amount</th>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Mode</th>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Reference</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($stats['paymentHistory']['transactions'] as $payment)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $payment['date'] }}</td>
                                            <td class="px-4 sm:px-6 py-4 text-sm text-gray-700">{{ $payment['batch'] }}
                                            </td>
                                            <td class="px-4 sm:px-6 py-4 text-sm text-gray-700">
                                                ₹{{ number_format($payment['amount'], 2) }}</td>
                                            <td class="px-4 sm:px-6 py-4 text-sm capitalize text-gray-700">
                                                {{ $payment['mode'] }}</td>
                                            <td class="px-4 sm:px-6 py-4">
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-medium
                                                    @class([
                                                        'bg-green-100 text-green-800' => $payment['status'] === 'success',
                                                        'bg-yellow-100 text-yellow-800' => $payment['status'] === 'pending',
                                                        'bg-red-100 text-red-800' => $payment['status'] === 'failed',
                                                    ])">
                                                    {{ ucfirst($payment['status']) }}
                                                </span>
                                            </td>
                                            <td class="px-4 sm:px-6 py-4 text-sm text-gray-700">
                                                {{ $payment['reference_no'] ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6"
                                                class="px-4 sm:px-6 py-4 text-center text-sm text-gray-500">
                                                No payment records found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($selectedTab === 'attendance')
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                        <div class="p-4 sm:p-6 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Attendance</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Date</th>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Batch</th>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th scope="col"
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($stats['attendanceRecords'] as $record)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $record['date'] }}</td>
                                            <td class="px-4 sm:px-6 py-4 text-sm text-gray-700">{{ $record['batch'] }}
                                            </td>
                                            <td class="px-4 sm:px-6 py-4">
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-medium
                                                    @class([
                                                        'bg-green-100 text-green-800' => $record['status'] === 'present',
                                                        'bg-red-100 text-red-800' => $record['status'] === 'absent',
                                                        'bg-yellow-100 text-yellow-800' => $record['status'] === 'late',
                                                    ])">
                                                    {{ ucfirst($record['status']) }}
                                                </span>
                                            </td>
                                            <td class="px-4 sm:px-6 py-4 text-sm text-gray-700">
                                                {{ $record['remarks'] ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="px-4 sm:px-6 py-4 text-center text-sm text-gray-500">
                                                No attendance records found
                                            </td>
                                        </tr>
                                    @endforelse
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($selectedTab === 'courses')
                <div class="space-y-6">
                    @forelse($coursesData as $course)
                        <div class="bg-white rounded-2xl border shadow-sm p-4 sm:p-6">
                            <div class="flex flex-col md:flex-row justify-between gap-4">
                                <div class="space-y-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $course['name'] }}</h3>
                                    <p class="text-sm text-gray-500">Batch: {{ $course['batch'] }}</p>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-sm text-gray-500">
                                        <span>Admitted: {{ $course['admission_date'] }}</span>
                                        <span class="hidden sm:inline">•</span>
                                        <span>{{ $course['start_date'] }} - {{ $course['end_date'] }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium
                                        @class([
                                            'bg-green-100 text-green-800' => $course['status'] === 'active',
                                            'bg-blue-100 text-blue-800' => $course['status'] === 'completed',
                                            'bg-red-100 text-red-800' => $course['status'] === 'cancelled',
                                        ])">
                                        {{ ucfirst($course['status']) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-6 space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-600">Course Progress</span>
                                        <span class="font-medium text-gray-900">{{ $course['progress'] }}%</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full transition-all duration-300"
                                            style="width: {{ $course['progress'] }}%"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t">
                                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                                        <p class="text-sm text-gray-500">Attendance</p>
                                        <p class="text-lg font-semibold mt-1 text-gray-900">
                                            {{ $course['attendance'] }}%</p>
                                    </div>
                                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                                        <p class="text-sm text-gray-500">Fee Paid</p>
                                        <p class="text-lg font-semibold mt-1 text-gray-900">
                                            ₹{{ number_format($course['fee_paid']) }}
                                            <span class="text-xs text-gray-400">/
                                                ₹{{ number_format($course['fee_total']) }}</span>
                                        </p>
                                    </div>
                                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                                        <p class="text-sm text-gray-500">Status</p>
                                        <p class="text-lg font-semibold mt-1 text-gray-900">
                                            {{ ucfirst($course['status']) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-gray-50 rounded-2xl border shadow-sm">
                            <p class="text-gray-500">No courses enrolled yet.</p>
                        </div>
                    @endforelse
                </div>
            @elseif($selectedTab === 'performance')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-white rounded-2xl border shadow-sm p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Overall Performance</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Attendance</span>
                                    <div class="w-32 sm:w-48 bg-gray-100 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                                            style="width: {{ $performanceStats['overall']['attendance'] }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ number_format($performanceStats['overall']['attendance'], 1) }}%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Course Completion</span>
                                    <div class="w-32 sm:w-48 bg-gray-100 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                            style="width: {{ $performanceStats['overall']['completion'] }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ number_format($performanceStats['overall']['completion'], 1) }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl border shadow-sm p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course-wise Performance</h3>
                            <div class="space-y-4">
                                @foreach ($performanceStats['courses'] as $course)
                                    <div class="border rounded-xl p-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $course['course'] }}
                                        </h4>
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Progress</span>
                                                <div class="w-32 sm:w-48 bg-gray-100 rounded-full h-2">
                                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                                                        style="width: {{ $course['progress'] }}%"></div>
                                                </div>
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ $course['progress'] }}%</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Attendance</span>
                                                <div class="w-32 sm:w-48 bg-gray-100 rounded-full h-2">
                                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                                        style="width: {{ $course['attendance'] }}%"></div>
                                                </div>
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ $course['attendance'] }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Payment History Chart (Payments Tab)
                const paymentChart = document.getElementById('paymentChart');
                if (paymentChart) {
                    new Chart(paymentChart, {
                        type: 'bar',
                        data: {
                            labels: @json($stats['paymentHistory']['chartData']['labels'] ?? []),
                            datasets: [{
                                label: 'Monthly Payments',
                                data: @json($stats['paymentHistory']['chartData']['data'] ?? []),
                                backgroundColor: '#4CAF50',
                                borderColor: '#388E3C',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Amount (₹)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Month'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</div>
