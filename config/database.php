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
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
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
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
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
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
        ],

        'cntpqdc' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_DCCNTPQ', 'localhost'),
            'database' => env('DB_DATABASE_DCCNTPQ', 'forge'),
            'username' => env('DB_USERNAME_DCCNTPQ', 'forge'),
            'password' => env('DB_PASSWORD_DCCNTPQ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
        ],

        'cntpqdm' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_DMCNTPQ', 'localhost'),
            'database' => env('DB_DATABASE_DMCNTPQ', 'forge'),
            'username' => env('DB_USERNAME_DMCNTPQ', 'forge'),
            'password' => env('DB_PASSWORD_DMCNTPQ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
        ],

        'cntpqod' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_ODCNTPQ', 'localhost'),
            'database' => env('DB_DATABASE_ODCNTPQ', 'forge'),
            'username' => env('DB_USERNAME_ODCNTPQ', 'forge'),
            'password' => env('DB_PASSWORD_ODCNTPQ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
        ],

        'cntpqom' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_OMCNTPQ', 'localhost'),
            'database' => env('DB_DATABASE_OMCNTPQ', 'forge'),
            'username' => env('DB_USERNAME_OMCNTPQ', 'forge'),
            'password' => env('DB_PASSWORD_OMCNTPQ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
        ],

        'cntpqoc' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_OCCNTPQ', 'localhost'),
            'database' => env('DB_DATABASE_OCCNTPQ', 'forge'),
            'username' => env('DB_USERNAME_OCCNTPQ', 'forge'),
            'password' => env('DB_PASSWORD_OCCNTPQ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
        ],

        'cntpq_inf' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_CNTPQ_INF', 'localhost'),
            'database' => env('DB_DATABASE_CNTPQ_INF', 'forge'),
            'username' => env('DB_USERNAME_CNTPQ_INF', 'forge'),
            'password' => env('DB_PASSWORD_CNTPQ_INF', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
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
            'options'   => [
                PDO::ATTR_EMULATE_PREPARES => true
            ]
        ],

        'igh92' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_IGH92', '127.0.0.1'),
            'port' => env('DB_PORT_IGH92', '3306'),
            'database' => env('DB_DATABASE_IGH92', 'forge'),
            'username' => env('DB_USERNAME_IGH92', 'forge'),
            'password' => env('DB_PASSWORD_IGH92', ''),
            'unix_socket' => env('DB_SOCKET_IGH92', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

        'interfaz' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_INTERFAZ', 'localhost'),
            'database' => env('DB_DATABASE_INTERFAZ', 'forge'),
            'username' => env('DB_USERNAME_INTERFAZ', 'forge'),
            'password' => env('DB_PASSWORD_INTERFAZ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
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
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
        ],

        'repseg' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_REPSEG', 'localhost'),
            'database' => env('DB_DATABASE_REPSEG', 'forge'),
            'username' => env('DB_USERNAME_REPSEG', 'forge'),
            'password' => env('DB_PASSWORD_REPSEG', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'correos' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_CORREOS', 'localhost'),
            'database' => env('DB_DATABASE_CORREOS', 'forge'),
            'username' => env('DB_USERNAME_CORREOS', 'forge'),
            'password' => env('DB_PASSWORD_CORREOS', ''),
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
            /*'options' => [
                PDO::ATTR_TIMEOUT => 300,
            ]*/
            /*'options' =>[
                PDO::DBLIB_ATTR_STRINGIFY_UNIQUEIDENTIFIER => true,
            ],*/
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

        'contratos_legales' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_CONTRATOS_LEGALES', '127.0.0.1'),
            'port' => env('DB_PORT_CONTRATOS_LEGALES', '3306'),
            'database' => env('DB_DATABASE_CONTRATOS_LEGALES', 'forge'),
            'username' => env('DB_USERNAME_CONTRATOS_LEGALES', 'forge'),
            'password' => env('DB_PASSWORD_CONTRATOS_LEGALES', ''),
            'unix_socket' => env('DB_SOCKET_CONTRATOS_LEGALES', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

        'controlrec' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_CONTROL_RECURSOS', '127.0.0.1'),
            'port' => env('DB_PORT_CONTROL_RECURSOS', '3306'),
            'database' => env('DB_DATABASE_CONTROL_RECURSOS', 'forge'),
            'username' => env('DB_USERNAME_CONTROL_RECURSOS', 'forge'),
            'password' => env('DB_PASSWORD_CONTROL_RECURSOS', ''),
            'unix_socket' => env('DB_SOCKET_CONTROL_RECURSOS', ''),
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
