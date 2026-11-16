<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BeachClass extends Model
{
    protected $fillable = ['name', 'level', 'weekday', 'starts_at', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'beach_class_id');
    }
}
