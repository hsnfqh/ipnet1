<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('Helpers/CurrencyHelper.php'))) {
            require_once app_path('Helpers/CurrencyHelper.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tailwind');

        Blade::directive('currencyCompact', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::formatCompact($expression); ?>";
        });

        Blade::directive('rupiah', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::formatRupiah($expression); ?>";
        });
    }
}
