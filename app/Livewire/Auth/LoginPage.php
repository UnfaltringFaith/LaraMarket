<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class LoginPage extends Component
{
    public string $email = '';
    public string $password = '';

    public function mount()
    {
        if (session()->has('success_reset_password')) {
            LivewireAlert::title(session('success_reset_password'))
            ->position('bottom-end')
            ->success()
            ->timer(2000)
            ->toast()
            ->show();
        }
    }

    /**
     * Login logic.
     *
     * @return void
     */
    public function login()
    {
        // Implement login logic here
        $this->validate([
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->flash('error_credits', 'Invalid credentials. Please try again.');
            return;
        } 

        return redirect()->route('home')->with('success', 'Login successful!');
    }
    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
