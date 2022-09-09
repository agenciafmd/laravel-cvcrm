<?php

namespace Agenciafmd\Cvcrm\Providers;

use Illuminate\Support\ServiceProvider;

class CvcrmServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 
    }

    public function register()
    {
        $this->loadConfigs();
    }

    protected function loadConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-cvcrm.php', 'laravel-cvcrm');
    }
}
