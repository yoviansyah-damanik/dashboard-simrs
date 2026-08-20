<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Hak akses (permission/role) pada pengguna yang sedang login sudah tidak sinkron
        // dengan data terbaru (mis. permission dihapus/diganti nama). Paksa logout agar
        // sesi diperbarui daripada menampilkan halaman error mentah ke pengguna.
        $exceptions->render(function (PermissionDoesNotExist|RoleDoesNotExist $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi tidak valid, silakan login kembali.'], 401);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $request->session()->flash('livewire-alert', [
                'type' => 'warning',
                'message' => 'Sesi Anda telah berakhir karena terjadi perubahan hak akses. Silakan login kembali.',
                'events' => [],
                'options' => [],
                'data' => null,
            ]);

            return redirect()->route('login');
        });
    })->create();
