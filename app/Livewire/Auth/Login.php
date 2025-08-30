<?php
namespace App\Livewire\Auth;

use App\Models\Device;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class Login extends Component
{
    #[Validate('required|email|exists:users,email')]
    public $email = '';

    #[Validate('required|min:5')]
    public $password = '';

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = Auth::user();
            
            // Debug logging
            Log::info('Login successful for user: ' . $user->email);

            // Resolve device cookie if present
            $publicId = request()->cookie('adm_dev');
            $device = $publicId
                ? Device::where('public_id', $publicId)->where('user_id', $user->id)->first()
                : null;

            if (!$device) {
                // First successful login on this browser: create device row without PIN
                $device = Device::create([
                    'user_id'      => $user->id,
                    'public_id'    => Str::random(60),
                    'name'         => $this->guessDeviceName(),
                    'user_agent'   => request()->userAgent(),
                    'ip'           => request()->ip(),
                    'last_used_at' => now(),
                ]);
                
                Log::info('New device created: ' . $device->name . ' with ID: ' . $device->public_id);
            } else {
                $device->update(['last_used_at' => now()]);
                Log::info('Existing device found: ' . $device->name);
            }

            // Set/update the device cookie for this browser
            Cookie::queue('adm_dev', $device->public_id, 60 * 24 * 365); // 1 year

            // If PIN already set, go straight to dashboard
            if ($device->hasPin()) {
                Log::info('Device has PIN, redirecting to dashboard');
                return $this->redirect(route('admin.dashboard'));
            }

            // Otherwise, force PIN setup once
            Log::info('Device needs PIN setup, redirecting to setpin');
            return $this->redirect(route('admin.setpin'));
        }

        // If login fails
        $this->addError('email', 'Invalid credentials.');
    }

    private function guessDeviceName(): string
    {
        $ua = request()->userAgent() ?? '';
        if (str_contains($ua, 'Windows')) {
            return 'Windows';
        }

        if (str_contains($ua, 'Macintosh')) {
            return 'macOS';
        }

        if (str_contains($ua, 'Linux')) {
            return 'Linux';
        }

        if (str_contains($ua, 'Android')) {
            return 'Android';
        }

        if (str_contains($ua, 'iPhone')) {
            return 'iPhone';
        }

        return 'Unknown Device';
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
