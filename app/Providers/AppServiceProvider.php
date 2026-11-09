<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        // Режим розробки: ледаче завантаження звʼязку кидає
        // LazyLoadingViolationException, що виявляє проблему N+1.
        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
