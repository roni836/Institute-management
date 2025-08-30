<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'pin_set_at'     => 'datetime',
        'locked_until'   => 'datetime',
        'last_used_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasPin(): bool 
    {
        return !empty($this->pin_hash);
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function setPin(string $pin): void
    {
        $this->pin_hash = Hash::make($pin);
        $this->pin_set_at = now();
        $this->failed_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    public function verifyPin(string $pin): bool
    {
        if ($this->isLocked()) {
            return false;
        }

        if (Hash::check($pin, $this->pin_hash)) {
            $this->failed_attempts = 0;
            $this->last_used_at = now();
            $this->save();
            return true;
        }

        $this->failed_attempts++;
        
        // Lock device after 5 failed attempts for 10 minutes
        if ($this->failed_attempts >= 5) {
            $this->locked_until = now()->addMinutes(10);
        }
        
        $this->save();
        return false;
    }

    public function getDeviceName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        // Generate device name from user agent
        $userAgent = $this->user_agent ?? '';
        
        if (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        } else {
            $browser = 'Browser';
        }

        if (preg_match('/Windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iOS/i', $userAgent)) {
            $os = 'iOS';
        } else {
            $os = 'Unknown OS';
        }

        return "{$browser} on {$os}";
    }
}
