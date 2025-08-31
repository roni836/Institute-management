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
        'gst'    => 'decimal:2',
        'date'   => 'date',
    ];

    protected $fillable = [
        'admission_id',
        'payment_schedule_id',
        'amount',
        'gst',
        'date',
        'mode',
        'reference_no',
        'status',
        'receipt_number',
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
