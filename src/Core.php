<?php

namespace System;

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager;
use System\Singletons\PageLoader;

class Core
{

    public static function init (): void
    {
        self::setupDotEnv();
        self::setupEloquent();
        PageLoader::getInstance()->init();
    }

    /**
     * Loads in environmental variables
     */
    private static function setupDotEnv (): void
    {
        $dotenv = Dotenv::createImmutable( __DIR__ );
        $dotenv->load();
        $dotenv->required( [ 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS' ] );
    }

    /**
     * Setup for Illuminate Eloquent Models
     */
    private static function setupEloquent (): void
    {
        $capsule = new Manager();

        $capsule->addConnection( [
            'driver' => 'mysql',
            'host' => $_ENV[ 'DB_HOST' ],
            'database' => $_ENV[ 'DB_NAME' ],
            'username' => $_ENV[ 'DB_USER' ],
            'password' => $_ENV[ 'DB_PASS' ]
        ] );

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}