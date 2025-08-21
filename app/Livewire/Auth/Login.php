<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email|exists:users,email')]
    public $email = '';
 
    #[Validate('required|min:5|')]
    public $password = '';

    public function login(){
        $this->validate();

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = auth()->user();
    
            // Check role and redirect accordingly
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'teacher') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('public.home');
            }
        }
    
        // If login fails
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
