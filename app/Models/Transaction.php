<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
