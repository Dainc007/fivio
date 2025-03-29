<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        LoginResponse::class => \App\Http\Responses\LoginResponse::class,
        LogoutResponse::class => \App\Http\Responses\LogoutResponse::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isProduction = $this->app->isProduction();

        Model::shouldBeStrict($isProduction);
        Model::unguard();
        DB::prohibitDestructiveCommands($isProduction);
        URL::forceScheme('https');

        Lang::handleMissingKeysUsing(function ($key, $replace, $locale, $fallback): void {
            Log::error("Missing translation key: {$key} in locale: {$locale}");
        });

        date_default_timezone_set(config('app.timezone'));

        Vite::prefetch(concurrency: 3)->useAggressivePrefetching();

        Gate::define('viewPulse', function (User $user): bool {
            return $user->isAdmin();
        });
    }
}
