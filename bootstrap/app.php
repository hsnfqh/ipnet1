<?php
// bootstrap/app.php

require_once __DIR__ . '/../app/Helpers/FinfoPolyfill.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'verify/inspect',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran data atau file yang dikirim terlalu besar. Sistem telah mengaktifkan kompresi foto otomatis.',
                ], 413);
            }
            return redirect()->back()->with('error', 'Ukuran data yang dikirim terlalu besar untuk server. Foto dokumentasi telah diatur agar otomatis dikompres.');
        });
    })->create();