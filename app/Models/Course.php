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
