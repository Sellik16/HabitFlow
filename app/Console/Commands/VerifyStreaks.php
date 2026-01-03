<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Habit;
use Carbon\Carbon;

class VerifyStreaks extends Command
{
    // Nazwa komendy - zostawiamy bez zmian
    protected $signature = 'habits:verify-streaks';
    protected $description = 'Zeruje serie dla nawyków, które nie zostały wykonane wczoraj';

    public function handle()
    {
        $this->info('Rozpoczynam weryfikację dziennych serii...');

        // Optymalizacja: Pobieramy tylko nawyki z aktywnym streakiem
        // Dodatkowo używamy eager loadingu (with), aby uniknąć problemu N+1
        $habits = Habit::where('current_streak', '>', 0)
            ->with(['completions' => function($query) {
                $query->latest('completed_at');
            }])
            ->get();

        $count = 0;

        foreach ($habits as $habit) {
            $lastCompletion = $habit->completions->first();

            // LOGIKA: Jeśli dzisiaj sprawdzamy serie (np. o 00:01), 
            // to interesuje nas, czy nawyk był zaliczony wczoraj.
            // subDay() = wczoraj. startOfDay() zapewnia, że sprawdzamy całą dobę.
            
            $wasDoneYesterday = $lastCompletion && $lastCompletion->completed_at->isYesterday();
            $wasDoneToday = $lastCompletion && $lastCompletion->completed_at->isToday();

            // Jeśli nie był zrobiony wczoraj I nie został już zrobiony dzisiaj (bezpiecznik)
            if (!$wasDoneYesterday && !$wasDoneToday) {
                $habit->update(['current_streak' => 0]);
                $count++;
            }
        }

        $this->info("Zakończono. Zresetowano serie dla {$count} nawyków.");
    }
}