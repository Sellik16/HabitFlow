<?php

namespace App\Console\Commands;

use App\Models\LoginOtp;
use Illuminate\Console\Command;

class CleanupLoginOtps extends Command
{
    protected $signature = 'auth:cleanup-login-otps {--days=7}';
    protected $description = 'Usuwa stare rekordy OTP';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = LoginOtp::where(function ($q) use ($cutoff) {
                $q->whereNotNull('used_at')
                  ->orWhere('expires_at', '<', now());
            })
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$deleted} OTP records.");
        return self::SUCCESS;
    }
}
