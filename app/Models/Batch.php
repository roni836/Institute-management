<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_batches');
    }

    public function performances()
    {
        return $this->hasMany(Performance::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

}
