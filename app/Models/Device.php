<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'pin_set_at'     => 'datetime',
        'locked_until'   => 'datetime',
        'last_used_at'   => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function hasPin(): bool {
        return !empty($this->pin_hash);
    }
}
