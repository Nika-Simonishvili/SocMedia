<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Settings\UpdateChannelsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    public function updateChannels(UpdateChannelsRequest $request): JsonResponse
    {
        Auth::user()->settings()->update($request->validated());

        return Response::success();
    }
}
