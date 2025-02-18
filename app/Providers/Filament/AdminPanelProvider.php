<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Resources\DuKakResource\Widgets\kakStat;
use App\Filament\Resources\DuRenaksiResource\Widgets\RenaksiStat;
use App\Filament\Resources\DuRenaksiResource\Widgets\RenaksiView;
use App\Models\dm_bidang;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admins')
            ->login(Login::class)
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            // ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                RenaksiStat::class,
                // kakStat::class,
                // RenaksiView::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                // 'admin',
            ])
            // ->tenant(
            //     dm_bidang::class,
            // )
            // ->navigationItems([
            //     NavigationItem::make('Admin', 'admin.dashboard')
            //     ->visible(fn(): bool => ! auth()->user('admin')->can('dashboard.access')),
            // ])
            ->plugins([
                // SimpleLightBoxPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                ])
            
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups()
            ->sidebarWidth('18rem')
            ->spa()
            ->profile(isSimple: false)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandName('SI-PENDAKI')
            ->brandLogo(asset('gambar/sipendaki-white.png'))
            ->darkModeBrandLogo(asset('gambar/sipendaki-black.png'))
            ->brandLogoHeight('4rem')
            ->favicon(asset('gambar/paser.png'))
            ->renderHook(
                'panels::user-menu.before',
                fn () => view('filament.hooks.show-selected-year')
            )
            ->renderHook(
                'panels::footer',
                fn () => request()->routeIs('filament.admin.auth.login') ? '' : view('filament.footer')
            )
            ;
    }
}
