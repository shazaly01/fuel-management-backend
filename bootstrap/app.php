<?php

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
        // --- [بداية الإضافة] ---
        // هذا السطر يخبر Laravel بتعطيل حماية CSRF
        // لجميع المسارات التي تبدأ بـ /api/
        // لأن الـ API يستخدم مصادقة Sanctum (Bearer Token) بدلاً من الجلسات.
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
        // --- [نهاية الإضافة] ---
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
