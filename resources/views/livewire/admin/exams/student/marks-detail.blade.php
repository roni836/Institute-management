<div>
<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">
        {{ $student->name }} – {{ $exam->name }} ({{ $exam->exam_date }})
    </h1>

    <table class="min-w-full divide-y divide-gray-200 border rounded-lg shadow">
        <thead class="bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Subject</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-green-400">Correct ✅</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-red-400">Wrong ❌</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-300">Blank ⬜</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-blue-400">Total 🏆</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @foreach($subjects as $subject)
                @php
                    $marks = $subject->marks->first();
                @endphp
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                        {{ $subject->subject->name }}
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-green-600 font-semibold">
                        {{ $marks->correct ?? 0 }}
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-red-600 font-semibold">
                        {{ $marks->wrong ?? 0 }}
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600 font-semibold">
                        {{ $marks->blank ?? 0 }}
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-blue-600 font-bold">
                        {{ $marks->marks_obtained ?? 0 }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>
