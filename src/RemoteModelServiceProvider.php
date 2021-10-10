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

        $remoteMergeConfig = [
            'driver' => 'remote',
            'name' => 'remote'
        ];
        $remoteConfig = array_merge($this->app['config']->get('database.connections.remote'), $remoteMergeConfig);

        $this->app['config']->set('database.connections.remote', $remoteConfig);

        $this->app->extend('db', function (RemoteDatabaseManager $db, $app) {
            $db->extend('remote', function($config, $name) {
                return new RemoteConnection(null, $name, '', $config);
            });
            return $db;
        });

        $this->app->singleton('remote.tool', function ($app) {
            return new RemoteTool();
        });
    }
}
