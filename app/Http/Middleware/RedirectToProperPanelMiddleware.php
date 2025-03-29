<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;

final class RedirectToProperPanelMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        //        if (auth()->check() && auth()->user()->is_admin) {
        //            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        //        }

        return $next($request);
    }
}
