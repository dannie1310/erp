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
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\AlmacenController@show')->where(['id' => '[0-9]+']);
        });

        //BANCOS
        $api->group(['prefix'=>'banco'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\BancoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\BancoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\BancoController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\BancoController@store');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\BancoController@update')->where(['id' => '[0-9]+']);
        });

        // CLIENTES
        $api->group(['prefix' => 'cliente'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\ClienteController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ClienteController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ClienteController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\ClienteController@store');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\ClienteController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\ClienteController@destroy')->where(['id' => '[0-9]+']);
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

        // DESTAJISTAS
        $api->group(['prefix' => 'destajista'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\DestajistaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\DestajistaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\DestajistaController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\DestajistaController@store');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\DestajistaController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\DestajistaController@destroy')->where(['id' => '[0-9]+']);
        });

        // EMPRESAS
        $api->group(['prefix' => 'empresa'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\EmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\EmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\EmpresaController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\EmpresaController@store');

        });

        // FAMILIAS
        $api->group(['prefix' => 'familia'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\FamiliaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\FamiliaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\FamiliaController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\FamiliaController@store');
        });

        // FONDOS
        $api->group(['prefix' =>  'fondo'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\FondoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\FondoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\FondoController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\FondoController@store');

        });

        // INVENTARIOS
        $api->group(['prefix' => 'inventario'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\InventarioController@index');
        });

        // MATERIALES
        $api->group(['prefix' => 'material'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\MaterialController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\MaterialController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\MaterialController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\MaterialController@store');
            $api->get('/descargar_lista_material', 'App\Http\Controllers\v1\CADECO\MaterialController@descargar_lista_material');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\MaterialController@destroy')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\MaterialController@update')->where(['id' => '[0-9]+']);
            $api->get('buscarMateriales', 'App\Http\Controllers\v1\CADECO\MaterialController@buscarMateriales');
        });

        // MONEDA
        $api->group(['prefix' => 'moneda'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\MonedaController@index');
            $api->get('/monedasGlobales', 'App\Http\Controllers\v1\CADECO\MonedaController@monedasGlobales');
        });

        // OBRA
        $api->group(['prefix' => 'obra'], function ($api) {
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ObraController@show');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\ObraController@update');
            $api->patch('{id}/updateGeneral', 'App\Http\Controllers\v1\CADECO\ObraController@updateGeneral');
            $api->patch('estado/{id}', 'App\Http\Controllers\v1\CADECO\ObraController@actualizarEstado');
            $api->patch('estadoGeneral/{id}', 'App\Http\Controllers\v1\CADECO\ObraController@actualizarEstadoGeneral');
            $api->post('{id}/global', 'App\Http\Controllers\v1\CADECO\ObraController@busquedaSinContexto');
        });

        // PROVEEDOR/CONTRATISTA
        $api->group(['prefix' => 'proveedor-contatista'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ProveedorcontratistaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ProveedorcontratistaController@show');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\ProveedorcontratistaController@store');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\ProveedorcontratistaController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\ProveedorcontratistaController@destroy')->where(['id' => '[0-9]+']);
        });

        // SUCURSAL
        $api->group(['prefix' => 'sucursal'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\SucursalController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\SucursalController@paginate');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\SucursalController@update')->where(['id' => '[0-9]+']);
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\SucursalController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\SucursalController@store');
            $api->post('proveedor', 'App\Http\Controllers\v1\CADECO\SucursalController@storeProveedorSucursal');
            $api->patch('{id}/proveedor', 'App\Http\Controllers\v1\CADECO\SucursalController@updateProveedorSucursal');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\SucursalController@destroy')->where(['id' => '[0-9]+']);
        });

        // SUMINISTRADO
        $api->group(['prefix'=>'suministrado'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\SuministradoController@store');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\SuministradoController@destroy');
        });

        //UNIDAD
        $api->group(['prefix'=>'unidad'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\UnidadController@index');
            $api->get('{id}/unidad', 'App\Http\Controllers\v1\CADECO\UnidadController@show');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\UnidadController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\UnidadController@store');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\UnidadController@destroy');
            $api->patch('{id}/update', 'App\Http\Controllers\v1\CADECO\UnidadController@update');
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
     * REPORTES
     */
    $api->group(['middleware' => 'api', 'prefix' => 'reportes'], function ($api) {
        $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Reportes\ReporteController@paginate');
        $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Reportes\ReporteController@show')->where(['id' => '[0-9]+']);
    });

    /**
     * CONTABILIDAD GENERAL
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contabilidad-general'], function ($api) {
        $api->group(['prefix' => 'empresa'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CTPQ\EmpresaController@store');
            $api->post('/connect','App\Http\Controllers\v1\CTPQ\EmpresaController@conectar');
            $api->get('/', 'App\Http\Controllers\v1\CTPQ\EmpresaController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CTPQ\EmpresaController@show')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'poliza'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CTPQ\PolizaController@store');
            $api->get('/', 'App\Http\Controllers\v1\CTPQ\PolizaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CTPQ\PolizaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CTPQ\PolizaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CTPQ\PolizaController@update')->where(['id' => '[0-9]+']);
            $api->get('{id}/pdf', 'App\Http\Controllers\v1\CTPQ\PolizaController@pdf')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'incidente-poliza'], function ($api) {//buscar-diferencias
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@store');
            $api->post('buscar-diferencias', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@buscarDiferencias');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@update')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'solicitud-edicion-poliza'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@store');
            $api->post('{id}/autorizar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@autorizar')->where(['id' => '[0-9]+']);
            $api->post('{id}/rechazar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@rechazar')->where(['id' => '[0-9]+']);
            $api->post('{id}/aplicar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@aplicar')->where(['id' => '[0-9]+']);
            $api->get('{id}/descargar-xls', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@descargarXLS')->where(['id' => '[0-9]+']);
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@update')->where(['id' => '[0-9]+']);
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@index');
            $api->post('/carga-masiva', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@cargaXLS');
            $api->get('{id}/impresion-polizas', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@impresionPolizas')->where(['id' => '[0-9]+']);
            $api->get('{id}/impresion-polizas-propuesta', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@impresionPolizasPropuesta')->where(['id' => '[0-9]+']);
            $api->get('{id}/impresion-polizas-original', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionController@impresionPolizasOriginal')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'lista-empresa'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\ListaEmpresasController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\ListaEmpresasController@paginate');
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\ListaEmpresasController@update')->where(['id' => '[0-9]+']);
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\ListaEmpresasController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/consolidar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\ListaEmpresasController@consolidar')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'empresa-sat'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\EmpresaSATController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\EmpresaSATController@paginate');
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\EmpresaSATController@update')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'cfd-sat'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@paginate');
            $api->post('carga-zip', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@cargaZIP');
            $api->post('procesa-dir-zip-cfd', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@procesaDirectorioZIPCFD');
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@update')->where(['id' => '[0-9]+']);
        });
    });

    /**
     * CONFIGURACION
     */
    $api->group(['middleware' => 'api', 'prefix' => 'configuracion'], function ($api) {
        $api->group(['prefix' => 'area-subcontratante'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@store');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@show')->where(['id' => '[0-9]+']);
            $api->get('por-usuario/{user_id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@porUsuario')->where(['user_id' => '[0-9]+']);
            $api->post('asignacion-areas-subcontratantes', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@asignacionAreas');
        });
        $api->group(['prefix' => 'area-compradora'], function ($api) {
            $api->post('asignar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\AreaCompradoraController@asignar');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\AreaCompradoraController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\AreaCompradoraController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\AreaCompradoraController@show')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'area-solicitante'], function ($api) {
            $api->post('asignar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\AreaSolicitanteController@asignar');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\AreaSolicitanteController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\AreaSolicitanteController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\AreaSolicitanteController@show')->where(['id' => '[0-9]+']);
        });

        $api->group(['prefix'=>'ctg_tipo'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Compras\CtgTipoController@index');
        });

        /// NODOS TIPO
        $api->group(['prefix' => 'nodo-tipo'], function ($api) {
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Configuracion\NodoTipoController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Configuracion\NodoTipoController@destroy')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Configuracion\NodoTipoController@store');
        });
        $api->group(['prefix' => 'nodo-proyecto'], function ($api) {
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Configuracion\NodoProyectoController@show')->where(['id' => '[0-9]+']);
        });
        // NODOS PROYECTO
    });

    /**
     * ALMACENES
     */
    $api->group(['middleware' => 'api', 'prefix' => 'almacenes'], function ($api) {

        //AJUSTE INVENTARIOS
        $api->group(['prefix' => 'ajuste-inventario'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Almacenes\AjusteController@paginate');

        //AJUSTE POSITIVO (+)
            $api->group(['prefix' => 'positivo'], function ($api) {
                $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\AjustePositivoController@store');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\AjustePositivoController@show')->where(['id' => '[0-9]+']);
                $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\AjustePositivoController@destroy')->where(['id' => '[0-9]+']);
                $api->post('layout', 'App\Http\Controllers\v1\CADECO\Almacenes\AjustePositivoController@cargaLayout');
            });

            //AJUSTE NEGATIVO (-)
            $api->group(['prefix' => 'negativo'], function ($api) {
                $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\AjusteNegativoController@store');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\AjusteNegativoController@show')->where(['id' => '[0-9]+']);
                $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\AjusteNegativoController@destroy')->where(['id' => '[0-9]+']);
                $api->post('layout', 'App\Http\Controllers\v1\CADECO\Almacenes\AjusteNegativoController@cargaLayout');
            });

            //NUEVO LOTE
            $api->group(['prefix' => 'nuevo-lote'], function ($api) {
                $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\NuevoLoteController@store');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\NuevoLoteController@show')->where(['id' => '[0-9]+']);
                $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\NuevoLoteController@destroy')->where(['id' => '[0-9]+']);
                $api->post('layout', 'App\Http\Controllers\v1\CADECO\Almacenes\NuevoLoteController@cargaLayout');
            });

        });

        //CONTEO
        $api->group(['prefix' => 'conteo'], function ($api) {
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\ConteoController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\ConteoController@store');
            $api->post('codigo-barra', 'App\Http\Controllers\v1\CADECO\Almacenes\ConteoController@storeCodigoBarra');
            $api->post('layout', 'App\Http\Controllers\v1\CADECO\Almacenes\ConteoController@cargaLayout');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Almacenes\ConteoController@paginate');
            $api->get('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Almacenes\ConteoController@cancelar')->where(['id' => '[0-9]+']);
        });

        //CATÁLOGO CONTEO TIPO
        $api->group(['prefix' => 'tipo-conteo'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Almacenes\CtgTipoConteoController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\CtgTipoConteoController@show')->where(['id' => '[0-9]+']);
        });


        // ENTRADA DE ALMACEN
        $api->group(['prefix' => 'entrada'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Almacenes\EntradaAlmacenController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\EntradaAlmacenController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\EntradaAlmacenController@destroy')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato-entrada-almacen', 'App\Http\Controllers\v1\CADECO\Almacenes\EntradaAlmacenController@pdfEntradaAlmacen')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\EntradaAlmacenController@store');
            //ORDEN DE COMPRA
            $api->group(['prefix' => 'orden-compra'], function ($api) {
                $api->get('/', 'App\Http\Controllers\v1\CADECO\Almacenes\OrdenCompraController@index');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\OrdenCompraController@show')->where(['id' => '[0-9]+']);
            });
        });

        //INVENTARIO FISICO
        $api->group(['prefix' => 'inventario-fisico'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@paginate');
            $api->get('{id}/pdf_marbetes', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@pdf_marbetes')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout/{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@descargaLayout');
            $api->get('{id}/descargar_resumen_conteo', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@descargar_resumen_conteo');
            $api->patch('{id}/actualizar', 'App\Http\Controllers\v1\CADECO\Almacenes\InventarioFisicoController@actualizar')->where(['id' => '[0-9]+']);
        });

        //MARBETE
        $api->group(['prefix'=>'marbete'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Almacenes\MarbeteController@index');
            $api->get('{id}/porCodigo', 'App\Http\Controllers\v1\CADECO\Almacenes\MarbeteController@showCodigo')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\MarbeteController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Almacenes\MarbeteController@paginate');
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\Almacenes\MarbeteController@destroy')->where(['id' => '[0-9]+']);
        });

        // SALIDA DE ALMACEN
        $api->group(['prefix' => 'salida'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Almacenes\SalidaAlmacenController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\SalidaAlmacenController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Almacenes\SalidaAlmacenController@destroy')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato-salida-almacen', 'App\Http\Controllers\v1\CADECO\Almacenes\SalidaAlmacenController@pdfSalidaAlmacen')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Almacenes\SalidaAlmacenController@store');
            $api->patch('{id}/actualizarEntrega', 'App\Http\Controllers\v1\CADECO\Almacenes\SalidaAlmacenController@actualizarEntregaContratista')->where(['id' => '[0-9]+']);

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
            $api->get('{id}/editar', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@showEdit')->where(['id' => '[0-9]+']);
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
        // ASIGNACIÓN
        $api->group(['prefix' => 'asignacion'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionController@paginate');
            $api->post('layout', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionController@cargaLayout');
            $api->get('descargaLayout', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionController@descargaLayout');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/asignacion', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionController@asignacion')->where(['id' => '[0-9]+']);
        });

         // ITEM CONTRATISTA
        $api->group(['prefix' => 'item-contratista'], function ($api) {
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Compras\ItemContratistaController@destroy')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Compras\ItemContratistaController@update')->where(['id' => '[0-9]+']);
        });

         // COTIZACIÓN
        $api->group(['prefix' => 'cotizacion'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@paginate');
            $api->get('{id}/layout', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@descargaLayout')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\Compras\CotizacionController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@show')->where(['id' => '[0-9]+']);

        });

         // ORDEN DE COMPRA
        $api->group(['prefix' => 'orden-compra'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato-orden-compra', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@pdfOrdenCompra')->where(['id' => '[0-9]+']);
        });

        //REQUISICIÓN
        $api->group(['prefix' => 'requisicion'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\RequisicionController@index');
            $api->post('layout', 'App\Http\Controllers\v1\CADECO\Compras\RequisicionController@cargaLayout');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\RequisicionController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\RequisicionController@show')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\Compras\RequisicionController@store');
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Compras\RequisicionController@pdfRequisicion')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Compras\RequisicionController@destroy')->where(['id' => '[0-9]+']);
        });

        // SOLICITUD DE COMPRA
        $api->group(['prefix' => 'solicitud-compra'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@paginate');
            $api->patch('{id}/aprobar', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@aprobar')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@destroy')->where(['id' => '[0-9]+']);
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@pdfSolicitudCompra')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato-cotizacion', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@pdfCotizacion')->where(['id' => '[0-9]+']);
        });
    });

    /**
     * CONTRATOS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contratos'], function ($api) {
        /**
         * CONTRATO PROYECTADO
         */
        $api->group(['prefix' => 'contrato-proyectado'], function ($api){
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@show')->where(['id' => '[0-9]+']);
            $api->get('getArea', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@getArea');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@paginate');
            $api->patch('{id}/actualizar', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@actualiza');
        });

        /**
         * CONCEPTOS
         */
        $api->group(['prefix' => 'concepto'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\ContratoController@index');
        });

        /**
         * ESTIMACIÓN
         */
        $api->group(['prefix' => 'estimacion'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/aprobar', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@aprobar')->where(['id' => '[0-9]+']);
            $api->patch('{id}/revertirAprobacion', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@revertirAprobacion')->where(['id' => '[0-9]+']);
            $api->patch('{id}/amortizacion', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@anticipo')->where(['id' => '[0-9]+']);
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@paginate');
            $api->get('{id}/formato-estimacion', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@pdfEstimacion')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@destroy')->where(['id' => '[0-9]+']);
            $api->patch('{id}/registrarRetencionIva', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@registrarRetencionIva')->where(['id' => '[0-9]+']);
            $api->get('{id}/ordenarConceptos', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@ordenarConceptos')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@update')->where(['id' => '[0-9]+']);

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
            $api->get('{id}/ordenarConceptos', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@ordenarConceptos')->where(['id' => '[0-9]+']);
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@paginate');
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

        // RUBROS
        $api->group(['prefix' => 'rubro'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\RubroController@index');
        });

        /**
         * CUENTA BANCARIA EMPRESA
         */
        $api->group(['prefix' => 'cuenta-bancaria-empresa'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@show')->where(['id' => '[0-9]+']);
        });

        // DATOS ESTIMACIONES
        $api->group(['prefix' => 'estimacion'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\ConfiguracionEstimacionController@store');
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\ConfiguracionEstimacionController@index');
        });

        /**
         * DISTRIBUCIÓN DE RECURSOS AUTORIZADOS EN REMESA
         */
        $api->group(['prefix' => 'distribuir-recurso-remesa'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\DistribucionRecursoRemesaController@index');
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
         * FACTURAS
         */
        $api->group(['prefix' => 'factura'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@store');
            $api->post('xml', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@cargaXML');
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@show')->where(['id' => '[0-9]+']);
            $api->get('autorizada', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@autorizadas');
            $api->get('{id}/pendientesPago', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@pendientesPago')->where(['id' => '[0-9]+']);
            $api->get('{id}/revertir', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@revertir')->where(['id' => '[0-9]+']);
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@paginate');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@destroy')->where(['id' => '[0-9]+']);
            /**
             * FORMATO DE CONTRARECIBO
             */
            $api->get('{id}/formato-cr', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@pdfCR')->where(['id' => '[0-9]+']);
        });

        /**
         * FONDO
         */

        $api->group(['prefix' => 'fondo'], function ($api) {

            $api->get('tipo-fondo', 'App\Http\Controllers\v1\CADECO\Finanzas\CtgTipoFondoController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\CtgTipoFondoController@show')->where(['id' => '[0-9]+']);

        });

        /**
         * GESTIÓN CUENTAS BANCARIAS
         */
        $api->group(['prefix' => 'gestion-cuenta-bancaria'], function ($api) {

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
        $api->group(['prefix' => 'gestion-pago'], function ($api) {
            $api->post('registrar_pagos', 'App\Http\Controllers\v1\CADECO\Finanzas\GestionPagoController@registrarPagos');
            $api->post('bitacora', 'App\Http\Controllers\v1\CADECO\Finanzas\GestionPagoController@presentaBitacora');
        });

        /***
         * PAGOS
         */
        $api->group(['prefix' => 'pago'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@paginate');

            $api->group(['prefix' => 'carga-masiva'], function ($api) {
                $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@paginate');
                $api->post('layout', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@procesaLayoutPagos');
                $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@store');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@show')->where(['id' => '[0-9]+']);
                $api->get('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@autorizar')->where(['id' => '[0-9]+']);
                $api->get('descarga-layout', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@descargarLayout');
            });
        });

        /**
         * REMESA
         */
        $api->group(['prefix' => 'remesa'], function ($api) {
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
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@pdfPagoAnticipado')->where(['id' => '[0-9]+']);
        });

        /**
         * TESORERIA
         */
        //MOVIMIENTOS BANCARIOS
        $api->group(['prefix' => 'movimiento-bancario'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\MovimientoBancarioController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\MovimientoBancarioController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\MovimientoBancarioController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\MovimientoBancarioController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\MovimientoBancarioController@destroy')->where(['id' => '[0-9]+']);
        });

        //TRASPASO ENTRE CUENTAS
        $api->group(['prefix' => 'traspaso-entre-cuentas'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\TraspasoEntreCuentasController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\TraspasoEntreCuentasController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\TraspasoEntreCuentasController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\TraspasoEntreCuentasController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\TraspasoEntreCuentasController@destroy')->where(['id' => '[0-9]+']);
        });

        //TIPOS MOVIMIENTO
        $api->group(['prefix' => 'tipo-movimiento'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\TipoMovimientoController@index');
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
            $api->get('descarga_listado_permisos_obra/{id}','App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@descargaListadoPermisosObra');
            $api->get('descarga_listado_permisos_usuario/{id}','App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@descargaListadoPermisosUsuario');
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
        $api->group(['prefix' => 'efo'], function ($api) {
            $api->post('layout', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@cargaLayout');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@paginate');
            $api->post('rfc', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@rfc');
        });
        $api->group(['prefix' => 'transaccion-efo'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\TransaccionesEfosController@paginate');
            $api->get('descarga-csv', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\TransaccionesEfosController@descargarCSV');
        });


        $api->group(['prefix' => 'incidencia'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\ControlInterno\IncidenciaController@paginate');
        });
    });

    /** SUBCONTRATOS ESTIMACIONES */
    $api->group(['middleware' => 'api', 'prefix' => 'subcontratos-estimaciones'], function ($api){
        $api->group(['prefix'=>'descuento'], function ($api){
            $api->get('{id}/list', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\DescuentoController@list');
            $api->get('{id}/listItems', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\DescuentoController@listItems')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\DescuentoController@storeItem');
            $api->post('updateList', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\DescuentoController@updateList');
        });

        $api->group(['prefix'=>'retencion'], function ($api){
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionController@store');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionController@destroy')->where(['id' => '[0-9]+']);
            $api->get('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionController@index');
        });

        $api->group(['prefix'=>'penalizacion'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\PenalizacionController@store');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\PenalizacionController@destroy')->where(['id' => '[0-9]+']);
            $api->get('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\PenalizacionController@index');
        });

        $api->group(['prefix'=>'retencion-liberacion'], function ($api){
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionLiberacionController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionLiberacionController@store');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionLiberacionController@destroy')->where(['id' => '[0-9]+']);
            $api->get('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionLiberacionController@index');
        });

        $api->group(['prefix'=>'penalizacion-liberacion'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\PenalizacionLiberacionController@store');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\PenalizacionLiberacionController@destroy')->where(['id' => '[0-9]+']);
            $api->get('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\PenalizacionLiberacionController@index');
        });

        $api->group(['prefix'=>'retencion-tipo'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones\RetencionTipoController@index');
        });
    });

    /** Ventas */
    $api->group(['middleware' => 'api', 'prefix' => 'ventas'], function ($api){

        $api->group(['prefix'=>'venta'], function ($api){
            $api->get('{id}/pdf_venta', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@pdfVenta');
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

    /*SCI*/

    $api->group(['middleware'=>'api', 'prefix'=> 'SCI'], function ($api){

        $api->group(['prefix' => 'marca'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SCI\MarcaController@index');
        });

        $api->group(['prefix' => 'modelo'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\SCI\ModeloController@index');
        });
    });

//    VENTAS
    $api->group(['middleware' => 'api', 'prefix' => 'ventas'], function ($api) {
        $api->group(['prefix' => 'venta'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/pdf_venta', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@pdfVenta')->where(['id' => '[0-9]+']);
            $api->get('{id}/pdf_factura', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@pdfFactura')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@destroy')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@store');
        });
    });
});
