<?php

namespace App\Providers;

use App\Repositories\AttributeValueRepositoryInterface;
use App\Repositories\Eloquent\EloquentAttributeValueRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\BrandRepositoryInterface;
use App\Repositories\Eloquent\EloquentBrandRepository;
use App\Repositories\AttributeRepositoryInterface;
use App\Repositories\Eloquent\EloquentAttributeRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\Eloquent\EloquentProductRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(BrandRepositoryInterface::class, EloquentBrandRepository::class);
        $this->app->bind(AttributeRepositoryInterface::class, EloquentAttributeRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(AttributeValueRepositoryInterface::class, EloquentAttributeValueRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
