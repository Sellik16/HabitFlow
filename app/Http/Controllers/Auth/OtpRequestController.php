<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailOtpCode;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class OtpRequestController extends Controller
{
    public function __construct(private readonly OtpService $otpService) {}

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc,dns'],
        ]);

        $email = strtolower($data['email']);

        // Throttle
        $key = 'otp-request:' . sha1($email . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Zbyt wiele prób. Spróbuj później.']);
        }
        RateLimiter::hit($key, 60);

        // Cooldown pomiedzy requestami
        $emailCooldownKey = 'otp-email-cooldown:' . sha1($email);
        if (RateLimiter::tooManyAttempts($emailCooldownKey, 1)) {
            return back()->withErrors([
                'email' => 'Kod został wysłany niedawno. Odczekaj 60 sekund i spróbuj ponownie.',
            ]);
        }
        RateLimiter::hit($emailCooldownKey, 60);

        $user = User::where('email', $email)->first();
        if ($user) {
            [$otp, $code] = $this->otpService->issue($email, $request->ip(), (string) $request->userAgent());

            $request->session()->forget('otp_challenge_id');

            $request->session()->put('otp_challenge_id', $otp->challenge_id);

            Mail::to($email)->send(new EmailOtpCode($code, $this->otpService->ttlMinutes));
        }

        usleep(random_int(200_000, 400_000));

        $request->session()->put('otp_email', $email);

        return redirect()->route('auth.otp.form')
            ->with('status', 'Jeśli adres istnieje w systemie, wysłaliśmy kod do logowania.');
    }
}
