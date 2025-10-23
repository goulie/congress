<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OtpMailService;
use App\Traits\GeneratesOtp;
use App\Traits\ValidOtp;

class OtpController extends Controller
{
    
   use ValidOtp;
    /* public function __construct(OtpMailService $otpMailService)
    {
        $this->otpMailService = $otpMailService;
    } */

    public function sendOtp(Request $request)
    {
        $user = $request->user();

        $request->validate(['email' => 'required|email']);

        $code = $user->generateOtp($user->email, 4, 15);

        app(OtpMailService::class)->sendOtp($user->email, app()->getLocale(), $code['code']);

        return redirect()->back()->with('success', 'success');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $result = $this->validateOtp($request->email, $request->otp);;

        if ($result['valid'] == true) {
            return redirect()->route('dashboard')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function hasValidOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $result = $this->validateOtp($request->email, $request->otp);;

        if ($result['valid'] == true) {
            return redirect()->route('dashboard')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function verifyOtpForm()
    {
        return view('otp.verify_form');
    }
}
