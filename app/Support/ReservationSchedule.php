<?php

namespace App\Support;

use App\Models\CourtReservation;
use Illuminate\Support\Carbon;

class ReservationSchedule
{
    public const DURATION_MINUTES = 120;

    public static function availableTimes(?string $date, ?int $ignoreReservationId = null): array
    {
        if (! $date || ! Carbon::canBeCreatedFromFormat($date, 'Y-m-d')) {
            return [];
        }

        [$year, $month, $day] = array_map('intval', explode('-', $date));

        if (! checkdate($month, $day, $year) || $date < Carbon::today()->toDateString()) {
            return [];
        }

        $dayOfWeek = Carbon::createFromFormat('!Y-m-d', $date)->dayOfWeekIso;
        $closingHour = $dayOfWeek >= 6 ? 18 : 22;
        $reservedTimes = CourtReservation::query()
            ->whereDate('reservation_date', $date)
            ->when($ignoreReservationId, fn ($query) => $query->whereKeyNot($ignoreReservationId))
            ->pluck('starts_at')
            ->map(fn ($time) => substr((string) $time, 0, 5))
            ->all();

        $availableTimes = [];

        for ($hour = 8; $hour + 2 <= $closingHour; $hour += 2) {
            $time = sprintf('%02d:00', $hour);

            if (in_array($time, $reservedTimes, true)) {
                continue;
            }

            if ($date === Carbon::today()->toDateString() && $time <= Carbon::now()->format('H:i')) {
                continue;
            }

            $availableTimes[] = $time;
        }

        return $availableTimes;
    }
}
