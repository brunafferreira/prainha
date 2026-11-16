<?php

namespace Tests\Feature;

use App\Livewire\Home;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_register_and_is_redirected_to_the_dashboard(): void
    {
        Livewire::test(Home::class)
            ->call('showRegister')
            ->set('name', 'Ana Jogadora')
            ->set('email', 'ana@example.com')
            ->set('password', 'strong-password')
            ->set('password_confirmation', 'strong-password')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
        $this->assertTrue(Hash::check('strong-password', User::where('email', 'ana@example.com')->firstOrFail()->password));
    }

    public function test_a_registered_user_can_log_in_and_is_redirected_to_the_dashboard(): void
    {
        User::factory()->create([
            'email' => 'jogador@example.com',
            'password' => 'strong-password',
        ]);

        Livewire::test(Home::class)
            ->set('email', 'jogador@example.com')
            ->set('password', 'strong-password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_a_guest_cannot_access_the_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_an_authenticated_user_can_view_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Visão geral')
            ->assertSee('Mensalidades');
    }
}
