<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginValidate;
use App\Models\Enterprise;
use App\Models\User;
use App\Models\UserLastLogin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    // Función para mostrar la vista de login
    public function showLoginForm(): View {
        $enterprise = Enterprise::where('id', 1)->get();
        return view('auth.login', compact('enterprise'));
    }

    public function login(LoginValidate $request): JsonResponse {
        // Validación de tasa de intentos
        $this->ensureIsNotRateLimited($request);
        $validated = $request->validated();
        // Buscar usuario activo (no eliminado)
        $user = User::where('email', $validated['email'])->whereNull('deleted_at')->first();
        if (!$user) {
            RateLimiter::hit($this->throttleKey($request));
            return response()->json([
                'status'    => false,
                'message'   => 'Credenciales inválidas.' // Mensaje genérico por seguridad
            ], 401);
        }

        // Verificar si el usuario está activo/baneado (si tu app tiene este campo)
        if (isset($user->is_active) && !$user->is_active) {
            return response()->json([
                'status'    => false,
                'message'   => 'Tu cuenta está desactivada.'
            ], 403);
        }

        // Intentar autenticación
        if (!Auth::attempt($validated)) {
            RateLimiter::hit($this->throttleKey($request));
            return response()->json([
                'status'    => false,
                'message'   => 'Credenciales inválidas.'
            ], 401);
        }

        // Autenticación exitosa
        $request->session()->regenerate();
        RateLimiter::clear($this->throttleKey($request));

        // Registrar último login
        UserLastLogin::updateOrCreate(
            ['id_user'      => Auth::id()],
            ['last_login'   => now(), 'ip_address' => $request->ip()]
        );

        // Obtener permisos
        $permissions = Auth::user()->getAllPermissions()->pluck('name');

        // Generar token CSRF para protección en formularios
        $csrfToken = csrf_token();

        return response()->json([
            'status'        => true,
            'message'       => 'Inicio de sesión exitoso.',
            'redirect'      => route('home'),
            'permissions'   => $permissions,
            'csrf_token'    => $csrfToken
        ]);
    }

    protected function ensureIsNotRateLimited(Request $request): void {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
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
    protected function throttleKey(Request $request): string {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'status'    => true,
            'redirect'  => route('login')
        ], 200);
    }
}
