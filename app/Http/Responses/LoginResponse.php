<?php

namespace App\Http\Responses;


use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;

class LoginResponse extends BaseLoginResponse
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
