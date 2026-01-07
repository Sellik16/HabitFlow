<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Habit;
use Carbon\Carbon;

class VerifyStreaks extends Command
{
    // Nazwa komendy, którą będziemy wywoływać
    protected $signature = 'habits:verify-streaks';
    protected $description = 'Zeruje streaki dla nawyków niezaliczonych od 2 dni';

    public function handle()
    {
        $this->info('Rozpoczynam sprawdzanie aktywności...');

        // Pobieramy nawyki, które mają streak większy niż 0
        $habits = Habit::where('current_streak', '>', 0)->get();
        $count = 0;

        foreach ($habits as $habit) {
            // Pobieramy datę ostatniego zaliczenia
            $lastCompletion = $habit->completions()->latest('completed_at')->first();

            // Jeśli nie ma zaliczeń LUB ostatnie było dawniej niż 2 dni temu (start dnia)
            if (!$lastCompletion || $lastCompletion->completed_at->lt(Carbon::now()->subDays(2)->startOfDay())) {
                $habit->update(['current_streak' => 0]);
                $count++;
            }
        }

        $this->info("Zakończono. Zresetowano streaki dla {$count} nawyków.");
    }
}