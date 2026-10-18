<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginValidate;
use App\Models\Enterprise;
use App\Models\User;
use App\Models\UserLastLogin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Display the login SPA view.
     */
    public function showLoginForm(): View
    {
        $enterprise = Enterprise::find(1) ?? Enterprise::first();

        if (! $enterprise) {
            $enterprise = new Enterprise([
                'id' => 1,
                'nombre_comercial' => config('global.site_name', 'Neumotar'),
                'logo' => '',
                'logo_receta' => '',
                'descripcion' => 'Especialista en enfermedades respiratorias',
            ]);
        }

        $loginState = [
            'appName' => $enterprise->nombre_comercial ?: config('global.site_name', 'Neumotar'),
            'logoUrl' => $enterprise->logo_receta ? asset('storage/'.$enterprise->logo_receta) : null,
            'faviconUrl' => $enterprise->logo ? asset('storage/'.$enterprise->logo) : null,
            'subtitle' => $enterprise->descripcion ?: 'Especialista en enfermedades respiratorias',
            'redirectUrl' => route('home'),
            'routes' => [
                'login' => url('/login'),
                'home' => route('home'),
                'logout' => route('logout'),
            ],
            'csrfToken' => csrf_token(),
        ];

        return view('auth.login', [
            'enterprise' => collect([$enterprise]),
            'enterpriseModel' => $enterprise,
            'loginState' => $loginState,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginValidate $request): JsonResponse
    {
        $this->ensureIsNotRateLimited($request);
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->whereNull('deleted_at')->first();
        if (! $user) {
            RateLimiter::hit($this->throttleKey($request));

            return response()->json([
                'status' => false,
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        if (isset($user->is_active) && ! $user->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Tu cuenta está desactivada.',
            ], 403);
        }

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey($request));

            return response()->json([
                'status' => false,
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        $request->session()->regenerate();
        RateLimiter::clear($this->throttleKey($request));

        UserLastLogin::updateOrCreate(
            ['id_user' => Auth::id()],
            ['last_login' => now(), 'ip_address' => $request->ip()]
        );

        $user = Auth::user();
        $permissions = $user->getAllPermissions()->pluck('name');
        $csrfToken = csrf_token();

        return response()->json([
            'status' => true,
            'message' => 'Inicio de sesión exitoso.',
            'redirect' => route('home'),
            'permissions' => $permissions,
            'csrf_token' => $csrfToken,
            'token' => $csrfToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
            ],
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'redirect' => route('login'),
        ], 200);
    }
}
