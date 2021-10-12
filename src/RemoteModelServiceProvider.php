<?php

namespace Laravel\Remote2Model;

use Illuminate\Support\ServiceProvider;
use Laravel\Remote2Model\Console\CreateExampleModels;

class RemoteModelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // $this->publishes([
        //     __DIR__.'/../examples/Models/' => app_path('Models'),
        // ]);
    }
    
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
        $config = $this->app['config']->get('database.connections.remote') ?? [];
        $remoteConfig = array_merge($config, $remoteMergeConfig);

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

        $this->app->singleton(
            'command.remote.example-models',
            function ($app) {
                return new CreateExampleModels($app['files']);
            }
        );

        $this->commands(
            'command.remote.example-models'
        );
    }
}
