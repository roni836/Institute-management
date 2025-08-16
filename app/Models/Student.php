<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [];

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'student_batches');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function performances()
    {
        return $this->hasMany(Performance::class);
    }
}
