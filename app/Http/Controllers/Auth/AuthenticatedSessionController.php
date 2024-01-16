<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\SigninLog;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'sitekey' =>config('services.recaptcha.sitekey'),
            'captcha_en' =>config('services.recaptcha.enabled', false)
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request) //: RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::user()->twofa) {
            Auth::user()->generateCode();
        }

        // log success
        SigninLog::create(
            [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'msg' => 'Login Success',
                'userAgent' => $request->userAgent(),
            ]
        );
        $mailonlogin = config('services.sysmailing.mailonlogin', false);
        if($mailonlogin) {
            SigninLog::signlogMail(
                [
                    'email' => $request->input('email'),
                    'ip' => $request->ip(),
                    'msg' => 'Login Success',
                    'userAgent' => $request->userAgent(),
                ]
            );
        }
        $captcha_en = config('services.recaptcha.enabled', false);
        if($captcha_en) {
            return  Inertia::location(RouteServiceProvider::HOME); // to refresh google captcha
        } else {
            return redirect()->intended(RouteServiceProvider::HOME);
        }




    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}
