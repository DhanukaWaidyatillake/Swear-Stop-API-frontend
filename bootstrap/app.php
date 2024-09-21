<?php

use App\Services\ToastMessageService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->throttleWithRedis();
        $middleware->append(\App\Http\Middleware\AuditPaddleWebhooks::class);
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, Throwable $exception, \Illuminate\Http\Request $request) {

            if (in_array($response->getStatusCode(), [500, 503, 404, 403, 429])) {

                $errorMessages = [
                    500 => 'Server Error: Please try again later.',
                    503 => 'Service Unavailable: The server is temporarily down.',
                    404 => 'Not Found: The requested resource could not be found.',
                    403 => 'Forbidden: You do not have permission to access this.',
                    429 => 'Too Many Requests',
                ];

                $toastMessageService = new ToastMessageService();
                $shortMessage = $errorMessages[$response->getStatusCode()];
                $toastMessageService->showToastMessage('error', $shortMessage);

                return back();
            }

            return $response;
        });
    })->create();
