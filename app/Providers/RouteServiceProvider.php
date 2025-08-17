<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('uploads', fn($r) => [Limit::perMinute(10)->by(optional($r->user())->id ?: $r->ip())]);

        RateLimiter::for('downloads', fn($r) => [Limit::perMinute(120)->by(optional($r->user())->id ?: $r->ip())]);
    }
}
