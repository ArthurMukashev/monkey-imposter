<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $renderApiError = static function (string $code, string $message, int $status) {
            return response()->json([
                'error' => [
                    'code' => $code,
                    'message' => $message,
                    'details' => null,
                ],
            ], $status);
        };

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) use ($renderApiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $renderApiError('not_found', 'Ресурс не найден.', 404);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) use ($renderApiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $renderApiError('not_found', 'Ресурс не найден.', 404);
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) use ($renderApiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                    'details' => $e->errors(),
                ],
            ], 422);
        });

        $exceptions->render(function (\Throwable $e, $request) use ($renderApiError) {
            if (! $request->is('api/*') || $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                return null;
            }

            // Исключение уже залогировано фреймворком (Kernel::reportException) — не дублируем.

            return $renderApiError('internal_error', 'Внутренняя ошибка сервера.', 500);
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('sync:remote-data')
            ->cron((string) config('app.remote_sync_cron'))
            ->withoutOverlapping();
    })
    ->create();
