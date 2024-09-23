<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = User::create($data);
        $user->assignRole(RolesEnum::USER);

        defer(fn () => event(new Registered($user)));

        return Response::success(data: [
            'user' => UserResource::make($user),
            'token' => $user->createToken('api token')->plainTextToken,
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->validated())) {
            return Response::error('Invalid credentials', 401);
        }

        return Response::success(data: [
            'user' => UserResource::make(Auth::user()),
            'token' => Auth::user()->createToken('api token')->plainTextToken,
        ]);
    }

    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return Response::success('email verification link has been sent to your email.');
    }

    public function markAsVerified(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return Response::success('email verified successfully.');
    }
}
