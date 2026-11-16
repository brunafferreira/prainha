<?php

namespace Tests\Feature;

use App\Livewire\ManageRecords;
use App\Livewire\PublicReservation;
use App\Models\CourtReservation;
use App\Models\User;
use App\Support\ReservationSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_open_the_public_reservation_page(): void
    {
        $this->get(route('reservations.create'))
            ->assertOk()
            ->assertSee('Reserve a quadra')
            ->assertSee('Segunda a sexta: 08h–22h')
            ->assertSee('Reservas de 2 horas');
    }

    public function test_the_public_form_starts_with_today_selected_and_available_times_loaded(): void
    {
        Carbon::setTestNow('2030-01-07 07:00:00');

        try {
            Livewire::test(PublicReservation::class)
                ->assertSet('reservation_date', '2030-01-07')
                ->assertSee('08:00 às 10:00')
                ->assertSee('Confirmar reserva');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_a_public_reservation_is_confirmed_and_blocks_the_slot(): void
    {
        $date = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        Livewire::test(PublicReservation::class)
            ->set('name', 'Mariana Costa')
            ->set('phone', '(11) 99999-0000')
            ->set('email', 'mariana@example.com')
            ->set('participant_count', 8)
            ->set('reservation_date', $date)
            ->set('starts_at', '08:00')
            ->call('reserve')
            ->assertHasNoErrors()
            ->assertSee('Horário reservado!');

        $this->assertDatabaseHas('court_reservations', [
            'name' => 'Mariana Costa',
            'starts_at' => '08:00',
            'ends_at' => '10:00:00',
            'participant_count' => 8,
        ]);
        $this->assertSame($date, CourtReservation::firstOrFail()->reservation_date->format('Y-m-d'));
        $this->assertNotContains('08:00', ReservationSchedule::availableTimes($date));
    }

    public function test_a_taken_or_out_of_hours_slot_cannot_be_reserved(): void
    {
        $saturday = Carbon::now()->next(Carbon::SATURDAY)->toDateString();
        $weekday = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        CourtReservation::create([
            'name' => 'Reserva existente',
            'phone' => '11999990000',
            'participant_count' => 4,
            'reservation_date' => $weekday,
            'starts_at' => '08:00',
            'ends_at' => '10:00',
        ]);

        Livewire::test(PublicReservation::class)
            ->set('name', 'Outro cliente')
            ->set('phone', '11988880000')
            ->set('reservation_date', $weekday)
            ->set('starts_at', '08:00')
            ->call('reserve')
            ->assertHasErrors(['starts_at']);

        Livewire::test(PublicReservation::class)
            ->set('name', 'Cliente de sábado')
            ->set('phone', '11977770000')
            ->set('reservation_date', $saturday)
            ->set('starts_at', '18:00')
            ->call('reserve')
            ->assertHasErrors(['starts_at']);
    }

    public function test_weekday_and_weekend_opening_hours_are_enforced(): void
    {
        $weekday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $saturday = Carbon::now()->next(Carbon::SATURDAY)->toDateString();

        $this->assertContains('20:00', ReservationSchedule::availableTimes($weekday));
        $this->assertNotContains('22:00', ReservationSchedule::availableTimes($weekday));
        $this->assertContains('16:00', ReservationSchedule::availableTimes($saturday));
        $this->assertNotContains('18:00', ReservationSchedule::availableTimes($saturday));
    }

    public function test_an_administrator_can_create_edit_and_cancel_a_reservation(): void
    {
        $date = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageRecords::class, ['section' => 'reservas'])
            ->call('create')
            ->set('reserved_name', 'Pedro Alves')
            ->set('reserved_phone', '11999990000')
            ->set('reservation_date', $date)
            ->set('starts_at', '08:00')
            ->call('save')
            ->assertHasNoErrors();

        $reservation = CourtReservation::where('name', 'Pedro Alves')->firstOrFail();
        $this->assertSame('10:00:00', $reservation->ends_at);

        Livewire::actingAs($user)
            ->test(ManageRecords::class, ['section' => 'reservas'])
            ->call('edit', $reservation->id)
            ->set('reserved_phone', '11988887777')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('court_reservations', ['id' => $reservation->id, 'phone' => '11988887777']);

        Livewire::actingAs($user)
            ->test(ManageRecords::class, ['section' => 'reservas'])
            ->call('delete', $reservation->id);

        $this->assertDatabaseMissing('court_reservations', ['id' => $reservation->id]);
        $this->assertContains('08:00', ReservationSchedule::availableTimes($date));
    }
}
