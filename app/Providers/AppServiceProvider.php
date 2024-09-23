<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($message = 'Success', $data = []) {
            return new JsonResponse([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ]);
        });

        Response::macro('error', function ($message, $code = 400) {
            return new JsonResponse([
                'success' => false,
                'message' => $message,
            ], $code);
        });
    }
}
