<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyReportMail;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateWeeklyReport extends Command
{
    // To musi się zgadzać z tym, co wpisujesz w terminalu
    protected $signature = 'habits:generate-report';

    protected $description = 'Generuje raporty PDF i wysyła je mailem do użytkowników';

    public function handle()
    {
        $this->info('Rozpoczynam generowanie raportów...');

        // Pobieramy użytkowników z nawykami i ich zaliczeniami (Eager Loading)
        $users = User::with(['habits.completions' => function($query) {
            $query->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }])->get();

        if ($users->isEmpty()) {
            $this->warn('Nie znaleziono użytkowników do wysyłki.');
            return;
        }

        foreach ($users as $user) {
            // 1. Przygotowanie danych do widoku Blade
            $habitsData = $user->habits->map(function ($habit) {
                return [
                    'name' => $habit->title,
                    'streak' => $habit->current_streak,
                    'completions_count' => $habit->completions->count()
                ];
            });

            // 2. Generowanie PDF
            $pdf = Pdf::loadView('emails.weekly-report-pdf', [ // To jest szablon PDF-a
                'user' => $user,
                'habitsData' => $habitsData
            ]);
            
            $pdfContent = $pdf->output();

            // 3. Wysyłka maila
            try {
                Mail::to($user->email)->send(new WeeklyReportMail($user, $pdfContent));
                $this->info("Wysłano raport do: {$user->email}");
            } catch (\Exception $e) {
                $this->error("Błąd wysyłki do {$user->email}: " . $e->getMessage());
            }
        }

        $this->info('Proces zakończony.');
    }
}