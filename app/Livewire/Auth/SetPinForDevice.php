<?php
namespace App\Livewire\Auth;

use App\Models\Device;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SetPinForDevice extends Component
{
    #[Validate('required|digits_between:4,6')]
    public string $pin = '';

    #[Validate('same:pin')]
    public string $pin_confirmation = '';

    public function save()
    {
        $admin = auth('admin')->user();
        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $this->validate();

        $device = $this->currentDeviceFor($admin->id);
        if (! $device) {
            throw ValidationException::withMessages(['pin' => 'Device not found. Please login again.']);
        }

        $device->update([
            'pin_hash'        => Hash::make($this->pin),
            'pin_set_at'      => now(),
            'failed_attempts' => 0,
            'locked_until'    => null,
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    private function currentDeviceFor(int $adminId): ?Device
    {
        $publicId = request()->cookie('adm_dev');
        if (! $publicId) {
            return null;
        }

        return Device::where('public_id', $publicId)->where('admin_id', $adminId)->first();
    }
    public function render()
    {
        return view('livewire.auth.set-pin-for-device');
    }
}
