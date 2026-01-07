<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habit; // Importujemy model Habit
use Carbon\Carbon;
use App\Models\Completion;

class HabitController extends Controller
{
    /**
     * Wyświetla listę nawyków na Dashboardzie.
     */
    public function index()
    {
        // Pobieramy nawyki zalogowanego użytkownika [cite: 52]
        // Na razie, jeśli baza jest pusta, zwróci pustą kolekcję
        $habits = auth()->user()->habits ?? collect();

        return view('dashboard', compact('habits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // Tworzymy nawyk przypisany do zalogowanego użytkownika
        auth()->user()->habits()->create([
            'title' => $request->title,
            'current_streak' => 0,
            'longest_streak' => 0,
        ]);

        return redirect()->back()->with('success', 'Nawyk dodany!');
    }

    public function complete(Habit $habit)
{
    $this->authorizeOwner($habit);

    $today = Carbon::today();

    // 1. Sprawdź czy już dzisiaj zaliczono (unikamy duplikatów)
    $alreadyCompleted = $habit->completions()->whereDate('completed_at', $today)->exists();

    if (!$alreadyCompleted) {
        // 2. Dodaj wpis do tabeli completions
        $habit->completions()->create([
            'completed_at' => $today
        ]);

        // 3. Logika zwiększania streaka
        // Sprawdzamy czy wczoraj też było zaliczone
        $yesterday = Carbon::yesterday();
        $completedYesterday = $habit->completions()->whereDate('completed_at', $yesterday)->exists();

        if ($completedYesterday || $habit->current_streak == 0) {
            $habit->increment('current_streak');
            
            // Aktualizacja rekordu życiowego
            if ($habit->current_streak > $habit->longest_streak) {
                $habit->update(['longest_streak' => $habit->current_streak]);
            }
        } else {
            // Jeśli była przerwa, zaczynamy od 1
            $habit->update(['current_streak' => 1]);
        }
    }

    return redirect()->back()->with('success', 'Postęp zapisany!');
    }

    public function update(Request $request, Habit $habit)
    {
        // Sprawdzamy, czy nawyk należy do zalogowanego użytkownika (Kontrola dostępu)
        $this->authorizeOwner($habit);

        $request->validate(['title' => 'required|string|max:255']);
        $habit->update(['title' => $request->title]);

        return redirect()->back()->with('success', 'Nawyk zaktualizowany!');
    }

    public function destroy(Habit $habit)
    {
        $this->authorizeOwner($habit);
        $habit->delete();

        return redirect()->back()->with('success', 'Nawyk usunięty!');
    }

    // Prywatna metoda pomocnicza dla bezpieczeństwa
    private function authorizeOwner(Habit $habit)
    {
        if ($habit->user_id !== auth()->id()) {
            abort(403);
        }
    }
}