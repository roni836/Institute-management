<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount'      => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date'    => 'date',
        'paid_date'   => 'date',
    ];

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

     public function getRemainingAttribute(): string
    {
        return number_format(max(0, (float)$this->amount - (float)$this->paid_amount), 2, '.', '');
    }
}
