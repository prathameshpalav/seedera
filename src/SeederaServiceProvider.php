<?php

namespace Pradility\Seedera;

use Illuminate\Support\ServiceProvider;

class SeederaServiceProvider extends ServiceProvider
{
    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'Pradility\Seedera\SeederGeneratorCommand',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}