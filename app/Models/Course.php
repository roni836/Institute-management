<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = [];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentSchedule::class);
    }
    
    public function admissions()
    {
        return $this->hasManyThrough(Admission::class, Batch::class);
    }
    
    public function students()
    {
        // Get unique students through all batches via admissions
        return $this->hasManyThrough(Student::class, Batch::class, 'course_id', 'id', 'id', 'id')
            ->distinct();
    }
    
    public function getStudentsCountAttribute()
    {
        // Return the count of students in all batches of this course
        return $this->batches()
            ->withCount('admissions')
            ->get()
            ->sum('admissions_count');
    }

     protected $casts = [
        'gross_fee' => 'decimal:2',
        'discount'  => 'decimal:2',
        'tution_fee' => 'decimal:2',
        'admission_fee' => 'decimal:2',
        'exam_fee' => 'decimal:2',
        'infra_fee' => 'decimal:2',
        'SM_fee' => 'decimal:2',
        'tech_fee' => 'decimal:2',
        'other_fee' => 'decimal:2',
    ];
}
