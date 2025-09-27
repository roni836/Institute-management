<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'type',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'district',
        'pincode',
        'country',
        'is_primary',
    ];

    /**
     * Get the parent addressable model (student, etc).
     */
    public function addressable()
    {
        return $this->morphTo();
    }
}
