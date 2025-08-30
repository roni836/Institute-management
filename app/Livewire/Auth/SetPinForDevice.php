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
class SetPinForDevice extends Component
{
    #[Validate('required|digits_between:4,6')]
    public string $pin = '';

    #[Validate('same:pin')]
    public string $pin_confirmation = '';

    public function mount()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return $this->redirect(route('admin.login'));
        }
    }

    public function save()
    {
        $user = Auth::user();
        if (!$user) {
            return $this->redirect(route('admin.login'));
        }

        $this->validate();

        $device = $this->currentDeviceFor($user->id);
        if (!$device) {
            throw ValidationException::withMessages(['pin' => 'Device not found. Please login again.']);
        }

        $device->setPin($this->pin);

        return $this->redirect(route('admin.dashboard'));
    }

    private function currentDeviceFor(int $userId): ?Device
    {
        $publicId = request()->cookie('adm_dev');
        if (!$publicId) {
            return null;
        }

        return Device::where('public_id', $publicId)->where('user_id', $userId)->first();
    }

    public function render()
    {
        return view('livewire.auth.set-pin-for-device');
    }
}
