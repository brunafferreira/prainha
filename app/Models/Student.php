<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'beach_class_id', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function beachClass(): BelongsTo
    {
        return $this->belongsTo(BeachClass::class, 'beach_class_id');
    }

    public function monthlyFees(): HasMany
    {
        return $this->hasMany(MonthlyFee::class);
    }
}
