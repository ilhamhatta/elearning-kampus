<?php

namespace App\Providers;

use App\Models\Material;
use App\Policies\MaterialPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Material::class => MaterialPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
