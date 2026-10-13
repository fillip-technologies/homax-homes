<?php

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
            'admin.guest'=> \App\Http\Middleware\AdminRedirect::class,
            'admin.auth'=> \App\Http\Middleware\AdminAuthenticate::class,
            'check.permission' => \App\Http\Middleware\CheckUserPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // An upload above PHP's post_max_size arrives with an empty body; send the user back
        // with a clear message instead of a bare 413 page.
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }
            return back()->withErrors([
                'upload' => 'The files you selected are too large for the server in total. Please upload fewer or smaller images and try again.',
            ]);
        });
    })->create();
