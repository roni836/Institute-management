<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
