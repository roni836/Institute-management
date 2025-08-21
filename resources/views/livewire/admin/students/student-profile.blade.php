<div>
<div class="p-6 space-y-6">
    <h2 class="text-2xl font-bold">{{ $student->full_name }}</h2>
    <p>Email: {{ $student->email ?? '—' }}</p>
    <p>Phone: {{ $student->phone ?? '—' }}</p>
    <p>Status: {{ ucfirst($student->status) }}</p>

    <!-- Batches -->
   {{-- <div>
        <h3 class="text-xl font-semibold mt-4">Batches</h3>
        @forelse($student->batches as $batch)
            <p>- {{ $batch->name }} ({{ $batch->start_date }} to {{ $batch->end_date }})</p>
        @empty
            <p class="text-gray-500">No batches assigned.</p>
        @endforelse
    </div>

    <!-- Payments -->
    <div>
        <h3 class="text-xl font-semibold mt-4">Payment Schedules</h3>
        @forelse($student->payments as $payment)
            <p>- ₹{{ $payment->amount }} (Due: {{ $payment->due_date }}) - {{ ucfirst($payment->status) }}</p>
        @empty
            <p class="text-gray-500">No payment records.</p>
        @endforelse
    </div>

    <!-- Transactions -->
    <div>
        <h3 class="text-xl font-semibold mt-4">Transactions</h3>
        @forelse($student->transactions as $transaction)
            <p>- ₹{{ $transaction->amount }} | Method: {{ $transaction->method }} | Date: {{ $transaction->created_at->format('d M Y') }}</p>
        @empty
            <p class="text-gray-500">No transactions found.</p>
        @endforelse
    </div>

    <!-- Performances -->
    <div>
        <h3 class="text-xl font-semibold mt-4">Performance Records</h3>
        @forelse($student->performances as $performance)
            <p>- {{ $performance->subject }}: {{ $performance->score }} ({{ $performance->grade }})</p>
        @empty
            <p class="text-gray-500">No performance records.</p>
        @endforelse
    </div>
--}}
    <!-- Admissions -->
    <div>
        <h3 class="text-xl font-semibold mt-4">Admissions</h3>
        @forelse($student->admissions as $admission)
            <p>- {{ $admission->course_name }} (Admitted on {{ $admission->created_at->format('d M Y') }})</p>
        @empty
            <p class="text-gray-500">No admissions found.</p>
        @endforelse
    </div>
</div>
</div>
