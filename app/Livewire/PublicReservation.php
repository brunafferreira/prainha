<?php

namespace App\Livewire;

use App\Models\CourtReservation;
use App\Support\ReservationSchedule;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PublicReservation extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public int $participant_count = 4;

    public string $reservation_date = '';

    public string $starts_at = '';

    public ?int $confirmationCode = null;

    public function mount(): void
    {
        $this->reservation_date = Carbon::today()->toDateString();
    }

    public function updatedReservationDate(): void
    {
        $this->starts_at = '';
        $this->resetValidation('starts_at');
    }

    public function reserve(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'participant_count' => ['required', 'integer', 'between:1,12'],
            'reservation_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'starts_at' => ['required', 'date_format:H:i', Rule::in(ReservationSchedule::availableTimes($this->reservation_date))],
        ], [
            'starts_at.in' => 'Este horário acabou de ser reservado ou está indisponível. Escolha outro horário.',
        ]);

        $endsAt = Carbon::createFromFormat('H:i', $validated['starts_at'])
            ->addMinutes(ReservationSchedule::DURATION_MINUTES)
            ->format('H:i:s');

        try {
            $reservation = CourtReservation::create([
                ...$validated,
                'ends_at' => $endsAt,
            ]);
        } catch (UniqueConstraintViolationException) {
            $this->addError('starts_at', 'Este horário acabou de ser reservado. Escolha outro horário.');

            return;
        }

        $this->confirmationCode = $reservation->id;
    }

    public function reserveAnother(): void
    {
        $this->reset(['name', 'phone', 'email', 'reservation_date', 'starts_at', 'confirmationCode']);
        $this->participant_count = 4;
        $this->reservation_date = Carbon::today()->toDateString();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.public-reservation', [
            'availableTimes' => ReservationSchedule::availableTimes($this->reservation_date),
        ]);
    }
}
