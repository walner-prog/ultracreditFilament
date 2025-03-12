<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

use App\Filament\Widgets\Abonos;
use App\Filament\Widgets\StatsOverview;



class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel

        ->colors([
            'danger' => Color::Rose,
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'primary' => Color::Indigo,
            'success' => Color::Emerald,
            'warning' => Color::Orange,
        ])

      //  ->databaseNotifications()
      
        ->sidebarCollapsibleOnDesktop()
      //  ->topNavigation() 
      
        
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            
          //  ->profile()
            ->profile(isSimple: false)
            ->passwordReset()
            ->brandName('Empresa')
           // ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('6rem')
            ->favicon(asset('images/logo.png'))
           // ->font('Poppins')
           // ->darkMode(false)
          // ->topNavigation()
          ->sidebarWidth('15rem')
          
            ->colors([
                'primary' => Color::Indigo, // podemos usar Indigo etc 
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
               // Widgets\AccountWidget::class,
               StatsOverview::class,  
             
            
              
            ])

            ->plugins([
                EasyFooterPlugin::make()
                ->footerEnabled() // false desabilita 
              //  ->withLogo('https://static.cdnlogo.com/logos/l/23/laravel.svg')
               // ->withFooterPosition('sidebar.footer')
                ->withFooterPosition('footer')
                ->withSentence('')
                ->withLoadTime() // muetra tiempo de carga de la pagina
                //->withBorder(),  // bordes 
                ->hiddenFromPagesEnabled(),
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
            ]);

            
    }
}
