<?php

namespace App\Livewire\Auth;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AdminPinLogin extends Component
{
     #[Validate('required|digits_between:4,6')]
    public string $pin = '';

    public function mount()
    {
        // If no device cookie or device has no PIN, redirect to password login
        $device = request()->attributes->get('resolvedDevice');
        if (! $device || ! $device->hasPin()) {
            redirect()->route('admin.login')->send();
        }
    }

    public function loginWithPin()
    {
        $this->validate();

        /** @var Device|null $device */
        $device = request()->attributes->get('resolvedDevice');
        if (! $device || ! $device->hasPin()) {
            return redirect()->route('admin.login');
        }

        // lockout protection
        if ($device->locked_until && now()->lessThan($device->locked_until)) {
            $mins = now()->diffInMinutes($device->locked_until) + 1;
            throw ValidationException::withMessages([
                'pin' => "Too many attempts. Try again in {$mins} minute(s)."
            ]);
        }

        if (! Hash::check($this->pin, $device->pin_hash)) {
            $device->increment('failed_attempts');

            if ($device->failed_attempts >= 5) {
                $device->update([
                    'locked_until' => now()->addMinutes(10),
                    'failed_attempts' => 0,
                ]);
            }

            throw ValidationException::withMessages(['pin' => 'Incorrect PIN.']);
        }

        // success
        $device->update([
            'failed_attempts' => 0,
            'locked_until'    => null,
            'last_used_at'    => now(),
            'ip'              => request()->ip(),
            'user_agent'      => request()->userAgent(),
        ]);

        // Log in the admin bound to this device
        Auth::guard('admin')->login($device->admin);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logoutDevice()
    {
        // Delete cookie so next visit shows password screen
        cookie()->queue(cookie()->forget('adm_dev'));
        return redirect()->route('admin.login');
    }

    public function render()
    {
        return view('livewire.auth.admin-pin-login');
    }
}
