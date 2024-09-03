<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class login extends BaseLogin
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.login';

    protected function getBackground(): string
    {
        return 'bg-red-600'; 
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL ) ? 'email' : 'nip';
 
        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException) {
            throw ValidationException::withMessages([
                'data.login' => 'NIP / NIK / Email atau Password Anda Salah !!!',
            ]);
        }
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    // public function mount(): void
    // {
    //     parent::mount();

    //     if (app()->environment('local')) {
    //         $this->form->fill([
    //             'email' => 'ZbqQK@example.com',
    //             'password' => 'password',

    //         ]);
    //     }
    // }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // $this->getEmailFormComponent(), 
                $this->getLoginFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }
 
    protected function getLoginFormComponent(): Component 
    {
        return TextInput::make('login')
            ->label('NIP / NIK / Email')
            ->validationMessages([
                'required' => 'NIP / NIK / Email harus diisi',
            ])
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    } 
    
}
