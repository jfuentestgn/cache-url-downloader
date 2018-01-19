<?php
/**
 * Created by PhpStorm.
 * User: jfuentes
 * Date: 08/01/2018
 * Time: 20:03
 */

namespace JFuentesTgn\CacheUrlDownloader;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBaseDownloader();
        $this->registerCacheDownloader();
    }

    private function registerBaseDownloader()
    {
        $this->app->singleton('jfuentestgn.downloader', function($app) {
           return new GuzzleUrlDownloader();
        });
    }

    private function registerCacheDownloader()
    {
        $this->app->singleton('jfuentestgn.cache.downloader', function ($app) {
            $cache = $app->make('cache');
            $cacheStore = $cache->store();
            $baseDownloader = $app['jfuentestgn.downloader'];
            return new CacheUrlDownloader($baseDownloader, $cacheStore);
        });

        $this->app->alias('jfuentestgn.cache.downloader', \JFuentesTgn\CacheUrlDownloader\UrlDownloader::class);
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['jfuentestgn.cache.downloader'];
    }
}