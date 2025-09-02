<div>
    @if (Auth::user()->isAdmin())

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            @foreach ([
        ['label' => 'Students', 'value' => $kpis['students'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>', 'icon_color' => 'text-blue-600', 'bg_color' => 'bg-blue-100'],
        ['label' => 'Admissions', 'value' => $kpis['admissions'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'icon_color' => 'text-green-600', 'bg_color' => 'bg-green-100'],
        ['label' => 'Fee Due', 'value' => '₹' . number_format($kpis['due']), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'icon_color' => 'text-red-600', 'bg_color' => 'bg-red-100'],
        ['label' => 'Collected (This Month)', 'value' => '₹' . number_format($kpis['collected_m']), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'icon_color' => 'text-yellow-600', 'bg_color' => 'bg-yellow-100'],
    ] as $card)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ $card['label'] }}</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-12 h-12 {{ $card['bg_color'] }} rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $card['icon_color'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                {!! $card['icon'] !!}
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Quick Actions & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-5">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.admissions.create') }}"
                        class="w-full flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Add New Student</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.batches.create') }}"
                        class="w-full flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2" />
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Create New Batch</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.attendance.create') }}"
                        class="w-full flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Mark Attendance</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.admissions.index') }}"
                        class="w-full flex items-center justify-between p-4 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">View Admissions</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-8 h-8 {{ $activity['bg_color'] }} rounded-full flex items-center justify-center flex-shrink-0">
                                @switch($activity['icon'])
                                    @case('user-plus')
                                        <svg class="w-4 h-4 {{ $activity['icon_color'] }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                    @break

                                    @case('currency-rupee')
                                        <svg class="w-4 h-4 {{ $activity['icon_color'] }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @break

                                    @case('academic-cap')
                                        <svg class="w-4 h-4 {{ $activity['icon_color'] }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                    @break

                                    @case('clipboard-check')
                                        <svg class="w-4 h-4 {{ $activity['icon_color'] }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    @break

                                    @default
                                        <svg class="w-4 h-4 {{ $activity['icon_color'] }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                @endswitch
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                                <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-gray-500 text-sm">No recent activities</p>
                                <p class="text-gray-400 text-xs mt-1">Activities will appear here as they happen</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
