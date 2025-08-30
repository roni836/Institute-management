<?php
namespace App\Livewire\Auth;

use App\Models\Device;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Login extends Component
{
    #[Validate('required|email|exists:users,email')]
    public $email = '';

    #[Validate('required|min:5|')]
    public $password = '';

    public function login()
    {
        $this->validate();

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = auth()->user();

            // Resolve device cookie if present
            $publicId = request()->cookie('adm_dev');
            $device   = $publicId
            ? Device::where('public_id', $publicId)->where('user_id', $user->id)->first()
            : null;

            if (! $device) {
                // First successful login on this browser: create device row without PIN
                $device = Device::create([
                    'user_id'      => $user->id,
                    'public_id'    => Str::random(60),
                    'name'         => $this->guessDeviceName(),
                    'user_agent'   => request()->userAgent(),
                    'ip'           => request()->ip(),
                    'last_used_at' => now(),
                ]);
            } else {
                $device->update(['last_used_at' => now()]);
            }

            // Set/update the device cookie for this browser
            cookie()->queue(cookie()->forever('adm_dev', $device->public_id, null, null, false, true, false, 'strict'));

            // If PIN already set, go straight to dashboard
            if ($device->hasPin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Otherwise, force PIN setup once
            return redirect()->route('admin.setpin');

            // Check role and redirect accordingly
            // if ($user->role === 'admin') {
            //     return redirect()->route('admin.dashboard');
            // } elseif ($user->role === 'teacher') {
            //     return redirect()->route('admin.dashboard');
            // } else {
            //     return redirect()->route('public.home');
            // }

            // If login fails
            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ]);
        }
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
