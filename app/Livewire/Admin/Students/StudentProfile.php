<?php
namespace App\Livewire\Admin\Students;

use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class StudentProfile extends Component
{
    #[Validate('required|exists:students,id')]
    public $studentId;

    #[Validate('string|in:overview,courses,payments,performance,attendance')]
    public string $selectedTab = 'overview';

    public $student;

    public bool $isLoading = false;

    public function mount($id)
    {
        $this->studentId = $id;
        $this->loadStudent();
    }

    private function loadStudent()
    {
        $relationships = ['admissions.batch.course'];
        if ($this->selectedTab === 'overview' || $this->selectedTab === 'payments') {
            $relationships[] = 'admissions.transactions';
            $relationships[] = 'admissions.schedules';
        }
        // Optional: eager-load if such a relation exists in your model
        if ($this->selectedTab === 'attendance' && method_exists(Student::class, 'attendanceRecords')) {
            $relationships[] = 'attendanceRecords.admission.batch';
        }

        $this->student = Cache::remember("student_profile_{$this->studentId}_{$this->selectedTab}", 300, function () use ($relationships) {
            return Student::with($relationships)->findOrFail($this->studentId);
        });
    }

    public function updateTab($tab)
    {
        $this->isLoading   = true;
        $this->selectedTab = $tab;
        $this->loadStudent();
        $this->isLoading = false;
    }

    private function getStats()
    {
        return Cache::remember("student_stats_{$this->studentId}_{$this->selectedTab}", 300, function () {
            $admissions = $this->student->admissions;

            return [
                'totalFees'         => $admissions->sum('fee_total'),
                'paidFees'          => $admissions->sum(fn($admission) =>
                    $admission->transactions->where('status', 'success')->sum('amount')
                ),
                'coursesEnrolled'   => $admissions->count(),
                'attendance'        => $this->student->attendance_percentage ?? 0,

                // existing tab-scoped blocks
                'recentActivities'  => $this->selectedTab === 'overview' ? $this->getRecentActivities() : [],
                'batchProgress'     => $this->selectedTab === 'overview' ? $this->getBatchProgress() : [],
                'paymentHistory'    => $this->selectedTab === 'payments' ? $this->getPaymentHistory() : [],

                // NEW: attendance table data for Attendance tab
                'attendanceRecords' => $this->selectedTab === 'attendance' ? $this->getAttendanceRecords() : [],
            ];
        });
    }

    private function getAttendanceRecords(): array
    {
        // Try eager relation on the student (if it exists)
        $records = method_exists($this->student, 'attendanceRecords')
        ? $this->student->attendanceRecords
        : collect();

        // If relation doesn’t exist or is empty, fall back to a model lookup
        if ($records->isEmpty() && class_exists(\App\Models\StudentAttendance::class)) {
            /** @var \Illuminate\Database\Eloquent\Collection $records */
            $records = \App\Models\StudentAttendance::with(['admission.batch'])
                ->where('student_id', $this->studentId)
                ->latest('date')
                ->limit(200) // cap the list for table size; tweak as needed
                ->get();
        }

        // Map to the view-friendly structure
        return $records->map(function ($row) {
            // date handling
            $date    = $row->date ?? $row->created_at ?? null;
            $dateStr = $date ? \Carbon\Carbon::parse($date)->format('d M Y') : '-';

            // batch name via admission->batch, if available
            $batchName = '-';
            if (isset($row->batch_name)) {
                // In case your attendance row already has batch_name
                $batchName = $row->batch_name;
            } elseif (isset($row->admission) && isset($row->admission->batch)) {
                $batchName = $row->admission->batch->batch_name ?? '-';
            }

            return [
                'date'    => $dateStr,
                'batch'   => $batchName,
                'status'  => $row->status ?? 'absent', // expected: present/absent/late
                'remarks' => $row->remarks ?? null,
            ];
        })->values()->toArray();
    }

    public function attendanceRecords()
    {
        return $this->hasMany(StudentAttendance::class)->with('admission.batch');
    }

    private function getRecentActivities()
    {
        $transactions = $this->student->admissions->flatMap->transactions
            ->take(3)
            ->map(fn($tx) => [
                'icon'        => '₹',
                'description' => "Paid ₹{$tx->amount} via {$tx->mode}",
                'time'        => $tx->created_at->diffForHumans(),
            ]);

        $enrollments = $this->student->admissions
            ->take(3)
            ->map(fn($admission) => [
                'icon'        => '📚',
                'description' => "Enrolled in {$admission->batch->course->name}",
                'time'        => $admission->created_at->diffForHumans(),
            ]);

        return $transactions->merge($enrollments)
            ->sortByDesc('time')
            ->take(5)
            ->values();
    }

    private function getPaymentHistory()
    {
        return Cache::remember("student_payment_history_{$this->studentId}", 300, function () {
            $months       = collect();
            $transactions = collect();
            $now          = Carbon::now();

            for ($i = 0; $i < 6; $i++) {
                $date = $now->copy()->subMonths($i);
                $months->put($date->format('M Y'), 0);
            }

            $this->student->admissions->flatMap->transactions
                ->each(function ($tx) use (&$months, &$transactions) {
                    if ($tx->date) {
                        $transactions->push([
                            'date'         => $tx->date->format('d M Y'),
                            'amount'       => $tx->amount ?? 0,
                            'mode'         => $tx->mode ?? '-',
                            'status'       => $tx->status ?? 'pending',
                            'reference_no' => $tx->reference_no ?? '-',
                            'batch'        => optional($tx->admission->batch)->batch_name ?? '-',
                        ]);

                        if ($tx->status === 'success') {
                            $key = $tx->date->format('M Y');
                            if ($months->has($key)) {
                                $months[$key] += $tx->amount;
                            }
                        }
                    }
                });

            return [
                'transactions' => $transactions->sortByDesc('date')->values()->toArray(),
                'chartData'    => [
                    'labels' => $months->keys()->reverse()->values()->toArray(),
                    'data'   => $months->values()->reverse()->values()->toArray(),
                ],
            ];
        });
    }

    private function calculateProgress($admission)
    {
        if (! $admission->batch || ! $admission->batch->start_date || ! $admission->batch->end_date) {
            return 0;
        }

        $startDate = Carbon::parse($admission->batch->start_date);
        $endDate   = Carbon::parse($admission->batch->end_date);
        $now       = Carbon::now();

        if ($now->lt($startDate)) {
            return 0;
        }

        if ($now->gte($endDate)) {
            return 100;
        }

        $totalDays = max(1, $startDate->diffInDays($endDate));
        $elapsed   = $startDate->diffInDays($now);

        return min(100, round(($elapsed / $totalDays) * 100));
    }

    private function getBatchProgress()
    {
        return $this->student->admissions->map(fn($admission) => [
            'batch'    => $admission->batch->batch_name,
            'course'   => $admission->batch->course->name,
            'progress' => $this->calculateProgress($admission),
            'status'   => $admission->status,
        ]);
    }

    private function getPerformanceData()
    {
        $admissions   = $this->student->admissions;
        $totalCourses = $admissions->count();

        $performanceStats = [
            'overall' => [
                'attendance' => 0,
                'completion' => 0,
            ],
            'courses' => [],
        ];

        if ($totalCourses > 0) {
            $completed                   = $admissions->where('status', 'completed')->count();
            $performanceStats['overall'] = [
                'attendance' => number_format($this->student->attendance_percentage ?? 0, 1),
                'completion' => number_format($completed / $totalCourses * 100, 1),
            ];

            $performanceStats['courses'] = $admissions->map(fn($admission) => [
                'course'     => $admission->batch->course->name ?? 'Unknown Course',
                'progress'   => $this->calculateProgress($admission),
                'attendance' => $this->calculateCourseAttendance($admission),
            ])->toArray();
        }

        return $performanceStats;
    }

    private function calculateCourseAttendance($admission)
    {
        return random_int(75, 100); // Replace with actual logic
    }

    private function getCoursesData()
    {
        return $this->student->admissions->map(fn($admission) => [
            'id'             => $admission->batch->course->id,
            'name'           => $admission->batch->course->name,
            'batch'          => $admission->batch->batch_name,
            'admission_date' => $admission->admission_date->format('d M Y'),
            'start_date'     => Carbon::parse($admission->batch->start_date)->format('d M Y'),
            'end_date'       => Carbon::parse($admission->batch->end_date)->format('d M Y'),
            'progress'       => $this->calculateProgress($admission),
            'status'         => $admission->status,
            'fee_total'      => $admission->fee_total,
            'fee_paid'       => $admission->fee_total - $admission->fee_due,
            'attendance'     => $this->calculateCourseAttendance($admission),
        ]);
    }

    public function render()
    {
        return view('livewire.admin.students.student-profile', [
            'stats'            => $this->getStats(),
            'performanceStats' => $this->selectedTab === 'performance' ? $this->getPerformanceData() : [],
            'coursesData'      => $this->selectedTab === 'courses' ? $this->getCoursesData() : [],
            'isLoading'        => $this->isLoading,
        ]);
    }
}
