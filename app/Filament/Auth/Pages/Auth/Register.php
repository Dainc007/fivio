<?php

declare(strict_types=1);

namespace App\Filament\Auth\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;

final class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getPhoneNumberFormComponent(),
                        $this->getCompanyNameFormComponent(),
                        $this->getTaxNumberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    private function getTaxNumberFormComponent(): Component
    {
        return TextInput::make('tax_number');
    }

    private function getCompanyNameFormComponent(): TextInput
    {
        return TextInput::make('company_name');
    }

    private function getPhoneNumberFormComponent(): TextInput
    {
        return TextInput::make('phone_number')->tel()->prefix('+48');
    }
}
