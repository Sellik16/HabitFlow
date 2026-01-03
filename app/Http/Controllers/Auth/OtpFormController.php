<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use Illuminate\Http\Request;

class OtpFormController extends Controller
{
    public function __invoke(Request $request)
    {
        $email = $request->session()->get('otp_email');
        $challengeId = $request->session()->get('otp_challenge_id');

        $expiresAtIso = null;

        if ($email && $challengeId) {
            $otp = LoginOtp::where('challenge_id', $challengeId)
                ->where('email', $email)
                ->whereNull('used_at')
                ->first();

            if ($otp && $otp->expires_at) {

                $expiresAtIso = $otp->expires_at->toIso8601String();
            }
        }

        return view('auth.otp', [
            'expiresAtIso' => $expiresAtIso, // może być null
        ]);
    }
}
