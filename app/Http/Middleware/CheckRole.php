<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user || ! $user->is_active) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Tidak Aktif atau Sesi Berakhir, silahkan Login',
            ]);
        }

        if (! $user->hasAnyRole($roles)) {
            abort(403, 'Anda Tidak Memiliki Akses ke Halaman Ini');
        }

        return $next($request);
    }
}
