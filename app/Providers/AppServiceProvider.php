<?php

namespace App\Providers;

use App\Services\SystemSettingsService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
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
        View::composer('*', function ($view): void {
            try {
                /** @var SystemSettingsService $settings */
                $settings = app(SystemSettingsService::class);
                $payload = $settings->getUiPayload();

                $view->with('appSettings', $payload);
            } catch (Throwable) {
                // During install/migrate phases settings table may be unavailable.
                $view->with('appSettings', [
                    'system_name' => config('app.name', 'ERP Platform'),
                    'company_name' => '',
                    'company_email' => '',
                    'company_phone' => '',
                    'default_currency' => 'USD',
                    'timezone' => config('app.timezone'),
                    'date_format' => 'Y-m-d',
                    'system_logo' => '',
                    'system_favicon' => '',
                    'system_logo_url' => null,
                    'system_favicon_url' => null,
                    'logo_url' => null,
                    'favicon_url' => null,
                ]);
            }
        });
    }
}
