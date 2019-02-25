<?php

namespace XiangDangDang\AllInPayTlt\Providers;

use Illuminate\Support\ServiceProvider;
use XiangDangDang\AllInPayTlt\TltPay;

class AllInPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/allinpaytlt.php', 'allinpaytlt.php'
        );

        $this->app->bind('XiangDangDang\AllInPay\TltPay', function () {
            return new TltPay();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/allinpaytlt.php' => config_path('allinpaytlt.php')
        ]);
    }
}
