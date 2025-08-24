<?php

namespace App\Livewire\Admin\Students;

use App\Models\Batch;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.admin')]

class StudentProfile extends Component
{
    public $student;
    public $selectedTab = 'overview';

    public function mount($id)
    {
        $this->student = Student::with([
            'admissions.batch.course',
            'admissions.schedules',
            'admissions.transactions',
        ])->findOrFail($id);
    }

    protected function getStats()
    {
        return [
            'totalFees' => $this->student->admissions->sum('fee_total'),
            'paidFees' => $this->student->admissions->sum(function($admission) {
                return $admission->transactions->where('status', 'success')->sum('amount');
            }),
            'coursesEnrolled' => $this->student->admissions->count(),
            'attendance' => $this->student->attendance_percentage ?? 0,
            'recentActivities' => $this->getRecentActivities(),
            'batchProgress' => $this->getBatchProgress(),
            'paymentHistory' => $this->getPaymentHistory(),
        ];
    }

    protected function getRecentActivities()
    {
        $activities = collect();

        // Add recent payments
        $this->student->admissions->each(function($admission) use ($activities) {
            $admission->transactions->take(3)->each(function($tx) use ($activities) {
                $activities->push([
                    'icon' => '₹',
                    'description' => "Paid ₹{$tx->amount} via {$tx->mode}",
                    'time' => $tx->created_at->diffForHumans()
                ]);
            });
        });

        // Add course enrollments
        $this->student->admissions->take(3)->each(function($admission) use ($activities) {
            $activities->push([
                'icon' => '📚',
                'description' => "Enrolled in {$admission->batch->course->name}",
                'time' => $admission->created_at->diffForHumans()
            ]);
        });

        return $activities->sortByDesc('time')->take(5)->values();
    }

    protected function getPaymentHistory()
    {
        $months = collect();
        $now = Carbon::now();
        
        // Get last 6 months
        for ($i = 0; $i < 6; $i++) {
            $date = $now->copy()->subMonths($i);
            $months->put($date->format('M Y'), 0);
        }

        // Sum payments by month
        $this->student->admissions->each(function($admission) use (&$months) {
            $admission->transactions
                ->where('status', 'success')
                ->each(function($transaction) use (&$months) {
                    $key = Carbon::parse($transaction->date)->format('M Y');
                    if ($months->has($key)) {
                        $months[$key] += $transaction->amount;
                    }
                });
        });

        return [
            'labels' => $months->keys()->reverse()->values(),
            'data' => $months->values()->reverse()->values(),
        ];
    }

    protected function getBatchProgress()
    {
        return $this->student->admissions->map(function($admission) {
            $startDate = Carbon::parse($admission->batch->start_date);
            $endDate = Carbon::parse($admission->batch->end_date);
            $total = $startDate->diffInDays($endDate);
            $elapsed = $startDate->diffInDays(now());
            
            return [
                'batch' => $admission->batch->batch_name,
                'course' => $admission->batch->course->name,
                'progress' => min(100, round(($elapsed / $total) * 100)),
                'status' => $admission->status,
            ];
        });
    }

    protected function getPerformanceData()
    {
        $performanceStats = [
            'overall' => [
                'attendance' => 0,
                'completion' => 0,
                'grades' => []
            ],
            'courses' => []
        ];

        $totalCourses = $this->student->admissions->count();
        if ($totalCourses > 0) {
            // Calculate overall performance metrics
            $performanceStats['overall'] = [
                'attendance' => number_format($this->student->attendance_percentage ?? 0, 1),
                'completion' => number_format($this->calculateCompletion(), 1),
                'grades' => $this->calculateGradeDistribution()
            ];

            // Calculate per-course performance
            $performanceStats['courses'] = $this->student->admissions->map(function($admission) {
                return [
                    'course' => $admission->batch->course->name ?? 'Unknown Course',
                    'progress' => $this->calculateCourseProgress($admission),
                    'attendance' => $this->calculateCourseAttendance($admission),
                    'grades' => [
                        'assignments' => $this->calculateAssignmentGrade($admission),
                        'midterm' => $this->calculateMidtermGrade($admission),
                        'final' => $this->calculateFinalGrade($admission)
                    ]
                ];
            })->toArray();
        }

        return $performanceStats;
    }

    protected function calculateCompletion()
    {
        $completed = $this->student->admissions->where('status', 'completed')->count();
        $total = $this->student->admissions->count();
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    protected function calculateGradeDistribution()
    {
        // Placeholder - Replace with actual grade calculation logic
        return [
            'A' => 40,
            'B' => 35,
            'C' => 25
        ];
    }

    protected function calculateCourseProgress($admission)
    {
        if (!$admission->batch) return 0;

        $startDate = Carbon::parse($admission->batch->start_date);
        $endDate = Carbon::parse($admission->batch->end_date);
        $now = Carbon::now();

        if ($now->lt($startDate)) return 0;
        if ($now->gte($endDate)) return 100;

        $totalDays = max(1, $startDate->diffInDays($endDate));
        $elapsed = $startDate->diffInDays($now);

        return min(100, round(($elapsed / $totalDays) * 100));
    }

    protected function calculateCourseAttendance($admission)
    {
        // Placeholder - Replace with actual attendance calculation
        return random_int(75, 100);
    }

    protected function calculateAssignmentGrade($admission)
    {
        // Placeholder - Replace with actual assignment grade calculation
        return random_int(70, 95);
    }

    protected function calculateMidtermGrade($admission)
    {
        // Placeholder - Replace with actual midterm grade calculation
        return random_int(65, 90);
    }

    protected function calculateFinalGrade($admission)
    {
        // Placeholder - Replace with actual final grade calculation
        return random_int(70, 95);
    }

    public function render()
    {
        return view('livewire.admin.students.student-profile', [
            'stats' => $this->getStats(),
            'performanceStats' => $this->getPerformanceData()
        ]);
    }
}
