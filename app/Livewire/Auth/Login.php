<?php
namespace App\Livewire\Auth;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class Login extends Component
{
    #[Validate('required|email|exists:users,email')]
    public $email = '';

    #[Validate('required|min:5')]
    public $password = '';

    public function mount()
    {
        // Check if user is already authenticated
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return $this->redirect(route('admin.dashboard'));
            } elseif ($user->role === 'teacher') {
                return $this->redirect(route('admin.dashboard'));
            }
        }
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                return $this->redirect(route('admin.dashboard'));
            } elseif ($user->role === 'teacher') {
                return $this->redirect(route('admin.dashboard'));
            } else {
                // Logout immediately if role not allowed
                Auth::logout();
                $this->addError('email', 'You are not authorized to login.');
                return;
            }
        }

        // If login fails
        $this->addError('email', 'Invalid credentials.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
