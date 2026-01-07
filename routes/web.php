<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HabitController;
use Illuminate\Support\Facades\Route;

// Przekierowanie ze strony głównej (zależnie od stanu logowania)
Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('dashboard') 
        : redirect()->route('login');
});

// Grupa tras dostępnych tylko dla zalogowanych użytkowników
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Główny panel - pobiera dane przez HabitController [cite: 35]
    Route::get('/dashboard', [HabitController::class, 'index'])->name('dashboard');
    
    // Obsługa dodawania nowych nawyków [cite: 14]
    Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');

    // Zarządzanie profilem użytkownika
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/habits/{habit}', [HabitController::class, 'update'])->name('habits.update');
    Route::delete('/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');

    Route::post('/habits/{habit}/complete', [HabitController::class, 'complete'])->name('habits.complete');
    
});

require __DIR__.'/auth.php';