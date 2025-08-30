<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Register extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|email')]
    public $email = '';
 
    #[Validate('required|min:5')]
    public $password = '';

    #[Validate('required|same:password')]
    public $password_confirmation = '';

    public function register(){
        $this->validate();
        // dd($this->email,$this->password,$this->password_confirmation);
        User::create([
            'name'=> $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password)
        ]);
        return redirect()->route('login');
    }
    public function render()
    {
        return view('livewire.auth.register');
    }
}
