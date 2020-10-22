<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'cadeco' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_CADECO', 'localhost'),
            //'port' => env('DB_PORT_CADECO', '1433'),
            'database' => env('DB_DATABASE_CADECO', 'forge'),
            'username' => env('DB_USERNAME_CADECO', 'forge'),
            'password' => env('DB_PASSWORD_CADECO', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'cntpq' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_CNTPQ', 'localhost'),
            'database' => env('DB_DATABASE_CNTPQ', 'forge'),
            'username' => env('DB_USERNAME_CNTPQ', 'forge'),
            'password' => env('DB_PASSWORD_CNTPQ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'cntpqg' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_GCNTPQ', 'localhost'),
            'database' => env('DB_DATABASE_GCNTPQ', 'forge'),
            'username' => env('DB_USERNAME_GCNTPQ', 'forge'),
            'password' => env('DB_PASSWORD_GCNTPQ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'igh' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_IGH', '127.0.0.1'),
            'port' => env('DB_PORT_IGH', '3306'),
            'database' => env('DB_DATABASE_IGH', 'forge'),
            'username' => env('DB_USERNAME_IGH', 'forge'),
            'password' => env('DB_PASSWORD_IGH', ''),
            'unix_socket' => env('DB_SOCKET_IGH', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

        'sci' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_SCI', '127.0.0.1'),
            'port' => env('DB_PORT_SCI', '3306'),
            'database' => env('DB_DATABASE_SCI', 'forge'),
            'username' => env('DB_USERNAME_SCI', 'forge'),
            'password' => env('DB_PASSWORD_SCI', ''),
            'unix_socket' => env('DB_SOCKET_SCI', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

        'modulosao' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_MODULOSAO', 'localhost'),
            //'port' => env('DB_PORT_MODULOSAO', '1433'),
            'database' => env('DB_DATABASE_MODULOSAO', 'forge'),
            'username' => env('DB_USERNAME_MODULOSAO', 'forge'),
            'password' => env('DB_PASSWORD_MODULOSAO', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'seguridad' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_SEGURIDAD', 'localhost'),
            //'port' => env('DB_PORT_SEGURIDAD', '1433'),
            'database' => env('DB_DATABASE_SEGURIDAD', 'forge'),
            'username' => env('DB_USERNAME_SEGURIDAD', 'forge'),
            'password' => env('DB_PASSWORD_SEGURIDAD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'acarreos' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_ACARREOS', '127.0.0.1'),
            'port' => env('DB_PORT_ACARREOS', '3306'),
            'database' => env('DB_DATABASE_ACARREOS', 'forge'),
            'username' => env('DB_USERNAME_ACARREOS', 'forge'),
            'password' => env('DB_PASSWORD_ACARREOS', ''),
            'unix_socket' => env('DB_SOCKET_ACARREOS', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

        'scaconf' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_SCACONF', '127.0.0.1'),
            'port' => env('DB_PORT_SCACONF', '3306'),
            'database' => env('DB_DATABASE_SCACONF', 'forge'),
            'username' => env('DB_USERNAME_SCACONF', 'forge'),
            'password' => env('DB_PASSWORD_SCACONF', ''),
            'unix_socket' => env('DB_SOCKET_SCACONF', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration RepositoryInterface Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

    ],

];
