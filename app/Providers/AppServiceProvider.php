<?php

namespace App\Providers;

use App\Repositories\ProductAttributeValueRepository;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Webkul\Product\Repositories\ProductAttributeValueRepository as BaseProductAttributeValueRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $allowedIPs = array_map('trim', explode(',', config('app.debug_allowed_ips')));

        $allowedIPs = array_filter($allowedIPs);

        if (empty($allowedIPs)) {
            return;
        }

        if (in_array(Request::ip(), $allowedIPs)) {
            Debugbar::enable();
        } else {
            Debugbar::disable();
        }

        // Override ProductAttributeValueRepository with custom implementation
        $this->app->singleton(
            BaseProductAttributeValueRepository::class,
            ProductAttributeValueRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            Artisan::call('db:seed');
        });

        // Override product type classes in config
        $this->overrideProductTypes();
    }

    /**
     * Override product type classes with custom implementations.
     */
    protected function overrideProductTypes(): void
    {
        $productTypes = config('product_types', []);

        foreach ($productTypes as $type => $config) {
            if (isset($config['class'])) {
                $originalClass = $config['class'];
                $className = class_basename($originalClass);
                
                // Check if we have a custom override for this type
                $customClass = "App\\Product\\Type\\{$className}";
                if (class_exists($customClass)) {
                    // Override in config at runtime
                    config(["product_types.{$type}.class" => $customClass]);
                }
            }
        }
    }
}
