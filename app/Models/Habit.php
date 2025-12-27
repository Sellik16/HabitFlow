<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    // Pozwala na masowe wypełnianie tych pól
    protected $fillable = ['title', 'description', 'user_id', 'current_streak', 'longest_streak'];

    // Relacja: Nawyk należy do użytkownika
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relacja: Nawyk ma wiele rekordów ukończenia (potrzebne do Mapy Cieplnej) 
    public function completions(): HasMany
    {
        return $this->hasMany(Completion::class);
    }
}
