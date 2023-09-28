<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],
        /**
         * Disks para configurar almacenamiento de layouts bancarios de Santander por H2H y Portal
         */
        'h2h_in' =>[
            'driver' => 'local',
            'root' => storage_path(env('SANTANDER_H2H_STORAGE_IN')),
        ],
        'h2h_out' =>[
            'driver' => 'local',
            'root' => storage_path(env('SANTANDER_H2H_STORAGE_OUT')),
        ],
        'h2h_historico' =>[
            'driver' => 'local',
            'root' => storage_path(env('SANTANDER_H2H_STORAGE_HISTORICO')),
        ],
        'portal_descarga' =>[
            'driver' => 'local',
            'root' => storage_path(env('SANTANDER_PORTAL_STORAGE_DESCARGA')),
        ],
        'portal_carga' =>[
            'driver' => 'local',
            'root' => storage_path(env('SANTANDER_PORTAL_STORAGE_CARGA')),
        ],
        'portal_zip' =>[
            'driver' => 'local',
            'root' => storage_path(env('SANTANDER_PORTAL_STORAGE_ZIP')),
        ],
        'solicitud_cuenta_bancaria' =>[
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_SOPORTE_SOLICITUD_CUENTA_BANCARIA')),
        ],
        'inventario_fisico_descarga' =>[
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_RESUMEN_LAYOUT_INVENTARIO_FISICO')),
        ],
        'lista_insumos' =>[
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_LISTA_MATERIALES')),
        ],
        'pdf_factura_venta' =>[
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_PDF_FACTURA_VENTA')),
        ],
        'lista_efos' =>[
                'driver' => 'local',
                'root' => storage_path(env('STORAGE_LISTA_EFOS')),
        ],
        'layout_pagos_descarga' =>[
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_LAYOUT_PAGOS')),
        ],
        'control_interno' => [
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_CONTROL_INTERNO')),
        ],
        'padron_contratista' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_PADRON_CONTRATISTAS')),
        ],
        'padron_contratista_hashfiles' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_PADRON_CONTRATISTAS').'/hashfiles'),
        ],
        'xml_sat' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_XML_SAT')),
        ],
        'xml_errores' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_XML_ERRORES')),
        ],
        'xml_emitidos' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_XML_EMITIDOS')),
        ],
        'xml_control_recursos' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_XML_CONTROL_RECURSOS')),
        ],
        'xml_control_recursos_relacion_gastos' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_XML_CONTROL_RECURSOS_RELACION_GASTOS')),
        ],
        'pdf_emitidos' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_PDF_EMITIDOS')),
        ],
        'archivos_transacciones' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_ARCHIVOS_TRANSACCIONES')),
        ],
        'archivos_invitaciones' => [
            'driver' => 'local',
            'root' => public_path(env('STORAGE_ARCHIVOS_INVITACIONES')),
        ],
        'polizas_pdf' =>[
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_CONTABILIDAD_GENERAL_POLIZAS')),
        ],
        'polizas_zip' =>[
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_CONTABILIDAD_GENERAL_POLIZAS_ZIP')),
        ],
        'contabilidad_electronica' =>[
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_LAYOUT_CONTABILIDAD_ELECTRONICA')),
        ],
        'bancario_recurso_descarga' =>[
            'driver' => 'local',
            'root' => storage_path(env('SANTANDER_RECURSO_BANCARIO_STORAGE_DESCARGA')),
        ],
        'bancario_recurso_descarga_zip' =>[
            'driver' => 'local',
            'root' => storage_path(env('SANTANDER_RECURSO_BANCARIO_STORAGE_DESCARGA_ZIP')),
        ],
    ],
];
