<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api', 'prefix' => 'auth'], function ($api) {
        $api->post('login', 'App\Http\Controllers\v1\AuthController@login');
        $api->post('logout', 'App\Http\Controllers\v1\AuthController@logout');
        $api->post('setContext', 'App\Http\Controllers\v1\AuthController@setContext');
        $api->post('getContext', 'App\Http\Controllers\v1\AuthController@getContext');
        $api->post('refresh', 'App\Http\Controllers\v1\AuthController@refresh');
        $api->get('obras', 'App\Http\Controllers\v1\AuthController@obras');
    });

    /**
     * DBO
     */
    $api->group(['middleware' => 'api'], function ($api) {
        // ALMACENES
        $api->group(['prefix' => 'almacen'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\AlmacenController@index');
        });

        // CONCEPTOS
        $api->group(['prefix' => 'concepto'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\ConceptoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ConceptoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ConceptoController@show')->where(['id' => '[0-9]+']);
        });

        // COSTOS
        $api->group(['prefix' => 'costo'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\CostoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\CostoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\CostoController@show')->where(['id' => '[0-9]+']);
        });

        // CUENTAS
        $api->group(['prefix' => 'cuenta'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\CuentaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\CuentaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\CuentaController@show')->where(['id' => '[0-9]+']);

        });

        // EMPRESAS
        $api->group(['prefix' => 'empresa'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\EmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\EmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\EmpresaController@show')->where(['id' => '[0-9]+']);
        });

        // FONDOS
        $api->group(['prefix' =>  'fondo'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\FondoController@index');
        });

        // MATERIALES
        $api->group(['prefix' => 'material'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\MaterialController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\MaterialController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\MaterialController@show')->where(['id' => '[0-9]+']);
        });
    });

    /**
     * CONTABILIDAD
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contabilidad'], function ($api) {
        //CIERRES DE PERIODO
        $api->group(['prefix' => 'cierre-periodo'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/abrir', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@abrir')->where(['id' => '[0-9]+']);
            $api->patch('{id}/cerrar', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@cerrar')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE ALMACÉN
        $api->group(['prefix' => 'cuenta-almacen'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE BANCO
        $api->group(['prefix' => 'cuenta-banco'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@destroy')->where(['id' => '[0-9]+']);

        });

        //CUENTAS DE CONCEPTO
        $api->group(['prefix' => 'cuenta-concepto'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaConceptoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaConceptoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaConceptoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaConceptoController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE COSTO
        $api->group(['prefix' => 'cuenta-costo'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE EMPRESA
        $api->group(['prefix' => 'cuenta-empresa'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@destroy')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE FONDO
        $api->group(['prefix' => 'cuenta-fondo'], function ($api){
            $api->post('/','App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@store');
            $api->get('paginate','App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS GENERALES
        $api->group(['prefix' => 'cuenta-general'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE MATERIALES
        $api->group(['prefix' => 'cuenta-material'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaMaterialController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaMaterialController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaMaterialController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaMaterialController@update')->where(['id' => '[0-9]+']);
        });

        //ESTATUS PREPÓLIZA
        $api->group(['prefix' => 'estatus-prepoliza'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\EstatusPrepolizaController@index');
        });

        //PÓLIZAS
        $api->group(['prefix' => 'poliza'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@update')->where(['id' => '[0-9]+']);
            $api->patch('{id}/omitir', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@omitir')->where(['id' => '[0-9]+']);
            $api->patch('{id}/validar', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@validar')->where(['id' => '[0-9]+']);
        });

        //TIPOS CUENTA CONTABLE
        $api->group(['prefix' => 'tipo-cuenta-contable'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@index');
        });

        //TIPOS CUENTA EMPRESA
        $api->group(['prefix' => 'tipo-cuenta-empresa'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaEmpresaController@index');
        });

        //TIPOS CUENTA MATERIAL
        $api->group(['prefix' => 'tipo-cuenta-material'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaMaterialController@index');
        });

        //TIPOS PÓLIZA CONTPAQ
        $api->group(['prefix' => 'tipo-poliza-contpaq'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoPolizaContpaqController@index');
        });
    });

    /**
     * TESORERIA
     */
    $api->group(['middleware' => 'api', 'prefix' => 'tesoreria'], function ($api) {
        //MOVIMIENTOS BANCARIOS
        $api->group(['prefix' => 'movimiento-bancario'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@destroy')->where(['id' => '[0-9]+']);
        });

        //TIPOS MOVIMIENTO
        $api->group(['prefix' => 'tipo-movimiento'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Tesoreria\TipoMovimientoController@index');
        });
    });

    /**
     * CHARTS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'chart'], function ($api) {
        $api->get('avance-cuentas-contables', 'App\Http\Controllers\v1\ChartController@avanceCuentasContables');
        $api->get('prepolizas-semanal', 'App\Http\Controllers\v1\ChartController@prepolizasSemanal');
        $api->get('prepolizas-acumulado', 'App\Http\Controllers\v1\ChartController@polizasDoughnut');
    });
});