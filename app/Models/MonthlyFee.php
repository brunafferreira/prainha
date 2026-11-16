<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyFee extends Model
{
    protected $fillable = ['student_id', 'amount', 'due_date', 'paid_at', 'status', 'reference'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'due_date' => 'date', 'paid_at' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
