<?php

namespace App\Livewire;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Settings extends Component
{
    public bool $sidebarOpen = false;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(auth()->id())],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($validated['password']) {
            $user->password = $validated['password'];
        }

        $user->save();
        $this->reset(['password', 'password_confirmation']);
        session()->flash('notice', 'Configurações salvas com sucesso.');
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
