<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
