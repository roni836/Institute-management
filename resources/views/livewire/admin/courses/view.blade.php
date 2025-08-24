
    <!-- Tabs -->
    <div x-data="{ tab: 'overview' }" title="View Course" class="bg-white border rounded-xl p-6 max-w-3xl">
        <h2 class="text-lg font-semibold mb-4">Course Details</h2>

        <!-- Course details (same as before) -->
        <dl class="space-y-3 mb-6">
            <div>
                <dt class="text-xs text-gray-500">Name</dt>
                <dd class="text-base font-medium">{{ $course->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Batch Code</dt>
                <dd class="text-base font-medium">{{ $course->batch_code ?? '-' }}</dd>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500">Duration (months)</dt>
                    <dd class="text-base font-medium">{{ $course->duration_months ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Gross Fee</dt>
                    <dd class="text-base font-medium">₹{{ number_format($course->gross_fee, 2) }}</dd>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500">Discount</dt>
                    <dd class="text-base font-medium">₹{{ number_format($course->discount ?? 0, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Net Fee</dt>
                    <dd class="text-base font-medium">
                        ₹{{ number_format((float) ($course->gross_fee ?? 0) - (float) ($course->discount ?? 0), 2) }}
                    </dd>
                </div>
            </div>
        </dl>

        <!-- ✅ Tabs -->
        <div class="mt-8 border-b border-gray-200">
            <nav class="flex gap-4">
                <button @click="tab = 'overview'"
                    :class="tab === 'overview'
                        ?
                        'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 border-b-2 text-sm font-medium">
                    Overview
                </button>
                <button @click="tab = 'batches'"
                    :class="tab === 'batches'
                        ?
                        'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 border-b-2 text-sm font-medium">
                    Batches
                </button>
                <button @click="tab = 'payments'"
                    :class="tab === 'payments'
                        ?
                        'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 border-b-2 text-sm font-medium">
                    Payments
                </button>
                <button @click="tab = 'performance'"
                    :class="tab === 'performance'
                        ?
                        'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 border-b-2 text-sm font-medium">
                    Performance
                </button>
            </nav>
        </div>

        <!-- ✅ Tab Content -->
        <div class="mt-4">
            <!-- Overview -->
            <div x-show="tab === 'overview'" class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Overview</h2>
                <p class="text-gray-600">
                    Shaique is a Computer Science student enrolled in multiple batches.
                    He has completed all fee payments on time and has an excellent academic record.
                </p>
            </div>

            <!-- Batches -->
            <div x-show="tab === 'batches'" class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Batches</h2>
                @if ($course->batches->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left">#</th>
                                    <th class="px-4 py-2 text-left">Batch Name</th>
                                    <th class="px-4 py-2 text-left">Start Date</th>
                                    <th class="px-4 py-2 text-left">End Date</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($course->batches as $i => $batch)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ $i + 1 }}</td>
                                        <td class="px-4 py-2">{{ $batch->batch_name }}</td>
                                        <td class="px-4 py-2">{{ $batch->start_date }}</td>
                                        <td class="px-4 py-2">{{ $batch->end_date }}</td>
                                        <td class="px-4 py-2">
                                            @if ($batch->is_active)
                                                <span
                                                    class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Active</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-600">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No batches found for this course.</p>
                @endif
            </div>

            <!-- Payments -->
            <div x-show="tab === 'payments'" class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Payments</h2>
                <p class="text-gray-500">Payment records will be shown here.</p>
            </div>

            <!-- Performance -->
            <div x-show="tab === 'performance'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Progress Charts -->
                    <div class="bg-white p-6 rounded-xl shadow">
                        <h3 class="text-lg font-semibold mb-4">Overall Progress</h3>
                        <canvas id="progressChart"></canvas>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow">
                        <h3 class="text-lg font-semibold mb-4">Monthly Performance</h3>
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-4">Subject Performance</h3>
                    <canvas id="subjectsChart"></canvas>
                </div>
            </div>
        </div>

    <!-- Alpine.js + Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</div>
