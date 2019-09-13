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
      /*  $api->get('obras', 'App\Http\Controllers\v1\AuthController@obras');*/
        $api->get('obras/paginate', 'App\Http\Controllers\v1\CADECO\ObraController@authPaginate');
        $api->get('obras/por-usuario/{id_usuario}', 'App\Http\Controllers\v1\CADECO\ObraController@porUsuario')->where(['id_usuario' => '[0-9]+']);
    });

    /**
     * DBO
     */
    $api->group(['middleware' => 'api'], function ($api) {
        // ALMACENES
        $api->group(['prefix' => 'almacen'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\AlmacenController@index');
        });

        $api->group(['prefix'=>'banco'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\BancoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\BancoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\BancoController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\BancoController@store');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\BancoController@update')->where(['id' => '[0-9]+']);
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
            $api->post('/', 'App\Http\Controllers\v1\CADECO\CuentaController@store');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\CuentaController@update')->where(['id' => '[0-9]+']);

        });

        // EMPRESAS
        $api->group(['prefix' => 'empresa'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\EmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\EmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\EmpresaController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\EmpresaController@store');

        });

        // FONDOS
        $api->group(['prefix' =>  'fondo'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\FondoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\FondoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\FondoController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\FondoController@store');

        });

        // MATERIALES
        $api->group(['prefix' => 'material'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\MaterialController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\MaterialController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\MaterialController@show')->where(['id' => '[0-9]+']);
        });

        // MONEDA
        $api->group(['prefix' => 'moneda'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\MonedaController@index');
        });

        // OBRA
        $api->group(['prefix' => 'obra'], function ($api) {
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ObraController@show');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\ObraController@update');
            $api->patch('estado/{id}', 'App\Http\Controllers\v1\CADECO\ObraController@actualizarEstado');
        });

        // SUCURSAL
        $api->group(['prefix' => 'sucursal'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\SucursalController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\SucursalController@paginate');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\SucursalController@update');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\SucursalController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\SucursalController@store');
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

    /**
     * CONFIGURACION
     */
    $api->group(['middleware' => 'api', 'prefix' => 'CONFIGURACION'], function ($api) {
        $api->group(['prefix' => 'area-subcontratante'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@store');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@show')->where(['id' => '[0-9]+']);
            $api->get('por-usuario/{user_id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@porUsuario')->where(['user_id' => '[0-9]+']);
            $api->post('asignacion-areas-subcontratantes', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@asignacionAreas');
        });
    });

    /**
     * ALMACENES
     */
    $api->group(['middleware' => 'api', 'prefix' => 'almacenes'], function ($api) {

        //AJUSTE POSITIVO (+)
        $api->group(['prefix' => 'ajuste-positivo'], function ($api) {
//            $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\AjustePositivoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Almacenes\AjustePositivoController@paginate');
        });

        //INVENTARIO FISICO
        $api->group(['prefix' => 'inventario-fisico'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@paginate');
            $api->get('{id}/pdf_marbetes', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@pdf_marbetes')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout/{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@descargaLayout');
            $api->get('{id}/descargar_resumen_conteo', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@descargar_resumen_conteo');
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
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@destroy')->where(['id' => '[0-9]+']);
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

        // DATOS CONTABLES
        $api->group(['prefix' => 'datos-contables'], function ($api){
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\DatosContablesController@update')->where(['id' => '[0-9]+']);
        });

        //ESTATUS PREPÓLIZA
        $api->group(['prefix' => 'estatus-prepoliza'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\EstatusPrepolizaController@index');
        });

        //NATURALEZA PÓLIZA
        $api->group(['prefix' => 'naturaleza-poliza'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\NaturalezaPolizaController@index');
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
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@destroy')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@update')->where(['id' => '[0-9]+']);
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

        //TRANSACCIÓN INTERFÁZ
        $api->group(['prefix' => 'transaccion-interfaz'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TransaccionInterfazController@index');
        });
    });

    /**
     * COMPRAS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'compras'], function ($api) {

        //ALMACENES
        $api->group(['prefix' => 'almacen'], function ($api) {

            // ENTRADA DE ALMACEN
            $api->group(['prefix' => 'entrada'], function ($api) {
                $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\EntradaAlmacenController@paginate');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\EntradaAlmacenController@show')->where(['id' => '[0-9]+']);
                $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Compras\EntradaAlmacenController@destroy')->where(['id' => '[0-9]+']);
            });


            // SALIDA DE ALMACEN
            $api->group(['prefix' => 'salida'], function ($api) {
                $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\SalidaAlmacenController@paginate');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\SalidaAlmacenController@show')->where(['id' => '[0-9]+']);
                $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Compras\SalidaAlmacenController@destroy')->where(['id' => '[0-9]+']);


            });
        });

         // ORDEN DE COMPRA
        $api->group(['prefix' => 'orden-compra'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato-orden-compra', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@pdfOrdenCompra')->where(['id' => '[0-9]+']);
        });


        // SOLICITUD DE COMPRA
        $api->group(['prefix' => 'solicitud-compra'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@paginate');
        });
    });

    /**
     * CONTRATOS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contratos'], function ($api) {
        /**
         * PROYECTADO
         */
        $api->group(['prefix' => 'contrato-proyectado'], function ($api){
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@show')->where(['id' => '[0-9]+']);
            $api->get('getArea', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@getArea');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@paginate');
            $api->patch('{id}/actualizar', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@actualiza');

        });

        /**
         * ESTIMACIÓN
         */
        $api->group(['prefix' => 'estimacion'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/aprobar', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@aprobar')->where(['id' => '[0-9]+']);
            $api->patch('{id}/revertirAprobacion', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@revertirAprobacion')->where(['id' => '[0-9]+']);
            $api->get('{id}/getConceptos', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@getConceptos')->where(['id' => '[0-9]+']);
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@paginate');
            $api->get('{id}/formato-estimacion', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@pdfEstimacion')->where(['id' => '[0-9]+']);
            $api->get('{id}/showEstimacionTable','App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@showEstimacionTable');

            /**
             * FORMATO ORDEN DE PAGO DE ESTIMACION
             */
            $api->get('{id}/formato-orden-pago', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@pdfOrdenPago')->where(['id' => '[0-9]+']);
        });


        /**
         * SUBCONTRATO
         */
        $api->group(['prefix' => 'subcontrato'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/getConceptosNuevaEstimacion', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@getConceptosNuevaEstimacion')->where(['id' => '[0-9]+']);
        });
        //FONDO DE GARANTÍA
        $api->group(['prefix' => 'fondo-garantia'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@show')->where(['id' => '[0-9]+']);
            $api->post('{id}/ajustar_saldo', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@ajustarSaldo')->where(['id' => '[0-9]+']);
            //SOLICITUD DE MOVIMIENTO
            $api->group(['prefix' => 'solicitud-movimiento'], function ($api) {
                $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@paginate');
                $api->post('/', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@store');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@show')->where(['id' => '[0-9]+']);
                $api->patch('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@autorizar')->where(['id' => '[0-9]+']);
                $api->patch('{id}/rechazar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@rechazar')->where(['id' => '[0-9]+']);
                $api->patch('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@cancelar')->where(['id' => '[0-9]+']);
                $api->patch('{id}/revertir-autorizacion', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@revertirAutorizacion')->where(['id' => '[0-9]+']);
            });
        });
    });

    /**
     * FINANZAS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'finanzas'], function ($api) {

        /**
         * CUENTA BANCARIA EMPRESA
         */
        $api->group(['prefix' => 'cuenta-bancaria-empresa'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@show')->where(['id' => '[0-9]+']);
        });

        // DATOS ESTIMACIONES
        $api->group(['prefix' => 'estimacion'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\ConfiguracionEstimacionController@store');
        });

        /**
         * DISTRIBUCIÓN DE RECURSOS AUTORIZADOS EN REMESA
         */
        $api->group(['prefix' => 'distribuir-recurso-remesa'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@store');
            $api->get('{id}/layout', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@descargaLayout')->where(['id' => '[0-9]+']);
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@autorizar')->where(['id' => '[0-9]+']);
            $api->get('{id}/validar', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@validar')->where(['id' => '[0-9]+']);
            $api->patch('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@cancelar')->where(['id' => '[0-9]+']);
            $api->post('{id}/cargaLayoutManual', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@cargarLayoutManual')->where(['id' => '[0-9]+']);
        });

        /**
         * FONDO
         */

        $api->group(['prefix'=>'fondo'],function ($api){

            $api->get('tipo-fondo','App\Http\Controllers\v1\CADECO\Finanzas\CtgTipoFondoController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\CtgTipoFondoController@show')->where(['id' => '[0-9]+']);

        });

        /**
         * GESTIÓN CUENTAS BANCARIAS
         */
        $api->group(['prefix' => 'gestion-cuenta-bancaria'], function ($api){

            /**
             * SOLICITUD DE ALTA
             */
            $api->group(['prefix' => 'solicitud-alta'], function ($api) {
                $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudAltaCuentaBancariaController@paginate');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudAltaCuentaBancariaController@show')->where(['id' => '[0-9]+']);
                $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudAltaCuentaBancariaController@store');
                $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudAltaCuentaBancariaController@pdf');
                $api->get('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudAltaCuentaBancariaController@autorizar')->where(['id' => '[0-9]+']);
                $api->get('{id}/rechazar', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudAltaCuentaBancariaController@rechazar')->where(['id' => '[0-9]+']);
                $api->get('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudAltaCuentaBancariaController@cancelar')->where(['id' => '[0-9]+']);
            });

            /**
             * SOLICITUD DE BAJA
             */
            $api->group(['prefix' => 'solicitud-baja'], function ($api) {
               $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudBajaCuentaBancariaController@store');
                $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudBajaCuentaBancariaController@paginate');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudBajaCuentaBancariaController@show')->where(['id' => '[0-9]+']);
                $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudBajaCuentaBancariaController@pdf')->where(['id' => '[0-9]+']);
                $api->get('{id}/rechazar', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudBajaCuentaBancariaController@rechazar')->where(['id' => '[0-9]+']);
                $api->get('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudBajaCuentaBancariaController@cancelar')->where(['id' => '[0-9]+']);
                $api->get('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudBajaCuentaBancariaController@autorizar')->where(['id' => '[0-9]+']);
            });
        });

        /**
         * GESTIÓN PAGOS
         */
        $api->group(['prefix' => 'gestion-pago'], function ($api){
            $api->post('registrar_pagos', 'App\Http\Controllers\v1\CADECO\Finanzas\GestionPagoController@registrarPagos');
            $api->post('bitacora', 'App\Http\Controllers\v1\CADECO\Finanzas\GestionPagoController@presentaBitacora');
        });


        /***
         * PAGOS
         */
        $api->group(['prefix' => 'pago'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@paginate');
        });

        /**
         * REMESA
         */
        $api->group(['prefix' => 'remesa'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\MODULOSSAO\RemesaController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\MODULOSSAO\RemesaController@show')->where(['id' => '[0-9]+']);
        });

        /**
         * SOLICITUD DE PAGO ANTICIPADO
         */
        $api->group(['prefix' => 'solicitud-pago-anticipado'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@cancelar')->where(['id' => '[0-9]+']);
            $api->get('pdf/{id}','App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@pdfPagoAnticipado')->where(['id'=>'[0-9]+']);
        });
    });

    /**
     * PERSONALIZADO
     */
    $api->group(['middleware' => 'api', 'prefix' => 'seguridad'], function($api){

        //ESQUEMA PERSONALIZADO
        $api->group(['prefix' => 'rol'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@store');
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@show')->where(['id' => '[0-9]+']);
            $api->get('por-usuario/{user_id}', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@porUsuario')->where(['user_id' => '[0-9]+']);
            $api->post('asignacion-masiva', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@asignacionPersonalizada');
            $api->post('desasignacion-masiva', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@desasignacionPersonalizada');
            $api->post('asignacion-permisos', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@asignacionPermisos');
            //$api->post('crear-rol', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@crearRol');
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

        //TRASPASO ENTRE CUENTAS
        $api->group(['prefix' => 'traspaso-entre-cuentas'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@destroy')->where(['id' => '[0-9]+']);
        });

        //TIPOS MOVIMIENTO
        $api->group(['prefix' => 'tipo-movimiento'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Tesoreria\TipoMovimientoController@index');
        });
    });


    /** SEGURIDAD ERP */
    $api->group(['middleware' => 'api', 'prefix' => 'SEGURIDAD_ERP'], function ($api) {

        $api->group(['prefix' => 'configuracion-obra'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\ConfiguracionObraController@index');
            $api->get('contexto', 'App\Http\Controllers\v1\SEGURIDAD_ERP\ConfiguracionObraController@contexto');
        });

        $api->group(['prefix' => 'permiso'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@index');
            $api->get('por-usuario/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@porUsuario')->where(['id' => '[0-9]+']);
            $api->get('por-obra/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@porObra')->where(['id' => '[0-9]+']);
            $api->get('por-usuario-auditoria/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@porUsuarioAuditoria')->where(['id' => '[0-9]+']);
            $api->get('por-cantidad', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@porCantidad');
        });

        $api->group(['prefix' => 'rol'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@store');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@destroy')->where(['id' => '[0-9]+']);
            $api->get('por-usuario/{user_id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@porUsuario')->where(['user_id' => '[0-9]+']);
            $api->post('asignacion-masiva', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@asignacionMasiva');
            $api->post('desasignacion-masiva', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@desasignacionMasiva');
            $api->post('asignacion-permisos', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@asignacionPermisos');
        });

        $api->group(['prefix' => 'sistema'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\SistemaController@index');
            $api->get('sistemas-obra', 'App\Http\Controllers\v1\SEGURIDAD_ERP\SistemaController@porObra');
            $api->post('asignacion-sistemas', 'App\Http\Controllers\v1\SEGURIDAD_ERP\SistemaController@asignacionSistemas');
        });

        $api->group(['prefix' => 'tipo-proyecto'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\TipoProyectoController@index');
        });

        $api->group(['prefix' => 'google-2fa'], function ($api) {
            $api->get('qr', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Google2faController@qr');
            $api->get('code', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Google2faController@code');
            $api->post('check', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Google2faController@check');
            $api->get('isVerified', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Google2faController@isVerified');
            $api->get('secret-code', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Google2faController@secretCode');
        });

        $api->group(['prefix'=>'ctg_banco'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgBancoController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgBancoController@show');
        });
        $api->group(['prefix'=>'ctg_plaza'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgPlazaController@index');
        });
    });

    /** IGH */
    $api->group(['middleware' => 'api', 'prefix' => 'IGH'], function ($api) {
        $api->group(['prefix' => 'usuario'], function ($api) {
            $api->get('currentUser', 'App\Http\Controllers\v1\IGH\UsuarioController@currentUser');
            $api->get('/', 'App\Http\Controllers\v1\IGH\UsuarioController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\IGH\UsuarioController@show')->where(['id' => '[0-9]+']);
        });

        $api->group(['prefix' => 'menu'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\IGH\MenuController@index');
        });
    });
});
