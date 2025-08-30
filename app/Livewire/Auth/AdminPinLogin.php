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
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            return $this->redirect(route('admin.dashboard'));
        }

        // If no device cookie or device has no PIN, redirect to password login
        $device = $this->getCurrentDevice();
        if (!$device || !$device->hasPin()) {
            return $this->redirect(route('admin.login'));
        }
    }

    public function loginWithPin()
    {
        $this->validate();

        $device = $this->getCurrentDevice();
        if (!$device || !$device->hasPin()) {
            return $this->redirect(route('admin.login'));
        }

        // lockout protection
        if ($device->isLocked()) {
            $mins = now()->diffInMinutes($device->locked_until) + 1;
            throw ValidationException::withMessages([
                'pin' => "Too many attempts. Try again in {$mins} minute(s)."
            ]);
        }

        if (!$device->verifyPin($this->pin)) {
            throw ValidationException::withMessages(['pin' => 'Incorrect PIN.']);
        }

        // Log in the user bound to this device
        Auth::login($device->user);

        return $this->redirect(route('admin.dashboard'));
    }

    public function logoutDevice()
    {
        // Delete cookie so next visit shows password screen
        cookie()->queue(cookie()->forget('adm_dev'));
        return $this->redirect(route('admin.login'));
    }

    private function getCurrentDevice(): ?Device
    {
        $publicId = request()->cookie('adm_dev');
        if (!$publicId) {
            return null;
        }

        return Device::where('public_id', $publicId)->first();
    }

    public function render()
    {
        return view('livewire.auth.admin-pin-login');
    }
}
