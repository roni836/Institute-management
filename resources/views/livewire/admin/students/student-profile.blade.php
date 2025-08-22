<div>
<div class="max-w-6xl mx-auto p-6" x-data="{ tab: 'overview' }">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 flex flex-col md:flex-row items-center gap-6">
        <img class="w-32 h-32 rounded-full object-cover shadow"
             src="https://via.placeholder.com/150"
             alt="Student photo">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{$student->name}}</h1>
            <p class="text-gray-500">Batch name :{{$batch->batch_name}}</p>
            <p class="mt-2 text-sm text-gray-600">{{$student->email}}</p>
            <p class="text-sm text-gray-600">{{$student->phone}}</p>
            <span class="inline-block mt-3 px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mt-8 border-b border-gray-200">
        <nav class="flex gap-4">
            <button @click="tab = 'overview'" 
                    :class="tab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 border-b-2 text-sm font-medium">
                Overview
            </button>
            <button @click="tab = 'batches'" 
                    :class="tab === 'batches' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 border-b-2 text-sm font-medium">
                Batches
            </button>
            <button @click="tab = 'payments'" 
                    :class="tab === 'payments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 border-b-2 text-sm font-medium">
                Payments
            </button>
            <button @click="tab = 'performance'" 
                    :class="tab === 'performance' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 border-b-2 text-sm font-medium">
                Performance
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="mt-4">
        <!-- Overview -->
        <div x-show="tab === 'overview'" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Overview</h2>
            <p class="text-gray-600">Shaique  is a Computer Science student enrolled in multiple batches.  
                He has completed all fee payments on time and has an excellent academic record.</p>
        </div>

        <!-- Batches -->
        <div x-show="tab === 'batches'" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Batches</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-6 py-3">Batch Name</th>
                            <th class="px-6 py-3">Start Date</th>
                            <th class="px-6 py-3">End Date</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="px-6 py-4">{{$batch->batch_name}}</td>
                            <td class="px-6 py-4">{{$batch->start_date}}</td>
                            <td class="px-6 py-4">{{$batch->end_date}}</td>
                            <td class="px-6 py-4">
                                @if($batch->end_date && \Carbon\Carbon::parse($batch->end_date)->isPast())
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Completed</span>
                                @else
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Ongoing</span>
                                @endif
                            </td>                    
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payments -->
        <div x-show="tab === 'payments'" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Payments</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-6 py-3">Ref no.</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Payment Date</th>
                            <th class="px-6 py-3">Mode</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr class="border-b">
                            <td class="px-6 py-4">{{$payment->reference_no}}</td>
                            <td class="px-6 py-4">{{$payment->amount}}</td>
                            <td class="px-6 py-4">{{$payment->date}}</td>
                            <td class="px-6 py-4">{{$payment->mode}}</td>

                            <td class="px-6 py-4"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">{{$payment->status}}</span></td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance -->
        <div x-show="tab === 'performance'" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Performance</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3">Score</th>
                            <th class="px-6 py-3">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="px-6 py-4">Mathematics</td>
                            <td class="px-6 py-4">88</td>
                            <td class="px-6 py-4">A</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4">Computer Science</td>
                            <td class="px-6 py-4">92</td>
                            <td class="px-6 py-4">A+</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4">English</td>
                            <td class="px-6 py-4">76</td>
                            <td class="px-6 py-4">B+</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js (for tabs) -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</div>
