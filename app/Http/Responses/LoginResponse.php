<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

final class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (auth()->user()->is_admin) {
            return redirect()->route('filament.admin.resources.orders.index');
        }

        if (auth()->user()) {
            return redirect()->route('filament.auth.resources.orders.index');
        }

        return parent::toResponse($request);
    }
}
