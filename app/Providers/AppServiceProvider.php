<?php

namespace App\Providers;

use App\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\SubscriptionRepository;
use App\Models\News;
use App\Observers\NewsObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar el binding del repositorio
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the NewsObserver
        News::observe(NewsObserver::class);
    }
}
