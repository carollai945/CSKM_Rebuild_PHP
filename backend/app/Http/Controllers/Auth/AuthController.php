<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;

/**
 * A01 登入 / 登出 / 重設密碼
 *
 * 功能編號：A01
 * 對應文件：docs/sdd/a01-change-password-sdd.md
 */
class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ])->status(401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function resetPassword(Request $request): JsonResponse
    {
        Gate::authorize('management');

        $request->validate([
            'staff_id'                  => 'required|integer|exists:staff,id',
            'new_password'              => 'required|string|min:8|confirmed',
        ]);

        $staff = \App\Models\Staff::with('user')->findOrFail($request->staff_id);
        $staff->user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
