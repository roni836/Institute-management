<div>
    <h1 class="text-xl font-bold mb-4">Add Students to Exam</h1>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

<div class="overflow-x-auto mb-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-6 py-3">Student Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Batch Name</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $s)
                    <tr class="border-b">
                        <td class="px-6 py-4">{{ $s->student->name }}</td>
                        <td class="px-6 py-4">{{ $s->student->email }}</td>
                        <td class="px-6 py-4">{{ $s->batch->batch_name }}</td>
                        <td class="px-6 py-4">
                         
                            <button>
                            <a href="{{ route('admin.exams.marking', ['exam_id' => $exam->id, 'student_id' => $s->student->id]) }}"
                               class="bg-blue-500 text-white px-3 py-1 rounded">
                                Add
                            </a>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>