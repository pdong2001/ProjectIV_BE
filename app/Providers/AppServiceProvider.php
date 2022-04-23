<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\CustomerService;
use App\Services\InvoiceDetailService;
use App\Services\InvoiceService;
use App\Services\ProductDetailService;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProductService::class, function($app) {
            return new ProductService();
        });
        $this->app->bind(ProductDetailService::class, function($app) {
            return new ProductDetailService($app->make(ProductService::class));
        });
        $this->app->bind(InvoiceService::class, function($app) {
            return new InvoiceService($app->make(CustomerService::class));
        });
        $this->app->bind(InvoiceDetailService::class, function($app) {
            return new InvoiceDetailService($app->make(InvoiceService::class));
        });
        $this->app->bind(CustomerService::class, function($app) {
            return new CustomerService();
        });
        $this->app->bind(CartService::class, function($app) {
            return new CartService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
