<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public string $mode = 'login';

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $remember = false;

    public function showLogin(): void
    {
        $this->mode = 'login';
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'remember']);
        $this->resetValidation();
    }

    public function showRegister(): void
    {
        $this->mode = 'register';
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'remember']);
        $this->resetValidation();
    }

    public function login()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            $this->addError('email', 'E-mail ou senha inválidos.');

            return null;
        }

        session()->regenerate();

        return $this->redirectRoute('dashboard');
    }

    public function register()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create($validated);

        Auth::login($user);
        session()->regenerate();

        return $this->redirectRoute('dashboard');
    }

    public function render()
    {
        return view('livewire.home');
    }
}
