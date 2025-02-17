<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use App\Models\Tahun;
use Filament\Forms\Components\Select;
use Illuminate\Contracts\View\View;

class Login extends BaseLogin
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.login';

    public function getHeading(): string
    {
        return 'SISTEM INFORMASI PENGUMPULAN DATA KINERJA';
    }

    public function getSubheading(): string 
    {
        return 'Silakan login untuk melanjutkan';
    }

    protected function getBackground(): string
    {
        return "bg-[url('/gambar/bg-login2.jpg')] bg-cover bg-center bg-no-repeat bg-black/50 bg-blend-overlay";
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';
        
        return [
            $login_type => $data['login'],
            'password' => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $response = parent::authenticate();
            
            // Simpan tahun_id ke session menggunakan data dari form
            session(['selected_tahun' => $this->form->getRawState()['tahun_id']]);
            
            return $response;
        } catch (ValidationException) {
            throw ValidationException::withMessages([
                'data.login' => 'NIP/Email atau Password salah',
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
                Select::make('tahun_id')
                    ->label('Tahun')
                    ->options(function (): array {
                        return Tahun::where('status', '=', '1')
                            ->pluck('nama', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->validationMessages([
                        'required' => 'Silakan pilih tahun',
                    ])
                    ->live(),
                    
                TextInput::make('login')
                    ->label('NIP/Email')
                    ->required()
                    ->validationMessages([
                        'required' => 'NIP/Email harus diisi',
                    ])
                    ->autocomplete()
                    ->autofocus(),
                    
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
