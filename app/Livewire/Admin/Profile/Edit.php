<?php

namespace App\Livewire\Admin\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    #[Validate('required|string|max:120')]
    public $name = '';

    #[Validate('required|email')]
    public $email = '';

    #[Validate('nullable|string|max:20')]
    public $phone = '';

    #[Validate('nullable|string|max:500')]
    public $address = '';

    #[Validate('nullable|string|max:255')]
    public $expertise = '';

    // Password fields
    #[Validate('nullable|string|min:8')]
    public $current_password = '';

    #[Validate('nullable|string|min:8|confirmed')]
    public $new_password = '';

    #[Validate('nullable|string|min:8')]
    public $new_password_confirmation = '';

    public $showPasswordSection = false;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->expertise = $user->expertise;
    }

    public function togglePasswordSection()
    {
        $this->showPasswordSection = !$this->showPasswordSection;
        if (!$this->showPasswordSection) {
            $this->resetPasswordFields();
        }
    }

    public function resetPasswordFields()
    {
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
    }

    public function updateProfile()
    {
        $user = Auth::user();
        
        // Validate email uniqueness (excluding current user)
        $this->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->address = $this->address;
        $user->expertise = $this->expertise;
        
        $user->save();

        session()->flash('success', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        // Update password
        $user->password = Hash::make($this->new_password);
        $user->save();

        // Reset password fields
        $this->resetPasswordFields();
        $this->showPasswordSection = false;

        session()->flash('success', 'Password updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.profile.edit');
    }
}
