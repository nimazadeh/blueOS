<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Blue Control authentication (foundation).
 *
 * Phase 1A: email + password with built-in throttling, session regeneration
 * and explicit `admin` guard. RBAC and 2FA arrive with the Blue Control phase
 * (roles/permissions are not part of 1A schema).
 */
class LoginController extends Controller
{
    use ThrottlesLogins;

    public function showLoginForm(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        $credentials = [
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'is_active' => true,
        ];

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $this->incrementLoginAttempts($request);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    protected function validateLogin(Request $request): void
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:190'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'remember' => ['sometimes', 'boolean'],
        ]);
    }

    /**
     * Route-level throttle key (email-based rather than IP-only).
     */
    public function username(): string
    {
        return 'email';
    }
}
