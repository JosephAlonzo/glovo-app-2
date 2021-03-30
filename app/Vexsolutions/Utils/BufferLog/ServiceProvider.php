<?php

namespace Vexsolutions\Utils\Logger;

use Escom\Application\EMApp;
use Illuminate\Support\ServiceProvider;

class BufferLoggerServiceProvider extends ServiceProvider {
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        $this->app->bind('BufferLogger', function ($app) {
            return new BufferLogger();
        });


    }


}
