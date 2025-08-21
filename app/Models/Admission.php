<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
    public function schedules()
    {
        return $this->hasMany(PaymentSchedule::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function refreshDue(): void
    {
        $paid          = $this->transactions()->where('status', 'success')->sum('amount');
        $this->fee_due = max(0, bcsub($this->fee_total, $paid, 2));
        $this->save();
    }

    protected $casts = [
        'discount'  => 'decimal:2',
        'fee_total' => 'decimal:2',
        'fee_due'   => 'decimal:2',
        'admission_date' => 'date',
    ];
}
