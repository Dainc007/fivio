<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Infolists\Components\Entry;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\Filter;
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
//        DB::prohibitDestructiveCommands($isProduction);
        URL::forceScheme('https');

        if (! $isProduction) {
            Lang::handleMissingKeysUsing(function ($key, $replace, $locale, $fallback): void {
                Log::error("Missing translation key: {$key} in locale: {$locale}");
            });
        }

        date_default_timezone_set(config('app.timezone'));

        Vite::prefetch(concurrency: 3)->useAggressivePrefetching();

        Gate::define('viewPulse', function (User $user): bool {
            return $user->isAdmin();
        });

        $this->setDefaultFilamentSettings();

        LanguageSwitch::configureUsing(function (LanguageSwitch $languageSwitch) {
            $languageSwitch
                ->locales(['en', 'pl'])
            ->circular()
            ;
        });
    }

    protected function setDefaultFilamentSettings(): void
    {
        Column::configureUsing(function (Column $column): void {
            $column
                ->alignCenter()
                ->sortable()
                ->toggleable()
                ->translateLabel();
        });
        Filter::configureUsing(function (Filter $filter): void {
            $filter->translateLabel();
        });
        Field::configureUsing(function (Field $field): void {
            $field->translateLabel();
        });
        Entry::configureUsing(function (Entry $entry): void {
            $entry->translateLabel();
        });
        Action::configureUsing(function (Action $action): void {
            $action->translateLabel();
        });
    }
}
