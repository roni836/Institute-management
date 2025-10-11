<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];
    
    protected $fillable = [
        'name',
        'enrollment_id',
        'email',
        'phone',
        'whatsapp_no',
        'dob',
        'father_name',
        'mother_name',
        'father_occupation',
        'mother_occupation',
        'gender',
        'category',
        'alt_phone',
        'stream',
        'academic_session',
        'school_name',
        'school_address',
        'board',
        'class',
        'roll_no',
        'student_uid',
        'admission_date',
        'address',
        'status',
        'attendance_percentage',
        'total_courses_enrolled',
        'courses_completed',
        'photo',
        'aadhaar_document_path',
    ];

    /**
     * Get addresses for the student.
     */
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

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
