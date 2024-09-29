<?php

namespace App\Providers;

use App\Repositories\AttributeRepositoryInterface;
use App\Repositories\AttributeValueRepositoryInterface;
use App\Repositories\BrandRepositoryInterface;
use App\Repositories\Eloquent\EloquentAttributeRepository;
use App\Repositories\Eloquent\EloquentAttributeValueRepository;
use App\Repositories\Eloquent\EloquentBrandRepository;
use App\Repositories\Eloquent\EloquentProductRepository;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
