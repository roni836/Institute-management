<div>
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">
            {{ $exam->name }} – {{ $exam->exam_date }}
        </h1>
        <div class="flex space-x-2"> 
        <a href="{{ route('admin.exams.index') }}"
           class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
           Back
        </a>
        <a href="{{route('admin.students.create', ['exam_id' => $exam->id])}}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
           Add Student
        </a> 
    </div>
    </div>
    
<div class="overflow-visible">
    <table class="min-w-full border rounded">
        <thead class="bg-gray-50">
            <tr>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Action</th>
            </tr>
        </thead>
        <tbody>
       
            @foreach($students as $s)
                <tr>
                    <td>{{ $s->student->name }}</td>
                    <td>{{ $s->student->email }}</td>
                    <td class="p-3">
                        <a href=""
                           class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                            Edit details
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>
