<?php
namespace App\Livewire\Admin\Teachers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public string $name                   = '';
    public string $email                  = '';
    public bool $autoPassword             = true;
    public ?string $password              = null;
    public ?string $password_confirmation = null;
    public string $phone                  = '';
    public string $address                = '';
    public string $expertise              = '';

    // For displaying the generated password once
    public ?string $generatedPassword = null;

    public function save()
    {
        $rules = [
            'name'         => ['required', 'string', 'max:120'],
            'email'        => ['required', 'email', Rule::unique('users', 'email')],
            'autoPassword' => ['boolean'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string', 'max:500'],
            'expertise'    => ['nullable', 'string', 'max:255'],
        ];

        if (! $this->autoPassword) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $data = $this->validate($rules);

        $plain = $this->autoPassword
        ? Str::password(10) // random strong 10-char password
        : $this->password;

        $user           = new User();
        $user->name     = $this->name;
        $user->email    = $this->email;
        $user->password = Hash::make($plain);
        // simple role column:
        $user->role = 'teacher';
        $user->phone    = $this->phone;
        $user->address  = $this->address;
        $user->expertise = $this->expertise;
        $user->save();

        // If you're using Spatie roles instead of a 'role' column, use:
        // if (method_exists($user, 'assignRole')) { $user->assignRole('Teacher'); }

        $this->generatedPassword = $this->autoPassword ? $plain : null;

        session()->flash(
            'success',
            'Teacher created successfully' . ($this->generatedPassword ? " (password: {$this->generatedPassword})" : '')
        );

        return redirect()->route('admin.teachers.index');
    }

    public function render()
    {
        return view('livewire.admin.teachers.create');
    }
}
