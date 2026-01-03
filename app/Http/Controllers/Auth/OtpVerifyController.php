<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerifyController extends Controller
{
    public function __construct(private readonly OtpService $otpService) {}

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc,dns'],
            'code' => ['required', 'digits:6'],
        ]);

        $email = strtolower($data['email']);
        $code = $data['code'];

        $challengeId = $request->session()->get('otp_challenge_id');

        if (!$challengeId) {
            return back()
                ->withErrors(['code' => 'Sesja logowania wygasła. Wyślij kod jeszcze raz.'])
                ->withInput();
        }

        $ok = $this->otpService->verify($challengeId, $email, $code);

        if (!$ok) {
            return back()
                ->withErrors(['code' => 'Kod jest nieprawidłowy lub wygasł.'])
                ->withInput();
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Nie znaleziono użytkownika.'])
                ->withInput();
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();
        $request->session()->forget(['otp_email', 'otp_challenge_id']);

        return redirect()->intended('/dashboard');
    }
}
