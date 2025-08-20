<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date',
    ];

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }
    public function schedule()
    {
        return $this->belongsTo(PaymentSchedule::class, 'payment_schedule_id');
    }
}
