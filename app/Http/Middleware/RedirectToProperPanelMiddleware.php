<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Pages\Dashboard;
class RedirectToProperPanelMiddleware
{
    public function handle(Request $request, Closure $next)
    {
//        if (auth()->user()->is_admin) {
//            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
//        }
        return $next($request);
    }
}
