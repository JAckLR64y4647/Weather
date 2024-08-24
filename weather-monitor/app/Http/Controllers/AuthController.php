<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $user = User::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]));

        Auth::login($user, true);

        event(new Registered($user));

        return response()->json(
            ['message' => __('auth.successfully_registered')],
            201,
        );
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (Auth::attempt($data, $data['remember'] ?? false)) {
            session()->regenerate();

            return response()->json(['message' => __('auth.logged_in')]);
        }

        return response()->json(['message' => __('auth.invalid_credentials')], 401);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!password_verify($data['current_password'], $user->password)) {
            return response()->json(['message' => __('auth.invalid_password')], 401);
        }

        $user->update(['password' => bcrypt($data['new_password'])]);

        return response()->json(['message' => __('auth.password_updated')]);
    }

    public function sendResetPasswordEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        return response()->json(['message' => __('auth.reset_link_sent')]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),

            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        $isSuccess = $status === Password::PASSWORD_RESET;

        if ($isSuccess) {
            Auth::login(User::where('email', $request->get('email'))->first(), true);
        }

        return $isSuccess
            ? response()->json(['message' => __('auth.password_reset')])
            : response()->json(['message' => __('auth.reset_link_invalid')], 401);
    }

    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => __('auth.verification_link_sent')]);
    }

    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return response()->json(['message' => __('auth.email_verified')]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard("web")->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json(['message' => __('auth.logged_out')]);
    }
}
