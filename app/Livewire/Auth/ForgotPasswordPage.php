<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;

class ForgotPasswordPage extends Component
{
    public string $email = '';

    public function mount()
    {
        // Initialize any properties if needed
    }

    /**
     * Reset password logic.
     *
     * @return void
     */
    public function resetPassword()
    {
        $this->validate([
            'email' => 'required|email|max:255|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return session()->flash('success_email_sent', 'Password reset link sent to your email.');
        }

        return redirect()->route('login')->with('success', 'Password reset link sent to your email.');
    }
    public function render()
    {
        return view('livewire.auth.forgot-password-page');
    }
}
