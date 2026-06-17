<?php

namespace App\Providers;

use App\Services\NotificationService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.simple');

        View::composer(['admin.partials.sidebar', 'admin.partials.topbar'], function ($view) {
            if (! array_key_exists('unreadNotifications', $view->getData())) {
                $view->with('unreadNotifications', app(NotificationService::class)->unreadCount());
            }
        });
    }
}
