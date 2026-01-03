<?php

namespace App\Services;

use App\Models\LoginOtp;
use Illuminate\Support\Str;

class OtpService
{
    public int $ttlMinutes = 10;
    public int $maxAttempts = 5;

    public function generateCode(): string
    {

        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function hashCode(string $code): string
    {
        return hash_hmac('sha256', $code, config('app.key'));
    }

    public function issue(string $email, ?string $ip = null, ?string $userAgent = null): array
    {
        // unieważnij poprzednie aktywne kody
        LoginOtp::where('email', $email)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $code = $this->generateCode();
        $challengeId = (string) Str::uuid();

        $otp = LoginOtp::create([
            'challenge_id' => $challengeId,
            'email' => $email,
            'code_hash' => $this->hashCode($code),
            'expires_at' => now()->addMinutes($this->ttlMinutes),
            'attempts' => 0,
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);

    return [$otp, $code];
    }

    public function verify(string $challengeId, string $email, string $code): bool
    {
        $otp = LoginOtp::where('challenge_id', $challengeId)
            ->where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return false;
        }

        if ($otp->attempts >= $this->maxAttempts) {

            $otp->forceFill(['used_at' => now()])->save();
            return false;
        }

        $ok = hash_equals($otp->code_hash, $this->hashCode($code));

        if (!$ok) {
            $otp->increment('attempts');
            return false;
        }

        $otp->forceFill(['used_at' => now()])->save();
        return true;
    }
}
