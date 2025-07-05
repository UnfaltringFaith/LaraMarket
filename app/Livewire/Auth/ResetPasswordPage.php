<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Mockery\Generator\StringManipulation\Pass\Pass;

#[Title('Reset Password')]
class ResetPasswordPage extends Component
{
    public string $password = '';
    public string $password_confirmation = '';
    public string $token;
    #[Url]
    public string $email = '';


    public function render()
    {
        return view('livewire.auth.reset-password-page');
    }

    public function mount(string $token)
    {
        $this->token = $token;
        $email = request()->query('email');
    }

    public function resetPassword()
    {
        // Logic for resetting the password will go here.
        $this->validate([
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required|string',
        ]);

        $status = Password::reset(
            [
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'token' => $this->token,
            ], 
            function (User $user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                ]);
                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }

        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success_reset_password', 'Password reset successfully.')
            : session()->flash('error_password_reset', 'Failed to reset password. Please try again.');
    }
}
