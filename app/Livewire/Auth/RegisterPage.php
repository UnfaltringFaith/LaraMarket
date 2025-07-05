<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Title('Register Page')]
class RegisterPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';

    public function mount()
    {
        // Initialize any properties if needed
    }

    /**
     * Registration logic.
     *
     * @return void
     */
    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registration successful!');

    }

    public function render()
    {
        return view('livewire.auth.register-page');
    }
}
