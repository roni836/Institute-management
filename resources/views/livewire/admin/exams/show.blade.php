<div>
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">
            {{ $exam->name }} – {{ $exam->exam_date }}
        </h1>
        <a href="{{ route('admin.exams.index') }}"
           class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Back</a>
    </div>

    <div class="overflow-visible">
        <table class="min-w-full border rounded">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Rank</th>
                    <th class="p-3 text-left">Student</th>
                    <th class="p-3 text-left">Marks Obtained</th>
                    <th class="p-3 text-left">Max Marks</th>
                    <th class="p-3 text-left">Percentage</th>
                    <th class="p-3 text-left">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students_appeared_in_exam as $index => $s)
                    <tr class="border-t">
                        <td class="p-3 font-medium">#{{ $index + 1 }}</td>
                        <td class="p-3">{{ $s['student']->name }}</td>
                        <td class="p-3">{{ $s['total_marks_obtained'] }}</td>
                        <td class="p-3">{{ $s['total_max_marks'] }}</td>
                        <td class="p-3">{{ $s['percentage'] }}%</td>
                        <td class="relative">
                        <div class="group inline-block">
                            <!-- Eye icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <!-- Tooltip -->
                            <div class="absolute left-0 top-0 transform -translate-y-full bg-gray-800 text-white text-sm p-2 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-[9999] w-max">
                                @foreach($students_subject_wise[$s['student']->id] as $sub)
                                    <div>{{ $sub['subject'] }}: {{ $sub['marks_obtained'] }}/{{ $sub['max_marks'] }}</div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            No students appeared in this exam.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
