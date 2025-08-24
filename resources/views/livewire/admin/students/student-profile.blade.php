<div>
<div class="max-w-7xl mx-auto p-6 space-y-6" x-data="{ tab: 'overview' }">
    {{-- Student Header --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="md:flex">
            <div class="p-6 flex gap-6 items-center flex-1">
                <div class="h-24 w-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-3xl font-bold text-white">
                    {{ Str::of($student->name)->substr(0,1)->upper() }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $student->roll_no }} • {{ $student->student_uid }}
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            {{ $student->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Stat cards for totalFees, paidFees, coursesEnrolled, attendance -->
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl border">
        <div class="border-b px-4">
            <nav class="flex space-x-4">
                @foreach(['overview', 'courses', 'payments', 'performance'] as $tab)
                    <button wire:click="$set('selectedTab', '{{ $tab }}')"
                        class="px-4 py-3 text-sm font-medium border-b-2 {{ $selectedTab === $tab 
                            ? 'border-blue-500 text-blue-600' 
                            : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ ucfirst($tab) }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">
            @if($selectedTab === 'overview')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Quick Stats -->
                    <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-white p-4 rounded-xl border">
                            <p class="text-sm text-gray-600">Total Fees</p>
                            <p class="text-2xl font-semibold">₹{{ number_format($stats['totalFees'], 2) }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border">
                            <p class="text-sm text-gray-600">Paid Amount</p>
                            <p class="text-2xl font-semibold text-green-600">₹{{ number_format($stats['paidFees'], 2) }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border">
                            <p class="text-sm text-gray-600">Courses</p>
                            <p class="text-2xl font-semibold">{{ $stats['coursesEnrolled'] }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border">
                            <p class="text-sm text-gray-600">Attendance</p>
                            <p class="text-2xl font-semibold">{{ $stats['attendance'] }}%</p>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white rounded-xl border p-4">
                        <h3 class="font-medium mb-4">Recent Activities</h3>
                        <div class="space-y-4">
                            @foreach($stats['recentActivities'] as $activity)
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="text-blue-600">{{ $activity['icon'] }}</i>
                                    </div>
                                    <div>
                                        <p class="text-sm">{{ $activity['description'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Batch Progress -->
                <div class="bg-white rounded-xl border p-4">
                    <h3 class="font-medium mb-4">Batch Progress</h3>
                    <div class="grid gap-4">
                        @foreach($stats['batchProgress'] as $batch)
                            <div class="border rounded-lg p-3">
                                <div class="flex justify-between mb-2">
                                    <div>
                                        <p class="font-medium">{{ $batch['batch'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $batch['course'] }}</p>
                                    </div>
                                    <span @class([
                                        'px-2 py-1 rounded-full text-xs',
                                        'bg-green-100 text-green-700' => $batch['status'] === 'active',
                                        'bg-blue-100 text-blue-700' => $batch['status'] === 'completed'
                                    ])>{{ ucfirst($batch['status']) }}</span>
                                </div>
                                <div class="relative pt-1">
                                    <div class="flex mb-2 items-center justify-between">
                                        <div>
                                            <span class="text-xs font-semibold inline-block text-primary-600">
                                                Progress
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-semibold inline-block text-primary-600">
                                                {{ $batch['progress'] }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-primary-100">
                                        <div style="width:{{ $batch['progress'] }}%"
                                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary-500">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($selectedTab === 'payments')
                <div class="space-y-6">
                   
 <!-- Payment Transactions Table -->
                    <div class="bg-white rounded-xl border overflow-hidden">
                        <div class="p-4 border-b">
                            <h3 class="font-semibold">Payment Transactions</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($stats['paymentHistory']['transactions'] as $payment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $payment['date'] }}</td>
                                            <td class="px-6 py-4">{{ $payment['batch'] }}</td>
                                            <td class="px-6 py-4">₹{{ number_format($payment['amount'], 2) }}</td>
                                            <td class="px-6 py-4 capitalize">{{ $payment['mode'] }}</td>
                                            <td class="px-6 py-4">
                                                <span @class([
                                                    'px-2 py-1 rounded-full text-xs font-medium',
                                                    'bg-green-100 text-green-800' => $payment['status'] === 'success',
                                                    'bg-yellow-100 text-yellow-800' => $payment['status'] === 'pending',
                                                    'bg-red-100 text-red-800' => $payment['status'] === 'failed'
                                                ])>
                                                    {{ ucfirst($payment['status']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">{{ $payment['reference_no'] ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                No payment records found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($selectedTab === 'courses')
                <div class="space-y-6">
                    <!-- Course Cards -->
                    @forelse($coursesData as $course)
                        <div class="bg-white rounded-xl border p-6">
                            <div class="flex flex-col md:flex-row justify-between gap-4">
                                <!-- Course Info -->
                                <div class="space-y-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $course['name'] }}</h3>
                                    <p class="text-sm text-gray-600">Batch: {{ $course['batch'] }}</p>
                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <span>Admitted: {{ $course['admission_date'] }}</span>
                                        <span>•</span>
                                        <span>{{ $course['start_date'] }} - {{ $course['end_date'] }}</span>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    <span @class([
                                        'px-3 py-1 rounded-full text-sm font-medium',
                                        'bg-green-100 text-green-700' => $course['status'] === 'active',
                                        'bg-blue-100 text-blue-700' => $course['status'] === 'completed',
                                        'bg-red-100 text-red-700' => $course['status'] === 'cancelled',
                                    ])>
                                        {{ ucfirst($course['status']) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Progress & Stats -->
                            <div class="mt-6 space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Course Progress</span>
                                        <span class="font-medium">{{ $course['progress'] }}%</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" 
                                             style="width: {{ $course['progress'] }}%"></div>
                                    </div>
                                </div>

                                <!-- Course Statistics -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
                                    <!-- Attendance -->
                                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600">Attendance</p>
                                        <p class="text-xl font-semibold mt-1">{{ $course['attendance'] }}%</p>
                                    </div>

                                    <!-- Fee Status -->
                                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600">Fee Paid</p>
                                        <p class="text-xl font-semibold mt-1">
                                            ₹{{ number_format($course['fee_paid']) }}
                                            <span class="text-xs text-gray-500">/ ₹{{ number_format($course['fee_total']) }}</span>
                                        </p>
                                    </div>

                                    <!-- Progress Status -->
                                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600">Status</p>
                                        <p class="text-xl font-semibold mt-1">{{ ucfirst($course['status']) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-gray-50 rounded-xl border">
                            <p class="text-gray-500">No courses enrolled yet.</p>
                        </div>
                    @endforelse
                </div>
            @elseif($selectedTab === 'performance')
                {{-- Performance Metrics --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Overall Performance Card -->
                    <div class="bg-white rounded-xl border p-4">
                        <h3 class="text-lg font-semibold mb-4">Overall Performance</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Attendance</span>
                                <div class="w-48 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" 
                                         style="width: {{ $performanceStats['overall']['attendance'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ number_format($performanceStats['overall']['attendance'], 1) }}%</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Course Completion</span>
                                <div class="w-48 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" 
                                         style="width: {{ $performanceStats['overall']['completion'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ number_format($performanceStats['overall']['completion'], 1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course-wise Performance -->
                    @foreach($performanceStats['courses'] as $course)
                        <div class="bg-white rounded-xl border p-4">
                            <h3 class="text-lg font-semibold mb-4">{{ $course['course'] }}</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Progress</span>
                                    <div class="w-48 bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-primary-600 h-2.5 rounded-full" 
                                             style="width: {{ $course['progress'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ $course['progress'] }}%</span>
                                </div>

                                <div class="grid grid-cols-3 gap-4 mt-4">
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <div class="text-2xl font-bold text-primary-600">
                                            {{ $course['grades']['assignments'] }}%
                                        </div>
                                        <div class="text-xs text-gray-500">Assignments</div>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <div class="text-2xl font-bold text-primary-600">
                                            {{ $course['grades']['midterm'] }}%
                                        </div>
                                        <div class="text-xs text-gray-500">Midterm</div>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <div class="text-2xl font-bold text-primary-600">
                                            {{ $course['grades']['final'] }}%
                                        </div>
                                        <div class="text-xs text-gray-500">Final</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Include necessary Alpine.js and Chart.js scripts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('studentProfile', () => ({
            tab: 'overview',
            init() {
                this.$nextTick(() => {
                    this.initCharts();
                });
            },
            initCharts() {
                const performanceStats = @json($performanceStats);

                // Overall Progress Chart
                const ctx1 = document.getElementById('progressChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: performanceStats.overallProgress.labels,
                        datasets: [{
                            label: 'Progress',
                            data: performanceStats.overallProgress.data,
                            backgroundColor: '#4caf50',
                            borderColor: '#388e3c',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Monthly Performance Chart
                const ctx2 = document.getElementById('monthlyChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: performanceStats.monthlyPerformance.labels,
                        datasets: [{
                            label: 'Monthly Performance',
                            data: performanceStats.monthlyPerformance.data,
                            backgroundColor: 'rgba(76, 175, 80, 0.2)',
                            borderColor: '#4caf50',
                            borderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            x: {
                                type: 'time',
                                time: {
                                    unit: 'month',
                                    tooltipFormat: 'MMM DD',
                                    displayFormats: {
                                        month: 'MMM YYYY'
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Performance Score'
                                }
                            }
                        }
                    }
                });

                // Subject-wise Performance Chart
                const ctx3 = document.getElementById('subjectsChart').getContext('2d');
                new Chart(ctx3, {
                    type: 'radar',
                    data: {
                        labels: performanceStats.subjectWisePerformance.labels,
                        datasets: [{
                            label: 'Subject Performance',
                            data: performanceStats.subjectWisePerformance.data,
                            backgroundColor: 'rgba(76, 175, 80, 0.2)',
                            borderColor: '#4caf50',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            r: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }))
    })
</script>


@endpush
</div>
