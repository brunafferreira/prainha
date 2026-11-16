<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtReservation extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'participant_count',
        'reservation_date',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'participant_count' => 'integer',
        ];
    }
}
