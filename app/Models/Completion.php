<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Completion extends Model
{
    protected $fillable = ['habit_id', 'completed_at'];

    // To jest kluczowa zmiana:
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }
}