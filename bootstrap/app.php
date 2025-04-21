<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

<<<<<<< HEAD
=======
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

>>>>>>> bd447f1 (database pertama)
        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Http\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Http\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Http\Middleware\RoleOrPermissionMiddleware::class,
            'hasrole' => \Spatie\Permission\Http\Middleware\HasRole::class,
            'hasanyrole' => \Spatie\Permission\Http\Middleware\HasAnyRole::class,
            'hasallroles' => \Spatie\Permission\Http\Middleware\HasAllRoles::class,
            'haspermission' => \Spatie\Permission\Http\Middleware\HasPermission::class,
            'hasanypermission' => \Spatie\Permission\Http\Middleware\HasAnyPermission::class,
            'hasallpermissions' => \Spatie\Permission\Http\Middleware\HasAllPermissions::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
