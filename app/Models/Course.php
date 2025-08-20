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

    public function payments()
    {
        return $this->hasMany(PaymentSchedule::class);
    }

     protected $casts = [
        'gross_fee' => 'decimal:2',
        'discount'  => 'decimal:2',
    ];
}
