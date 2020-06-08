<?php

namespace  SmartRaya\IPPanelLaravel;

use Illuminate\Support\ServiceProvider;

class IPPanelServiceProvider extends ServiceProvider
{
	
	public function boot()
{
    $this->publishes([
        __DIR__.'/Config/ippanel.php' => config_path('ippanel.php'),
    ]);
}
    public function register()
    {
        $this->app->singleton('IPPanel', function ($app) {
            $conf = $app['config']['ippanel'];
			
            return new Client($conf['api_key']);
        });
    }
}
