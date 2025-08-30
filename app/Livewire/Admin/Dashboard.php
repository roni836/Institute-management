<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Student, Admission, Transaction, Batch, Course, StudentAttendance};
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        // Calculate KPIs with real data
        $kpis = [
            'students' => Student::count(),
            'admissions' => Admission::count(),
            'due' => Admission::sum('fee_due'),
            'collected_m' => Transaction::where('status', 'success')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // Get recent activities from different models
        $recentActivities = $this->getRecentActivities();

        return view('livewire.admin.dashboard', compact('kpis', 'recentActivities'));
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent admissions
        $recentAdmissions = Admission::with(['student', 'batch.course'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($admission) {
                return [
                    'type' => 'admission',
                    'title' => 'New student admission',
                    'description' => $admission->student->name . ' enrolled in ' . $admission->batch->course->name,
                    'time' => $admission->created_at,
                    'icon' => 'user-plus',
                    'icon_color' => 'text-blue-600',
                    'bg_color' => 'bg-blue-100'
                ];
            });

        // Recent payments
        $recentPayments = Transaction::with(['admission.student'])
            ->where('status', 'success')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'payment',
                    'title' => 'Payment received',
                    'description' => '₹' . number_format($transaction->amount, 2) . ' from ' . $transaction->admission->student->name,
                    'time' => $transaction->created_at,
                    'icon' => 'currency-rupee',
                    'icon_color' => 'text-green-600',
                    'bg_color' => 'bg-green-100'
                ];
            });

        // Recent batches
        $recentBatches = Batch::with('course')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($batch) {
                return [
                    'type' => 'batch',
                    'title' => 'New batch created',
                    'description' => $batch->batch_name . ' - ' . $batch->course->name,
                    'time' => $batch->created_at,
                    'icon' => 'academic-cap',
                    'icon_color' => 'text-purple-600',
                    'bg_color' => 'bg-purple-100'
                ];
            });

        // Recent attendance records
        $recentAttendance = StudentAttendance::with(['admission.student', 'admission.batch.course'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($attendance) {
                $status = $attendance->status === 'present' ? 'marked present' : 'marked absent';
                return [
                    'type' => 'attendance',
                    'title' => 'Attendance recorded',
                    'description' => $attendance->admission->student->name . ' ' . $status . ' in ' . $attendance->admission->batch->course->name,
                    'time' => $attendance->created_at,
                    'icon' => 'clipboard-check',
                    'icon_color' => 'text-indigo-600',
                    'bg_color' => 'bg-indigo-100'
                ];
            });

        // Merge all activities and sort by time
        $activities = $recentAdmissions
            ->concat($recentPayments)
            ->concat($recentBatches)
            ->concat($recentAttendance)
            ->sortByDesc('time')
            ->take(8)
            ->values();

        return $activities;
    }
}
