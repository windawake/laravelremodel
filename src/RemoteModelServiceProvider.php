<?php

namespace Laravel\Remote2Model;

use Illuminate\Support\ServiceProvider;

class RemoteModelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('db', function ($app) {
            return new RemoteDatabaseManager($app, $app['db.factory']);
        });

        $remoteConfig = [
            'driver' => 'remote',
            'name' => 'remote'
        ];

        $this->app['config']->set('database.connections.remote', $remoteConfig);

        $this->app->extend('db', function (RemoteDatabaseManager $db, $app) {
            $db->extend('remote', function($config, $name) {
                return new RemoteConnection(null, $name, '', $config);
            });
            return $db;
        });
    }
}
