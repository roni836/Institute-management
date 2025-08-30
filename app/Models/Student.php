<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];

    public function batch()
    {
        return $this->hasOne(Batch::class, 'student_batches');
    }

    public function payments()
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function performances()
    {
        return $this->hasMany(Performance::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_student')->withTimestamps();
    }

    public function attendanceRecords()
    {
        return $this->hasMany(\App\Models\StudentAttendance::class)
            ->latest('date')
            ->with('admission.batch');
    }

}
