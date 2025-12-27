<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Completion extends Model
{
    protected $fillable = ['habit_id', 'completed_at'];

    // Relacja zwrotna: Ukończenie należy do konkretnego nawyku
    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
