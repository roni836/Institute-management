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
        if(auth()->attempt(['email'=>$this->email,'password'=>$this->password])){
            return redirect()->route('admin.dashboard');
        }
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
