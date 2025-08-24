<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $guarded = [];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function examSubjects()
    {
        return $this->hasMany(ExamSubject::class);
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'exam_student')->withTimestamps();
    }
}
