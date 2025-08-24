<!-- Tabs -->
<div x-data="{ tab: 'overview' }" title="View Subject" class="bg-white border rounded-xl p-6 max-w-3xl">
    <h2 class="text-lg font-semibold mb-4">Subject Details</h2>

    <!-- Course details (same as before) -->
    <dl class="space-y-3 mb-6">
        <div>
            <dt class="text-xs text-gray-500">Name</dt>
            <dd class="text-base font-medium">{{ $subject->name }}</dd>
        </div>
        <div>
            <dt class="text-xs text-gray-500">Course</dt>
            <dd class="text-base font-medium">{{ $subject->course->name }}</dd>
        </div>

    </dl>


</div>
