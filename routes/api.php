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
    Route::post('/chat-bot', 'ChatBotController@listenToReplies');
    // $api->group([ 'prefix' => 'movil'], function ($api) {
    //     $api->post('authorizeMovil', 'App\Http\Controllers\Auth\Passport\AuthorizationController@authorizeMovil');
    // });

    Route::get('movil', 'Auth\Passport\AuthorizationController@movil');
    $api->group(['middleware' => 'api', 'prefix' => 'auth'], function ($api) {
        $api->post('login', 'App\Http\Controllers\v1\AuthController@login');
        $api->post('logout', 'App\Http\Controllers\v1\AuthController@logout');
        $api->post('setContext', 'App\Http\Controllers\v1\AuthController@setContext');
      /*  $api->get('obras', 'App\Http\Controllers\v1\AuthController@obras');*/
        $api->get('obras/paginate', 'App\Http\Controllers\v1\CADECO\ObraController@authPaginate');
        $api->get('obras/por-usuario/{id_usuario}', 'App\Http\Controllers\v1\CADECO\ObraController@porUsuario')->where(['id_usuario' => '[0-9]+']);
    });
    $api->group(['middleware' => ['auth:api','scope:autorizar-solicitudes-pago-anticipado']], function ($api) {
        $api->get('solicitud-pago-anticipado/{id}/pide-motivo-rechazo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@pideMotivoRechazoVista')->where(['id' => '[0-9]+']);
        $api->get('solicitud-pago-anticipado/{id}/rechazar-vista', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@rechazarVista')->where(['id' => '[0-9]+']);
        $api->get('solicitud-pago-anticipado/{id}/autorizar-vista', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@autorizarVista')->where(['id' => '[0-9]+']);
        $api->get('solicitud-pago-anticipado/{id}/rechazar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@rechazar')->where(['id' => '[0-9]+']);
        $api->get('solicitud-pago-anticipado/{id}/autorizar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@autorizar')->where(['id' => '[0-9]+']);
        $api->get('solicitud-pago-anticipado/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@showVista')->where(['id' => '[0-9]+']);
        $api->get('solicitud-pago-anticipado', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@indexVista');
    });


    /**
     * DBO
     */
    $api->group(['middleware' => 'api'], function ($api) {
        // ARCHIVO
        $api->group(['prefix' => 'archivo'], function ($api){
            $api->post('cargarArchivo', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@cargarArchivo');
            $api->post('cargarArchivoZIP', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@cargarArchivoZIP');
            $api->get('{id}/documento', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@documento')->where(['id' => '[0-9]+']);
            $api->get('{id}/transaccion', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@getArchivosTransaccion')->where(['id' => '[0-9]+']);
            $api->get('{tipo}/{id}/transaccion-relacionados', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@getArchivosRelacionadosTransaccion')->where(['id' => '[0-9]+']);
            $api->post('{id}', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@destroy')->where(['id' => '[0-9]+']);
            $api->get('{id}/imagenes', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@imagenes')->where(['id' => '[0-9]+']);
            $api->get('{id}/imagenes-sc', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@imagenesSC')->where(['id' => '[0-9]+']);
            $api->post('{id}/transaccion-sc', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@getArchivosTransaccionSC');
            $api->get('{tipo}/{id}/transaccion-relacionados-sc', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@getArchivosRelacionadosTransaccionSC')->where(['id' => '[0-9]+']);
            $api->get('{id}/documento-sc', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@documentoSC')->where(['id' => '[0-9]+']);
            $api->post('cargar-archivo-sc', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@cargarArchivoSC');
            $api->post('{id}/destroy-sc', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@destroySC')->where(['id' => '[0-9]+']);
            $api->get('{id}/invitacion/documento', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@documento')->where(['id' => '[0-9]+']);
            $api->get('{id}/invitacion/documento-sc', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@documentoSC')->where(['id' => '[0-9]+']);
            $api->get('{id}/invitacion', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@getArchivosInvitacion')->where(['id' => '[0-9]+']);
            $api->get('/descargar-archivo-invitacion/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@descargar')->where(['id' => '[0-9]+']);
            $api->get('/descargar-archivo-invitacion-sc/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@descargarSC')->where(['id' => '[0-9]+']);
            $api->get('/consultar-archivo-invitacion/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@show')->where(['id' => '[0-9]+']);
            $api->patch('/eliminar-archivo-invitacion/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@destroy')->where(['id' => '[0-9]+']);
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/descargar', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@descargar')->where(['id' => '[0-9]+']);
            $api->get('{id}/sc', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@showSC')->where(['id' => '[0-9]+']);
            $api->get('{id}/descargar-sc', 'App\Http\Controllers\v1\CADECO\Documentacion\ArchivoController@descargarSC')->where(['id' => '[0-9]+']);
        });
        // ALMACENES
        $api->group(['prefix' => 'almacen'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\AlmacenController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\AlmacenController@show')->where(['id' => '[0-9]+']);
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\AlmacenController@paginate');
            $api->post('/','App\Http\Controllers\v1\CADECO\AlmacenController@store');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\AlmacenController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\AlmacenController@destroy')->where(['id' => '[0-9]+']);
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
            $api->get('{id}/hijosMedibles', 'App\Http\Controllers\v1\CADECO\ConceptoController@conceptosHijosMedible')->where(['id' => '[0-9]+']);
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
            $api->get('{id}/detalleUnificacion', 'App\Http\Controllers\v1\CADECO\EmpresaController@detalleUnificacion')->where(['id' => '[0-9]+']);
            $api->get('{id}/detalleEmpresaUnificacion', 'App\Http\Controllers\v1\CADECO\EmpresaController@detalleEmpresaUnificacion')->where(['id' => '[0-9]+']);

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
            $api->post('/monedasBase', 'App\Http\Controllers\v1\CADECO\MonedaController@monedasBase');
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
        });

         // PROVEEDOR/CONTRATISTA/SUCURSAL
        $api->group(['prefix' => 'proveedor-contratista-sucursal'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\ProveedorContratistaSucursalController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ProveedorContratistaSucursalController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ProveedorContratistaSucursalController@show')->where(['id' => '[0-9]+']);
            $api->post('proveedor', 'App\Http\Controllers\v1\CADECO\ProveedorContratistaSucursalController@storeProveedorSucursal');
            $api->patch('{id}/proveedor', 'App\Http\Controllers\v1\CADECO\ProveedorContratistaSucursalController@updateProveedorSucursal');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\ProveedorContratistaSucursalController@destroy')->where(['id' => '[0-9]+']);
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
            $api->post('/porBase', 'App\Http\Controllers\v1\CADECO\UnidadController@unidadesGlobal');
        });

    });

    /**
     * CHARTS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'chart'], function ($api) {
        $api->get('avance-cuentas-contables', 'App\Http\Controllers\v1\ChartController@avanceCuentasContables');
        $api->get('prepolizas-semanal', 'App\Http\Controllers\v1\ChartController@prepolizasSemanal');
        $api->get('prepolizas-acumulado', 'App\Http\Controllers\v1\ChartController@polizasDoughnut');
        $api->get('pagos-anticipados', 'App\Http\Controllers\v1\ChartController@pagosAnticipados');
    });

    /**
     * REPORTES
     */
    $api->group(['middleware' => 'api', 'prefix' => 'reportes'], function ($api) {
        $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Reportes\ReporteController@paginate');
        $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Reportes\ReporteController@show')->where(['id' => '[0-9]+']);
    });

    /**
     * ACTIVO FIJO
     */

    $api->group(['middleware' => 'api', 'prefix' => 'activo-fijo'], function ($api){
        $api->group(['prefix' => 'resguardo'], function ($api){
            $api->get('lista', 'App\Http\Controllers\v1\ACTIVO_FIJO\ResguardoController@listaResguardos');
            $api->get('getResguardos', 'App\Http\Controllers\v1\ACTIVO_FIJO\ResguardoController@getResguardos');
            $api->get('{id}/pdf', 'App\Http\Controllers\v1\ACTIVO_FIJO\ResguardoController@pdfResguardo');
        });
        $api->group(['prefix' => 'usuario-ubicacion'], function ($api){
            $api->get('listaUbicaciones', 'App\Http\Controllers\v1\ACTIVO_FIJO\UsuarioUbicacionController@listaUbicaciones');
        });

    });

    /**
     * CONTABILIDAD GENERAL
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contabilidad-general'], function ($api) {
        $api->group(['prefix' => 'cuenta'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CTPQ\CuentaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CTPQ\CuentaController@paginate');
            $api->post('asociar', 'App\Http\Controllers\v1\CTPQ\CuentaController@asociarCuenta');
            $api->post('asociar-proveedor', 'App\Http\Controllers\v1\CTPQ\CuentaController@asociarProveedor');
            $api->post('eliminar-asociacion', 'App\Http\Controllers\v1\CTPQ\CuentaController@eliminarAsociacion');
        });
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
            $api->get('{id}/pdf-b', 'App\Http\Controllers\v1\CTPQ\PolizaController@pdfCaidaB')->where(['id' => '[0-9]+']);
            $api->get('descargar-pdf', 'App\Http\Controllers\v1\CTPQ\PolizaController@descargaZip')->where(['id' => '[0-9]+']);
            $api->post('busquedaExcel', 'App\Http\Controllers\v1\CTPQ\PolizaController@busquedaExcel');
            $api->get('descargar-zip', 'App\Http\Controllers\v1\CTPQ\PolizaController@getZip');
            $api->post('actualizar-cfdi', 'App\Http\Controllers\v1\CTPQ\PolizaController@getAsociacionCFDI');
        });
        $api->group(['prefix' => 'poliza-cfdi'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\PolizaCFDIRequeridoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\PolizaCFDIRequeridoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\PolizaCFDIRequeridoController@show')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'proveedor-sat'], function ($api) {
            $api->get('buscarProveedoresSat', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\ProveedorSATController@buscarProveedorAsociar');
        });

        $api->group(['prefix' => 'incidente-poliza'], function ($api) {//buscar-diferencias
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@store');
            $api->post('buscar-diferencias', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@buscarDiferencias');
            $api->post('obtener-informe', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@obtenerInforme');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@update')->where(['id' => '[0-9]+']);
            $api->get('{id}/impresion-polizas', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@impresionPolizas')->where(['id' => '[0-9]+']);
            $api->get('/pdfDiferencias', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaController@pdfDiferencias');
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
            $api->get('/pdfDiferencias', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\ListaEmpresasController@pdfDiferencias');
            $api->post('sincronizar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\ListaEmpresasController@sincronizar');
        });
        $api->group(['prefix' => 'empresa-sat'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\EmpresaSATController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\EmpresaSATController@paginate');
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\EmpresaSATController@update')->where(['id' => '[0-9]+']);
        });

        $api->group(['prefix' => 'cuenta-saldo-negativo'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativoController@paginate');
            $api->post('{id}/obtener-informe', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativoController@obtenerInforme')->where(['id' => '[0-9]+']);
            $api->post('{id}/obtener-informe-movimientos', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativoController@obtenerInformeMovimientos')->where(['id' => '[0-9]+']);
            $api->post('sincronizar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativoController@sincronizar');
        });

        $api->group(['prefix' => 'tipo-poliza'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CTPQ\TipoPolizaController@index');
        });
    });

    /**
     * ENTREGA DE CFDI
     */
    $api->group(['middleware' => 'api', 'prefix' => 'entrega-cfdi'], function ($api) {
        $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIController@index');
        $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIController@paginate');
        $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIController@store');
        $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIController@show')->where(['id' => '[0-9]+']);
        $api->post('{id}/cancelar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIController@cancelar');
        $api->get('{id}/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIController@pdfSolicitudRecepcion')->where(['id' => '[0-9]+']);

        $api->group(['prefix' => 'ctg-tipo-archivo'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\CtgTipoArchivoController@index');
        });

        $api->group(['prefix' => 'ctg-tipo-transaccion'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccionController@index');
        });

        $api->group(['prefix' => 'archivo'], function ($api){
            $api->post('cargarArchivo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@cargarArchivo');
            $api->post('reemplazarArchivo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@reemplazarArchivo');
            $api->post('eliminarArchivo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@eliminarArchivo');
            $api->post('agregarArchivo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@agregarArchivo');
            $api->post('agregarTipoArchivo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@agregarTipoArchivo');
            $api->get('{id}/documento', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@documento')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@destroy')->where(['id' => '[0-9]+']);
            $api->get('{id}/imagenes', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@imagenes')->where(['id' => '[0-9]+']);
            $api->get('/cfdi/{id_cfdi}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion\ArchivoController@getArchivosCFDI')->where(['id_cfdi' => '[0-9]+']);
        });

    });

    /**
     * FISCAL
     */
    $api->group(['middleware' => 'api', 'prefix' => 'fiscal'], function ($api) {
        $api->group(['prefix' => 'cfd-sat'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@paginate');
            $api->post('carga-zip', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@cargaZIP');
            $api->post('procesa-dir-zip-cfd', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@procesaDirectorioZIPCFD');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@update')->where(['id' => '[0-9]+']);
            $api->post('obtener-informe-empresa-mes', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeEmpresaMes');
            $api->post('obtener-informe-completo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeCompleto');
            $api->get('obtener-informe-completo/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeCompletoPDF');
            $api->post('obtener-contenido-directorio', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@getContenidoDirectorio');
            $api->get('descargar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@descargar');
            $api->get('{id}/descargar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@descargarIndividual');
            $api->get('{id}/cfdi-pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@pdfCFDI')->where(['id' => '[0-9]+']);
            $api->post('cargar-xml-proveedor', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@cargaXMLProveedor');
            $api->post('obtener-informe-sat-lp-2020', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeSATLP2020');
            $api->post('{id}/obtener-cuentas-informe-sat-lp-2020', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerCuentasInformeSATLP2020')->where(['id' => '[0-9]+']);
            $api->post('{id}/obtener-movimientos-cuentas-informe-sat-lp-2020', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerMovimientosCuentasInformeSATLP2020')->where(['id' => '[0-9]+']);
            $api->post('{id_proveedor}/obtener-lista-cfdi', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerListaCFDI')->where(['id_proveedor' => '[0-9]+']);
            $api->post('{id_proveedor}/obtener-lista-cfdi-mes-anio', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerListaCFDIMesAnio')->where(['id_proveedor' => '[0-9]+']);
            $api->post('obtener-numero-empresa', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerNumeroEmpresa');
            $api->post('obtener-informe-costos-cfdi-vs-costos-balanza', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeCostosCFDIvsCostosBalanza');
            $api->post('{id_proveedor}/obtener-lista-cfdi-costos-cfdi-costos-balanza', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerListaCFDICostosCFDICostosBalanza')->where(['id_proveedor' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@descargaLayout');
            $api->get('descargar-comunicados', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@descargarComunicados');
            $api->get('informe-rep-pendientes-proveedor/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeREPPDF');
            $api->get('informe-rep-pendientes-proveedor-empresa/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeREPProveedorEmpresaPDF');
            $api->get('informe-rep-pendientes-empresa/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeREPEmpresaPDF');
            $api->get('informe-rep-pendientes-empresa-proveedor/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad\CFDSATController@obtenerInformeREPEmpresaProveedorPDF');
        });
        $api->group(['prefix' => 'autocorreccion'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\AutocorreccionController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\AutocorreccionController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\AutocorreccionController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/aplicar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\AutocorreccionController@aplicar')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'efos'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\EfosController@index');
        });
        $api->group(['prefix' => 'no-deducido'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\NoDeducidoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\NoDeducidoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\NoDeducidoController@show')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'fecha-inhabil-sat'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\FechaInhabilSatController@paginate');
            $api->delete('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\FechaInhabilSatController@delete')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\FechaInhabilSatController@store');
        });
        $api->group(['prefix' => 'tipo-fecha-sat'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\TipoFechaController@index');
        });
        $api->group(['prefix' => 'no-localizado'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\NoLocalizadoController@paginate');
        });
        $api->group(['prefix' => 'ctg-no-localizado'], function ($api){
            $api->post('cargarCsv', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoController@cargarCsv');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoController@paginate');
            $api->post('obtener-informe', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoController@obtenerInforme');
            $api->get('obtener-informe/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoController@obtenerInformePDF');
            $api->get('obtener-informe/empresa-proyecto/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoController@obtenerInformeEmpresaProyectoPDF');
        });
    });

    /**
     * PADRON PROVEEDORES
     */
    $api->group(['middleware' => 'api', 'prefix' => 'padron-proveedores'], function ($api) {
        $api->group(['prefix' => 'archivo'], function ($api){
            $api->post('cargarArchivo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\ArchivoController@cargarArchivo');
            $api->post('cargarArchivoZIP', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\ArchivoController@cargarArchivoZIP');
            $api->get('{id}/documento', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\ArchivoController@documento')->where(['id' => '[0-9]+']);
            $api->get('getArchivosPrestadora', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\ArchivoController@getArchivosPrestadora')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\ArchivoController@destroy')->where(['id' => '[0-9]+']);
            $api->get('{id}/imagenes', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\ArchivoController@imagenes')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'archivo-invitacion'], function ($api){
            $api->get('{id}/documento', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@documentoSC')->where(['id' => '[0-9]+']);
            $api->get('{id}/documento/descargar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoController@descargarSC')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'ctg-area'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\CtgAreaController@index');
        });
        $api->group(['prefix' => 'ctg-seccion'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\CtgSeccionController@index');
        });
        $api->group(['prefix' => 'empresa'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@store');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@show')->where(['id' => '[0-9]+']);
            $api->get('{rfc}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@showRFC');
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@update')->where(['id' => '[0-9]+']);
            $api->post('registrarPrestadora', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@registrarPrestadora');
            $api->patch('{id}/revisarRFC', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@revisarRFC')->where(['id' => '[0-9]+']);
            $api->patch('revisarRFCPreexistente', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@revisarRFCPreexistente')->where(['id' => '[0-9]+']);
            $api->post('revisarRfcPrestadora', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@revisarRfcPrestadora')->where(['id' => '[0-9]+']);
            $api->get('{id}/descargaExpediente', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaController@descargaExpediente')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'empresa-boletinada'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaController@store');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaController@show')->where(['id' => '[0-9]+']);
            //$api->get('{rfc}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaController@showRFC');
            $api->delete('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaController@destroy')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaController@update')->where(['id' => '[0-9]+']);
        });
        $api->group(['prefix' => 'especialidad'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EspecialidadController@store');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EspecialidadController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\EspecialidadController@paginate');
        });
        $api->group(['prefix' => 'giro'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\GiroController@store');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\GiroController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\GiroController@paginate');
        });
        $api->group(['prefix' => 'invitacion'], function ($api){
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@show')->where(['id' => '[0-9]+']);
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@paginate');
            $api->get('/getSolicitudes', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@getPorCotizar');
            $api->get('{id}/getSolicitud', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@getSolicitud')->where(['id' => '[0-9]+']);
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@show')->where(['id' => '[0-9]+']);
            $api->post('abrir/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@abrir')->where(['id' => '[0-9]+']);
            $api->get('{id}/getPresupuestoEdit', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@getPresupuestoEdit')->where(['id' => '[0-9]+']);
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@pdf')->where(['id' => '[0-9]+']);
            $api->get('{id}/tipos-archivo', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@getTiposArchivo');
            $api->post('{id}/cargar-archivos', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores\InvitacionController@cargarArchivos');
        });
    });

    /**
     * PORTAL PROVEEDORES
     */
    $api->group(['middleware' => 'api', 'prefix' => 'portal-proveedor'], function ($api) {
        $api->group(['prefix' => 'solicitud-autorizacion-avance'], function ($api) {
            $api->get('index', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@index');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@store');
            $api->post('{id}/ordenarConceptos', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@proveedorConceptos')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@update')->where(['id' => '[0-9]+']);
            $api->patch('{id}/eliminar', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@destroy')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@pdfSolicitudAvanceFormato')->where(['id' => '[0-9]+']);
            $api->patch('{id}/registrarRetencionIva', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@registrarRetencionIva')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout/{id}', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@descargaLayout')->where(['id' => '[0-9]+']);
            $api->post('layout', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@cargaLayout');
            $api->get('descargaLayoutEdicion/{id}', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@descargaLayoutEdicion')->where(['id' => '[0-9]+']);
            $api->post('layoutEdit', 'App\Http\Controllers\v1\CADECO\PortalProveedor\SolicitudAutorizacionAvanceController@cargaEditarLayout');
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
     * ACARREOS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'acarreos'], function ($api) {
        //CAMIÓN
        $api->group(['prefix' => 'camion'], function ($api) {
            $api->post('/catalogo', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@catalogo');
            $api->post('/cambioClave', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@cambiarClave');
            $api->post('/registrar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@registrar');
            $api->post('/cargaImagenes', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@cargaImagenes');
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@desactivar')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@update')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\ACARREOS\Catalogos\CamionController@descargaLayout');
        });

        //CHECADOR
        $api->group(['prefix' => 'checador'], function ($api) {
            $api->get('getChecadores', 'App\Http\Controllers\v1\ACARREOS\Configuracion\UsuarioProyectoController@getChecadores');
        });

        //EMPRESA
        $api->group(['prefix' => 'empresa'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\EmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\EmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\EmpresaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\EmpresaController@update')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\EmpresaController@store');
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\EmpresaController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\EmpresaController@desactivar')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\ACARREOS\Catalogos\EmpresaController@descargaLayout');
        });

        //IMPRESORA
        $api->group(['prefix' => 'impresora'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@paginate');
        });

        //IMPRESORA
        $api->group(['prefix' => 'impresora'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@desactivar')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@update')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\ACARREOS\Catalogos\ImpresoraController@descargaLayout');
        });

        //MARCA
        $api->group(['prefix' => 'marca'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MarcaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MarcaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MarcaController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MarcaController@store');
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MarcaController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MarcaController@desactivar')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MarcaController@update')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MarcaController@descargaLayout');
        });

        //MATERIAL
        $api->group(['prefix' => 'material'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MaterialController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MaterialController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MaterialController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MaterialController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MaterialController@desactivar')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MaterialController@store');
            $api->get('descargaLayout', 'App\Http\Controllers\v1\ACARREOS\Catalogos\MaterialController@descargaLayout');
        });

        //OPERADOR
        $api->group(['prefix' => 'operador'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OperadorController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OperadorController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OperadorController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OperadorController@store');
            $api->patch('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OperadorController@update')->where(['id' => '[0-9]+']);
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OperadorController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OperadorController@desactivar')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OperadorController@descargaLayout');

        });

        //ORIGEN
        $api->group(['prefix' => 'origen'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OrigenController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OrigenController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OrigenController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OrigenController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OrigenController@desactivar')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OrigenController@update')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\ACARREOS\Catalogos\OrigenController@descargaLayout');
        });

        //SINDICATO
        $api->group(['prefix' => 'sindicato'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\SindicatoController@index');
        });

        //TELEFONO
        $api->group(['prefix' => 'telefono'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TelefonoController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TelefonoController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TelefonoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TelefonoController@update')->where(['id' => '[0-9]+']);
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TelefonoController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TelefonoController@desactivar')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TelefonoController@descargaLayout');
        });

        //TIPOORIGEN
        $api->group(['prefix' => 'tipo-origen'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TipoOrigenController@index');
        });

        //TIRO
        $api->group(['prefix' => 'tiro'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TiroController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TiroController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/asignar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TiroController@asignarConcepto')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TiroController@store');
            $api->get('{id}/activar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TiroController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TiroController@desactivar')->where(['id' => '[0-9]+']);
            $api->get('descargaTiros', 'App\Http\Controllers\v1\ACARREOS\Catalogos\TiroController@descargaTiros');
        });

        //TAG
        $api->group(['prefix' => 'tag'], function ($api) {
            $api->post('/catalogo', 'App\Http\Controllers\v1\ACARREOS\TagController@catalogo');
            $api->post('/registrar', 'App\Http\Controllers\v1\ACARREOS\TagController@registrarTag');
            $api->post('/cambioClave', 'App\Http\Controllers\v1\ACARREOS\TagController@cambiarClave');
        });

        //TAG GLOBAL
        $api->group(['prefix' => 'tag-global'], function ($api) {
            $api->post('/catalogo', 'App\Http\Controllers\v1\ACARREOS\Configuracion\TagController@catalogo');
            $api->post('/registrar', 'App\Http\Controllers\v1\ACARREOS\Configuracion\TagController@registrarTag');
        });

        //VIAJE NETO
        $api->group(['prefix' => 'viaje-neto'], function ($api) {
            $api->post('/catalogo', 'App\Http\Controllers\v1\ACARREOS\ViajeNetoController@catalogo');
            $api->post('/registrar', 'App\Http\Controllers\v1\ACARREOS\ViajeNetoController@registrarViaje');
            $api->post('/cargaImagenesViajes', 'App\Http\Controllers\v1\ACARREOS\ViajeNetoController@cargaImagenesViajes');
            $api->post('/cambioClave', 'App\Http\Controllers\v1\ACARREOS\ViajeNetoController@cambiarClave');
        });
    });

    /**
     * RECEPCION DE CFDI
     */
    $api->group(['middleware' => 'api', 'prefix' => 'recepcion-cfdi'], function ($api) {
        $api->get('/', 'App\Http\Controllers\v1\CADECO\RecepcionSolicitudes\SolicitudRecepcionCFDIController@index');
        $api->get('paginate', 'App\Http\Controllers\v1\CADECO\RecepcionSolicitudes\SolicitudRecepcionCFDIController@paginate');
        $api->post('/', 'App\Http\Controllers\v1\CADECO\RecepcionSolicitudes\SolicitudRecepcionCFDIController@store');
        $api->post('{id}/aprobar', 'App\Http\Controllers\v1\CADECO\RecepcionSolicitudes\SolicitudRecepcionCFDIController@aprobar');
        $api->post('{id}/rechazar', 'App\Http\Controllers\v1\CADECO\RecepcionSolicitudes\SolicitudRecepcionCFDIController@rechazar');
        $api->get('{id}', 'App\Http\Controllers\v1\CADECO\RecepcionSolicitudes\SolicitudRecepcionCFDIController@show')->where(['id' => '[0-9]+']);
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
     * CATALOGOS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'catalogos'], function ($api) {
        //UNIFICACION PROVEEDORES
        $api->group(['prefix' => 'unificacion-proveedores'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Catalogos\UnificacionProveedoresController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Catalogos\UnificacionProveedoresController@store');
        });

        //PROYECTOS
        $api->group(['prefix' => 'proyecto'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\ProyectoController@index');
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
            $api->get('/polizasCFDI', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@getPolizasPorAsociar');
            $api->post('asociar', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@asociarCFDI');
        });

        //PÓLIZAS CFDI
        $api->group(['prefix' => 'poliza-cfdi'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaCFDIRequeridoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaCFDIRequeridoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaCFDIRequeridoController@show')->where(['id' => '[0-9]+']);
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

        //CFDI PÓLIZA
        $api->group(['prefix' => 'cfdi-poliza'], function ($api) {
            $api->get('/cfdi-por-cargar', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@getCFDIPorCargar');
            $api->get('/descargar-cfdi', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@descargarCFDIPorCargar');
            $api->post('/cargar-cfdi-add', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@cargarCFDIADD');
        });
    });

    /**
     * COMPRAS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'compras'], function ($api) {
        // ASIGNACIÓN PROVEEDOR
        $api->group(['prefix' => 'asignacion'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@index');
            $api->get('{id}/getAsignacion', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@getAsignacion');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@paginate');
            $api->post('/','App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@destroy')->where(['id' => '[0-9]+']);
            $api->post('generarOC', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@generarOrdenCompra');
            $api->post('generarOrdenIndividual', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@generarOrdenIndividual');
            $api->get('pendientesOrden', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@pendientesOrden');
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Compras\AsignacionProveedorController@pdf')->where(['id' => '[0-9]+']);
        });

        // ITEM CONTRATISTA
        $api->group(['prefix' => 'item-contratista'], function ($api) {
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Compras\ItemContratistaController@destroy')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Compras\ItemContratistaController@update')->where(['id' => '[0-9]+']);
        });

        // COTIZACIÓN
        $api->group(['prefix' => 'cotizacion'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@paginate');
            $api->get('descargaLayout/{id}', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@descargaLayout')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\Compras\CotizacionController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\Compras\CotizacionController@destroy')->where(['id' => '[0-9]+']);
            $api->post('layout', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@cargaLayout');
            $api->get('{id}/pdf', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@pdf')->where(['id' => '[0-9]+']);
            $api->post('/portal-proveedor','App\Http\Controllers\v1\CADECO\Compras\CotizacionController@storePortalProveedor');
            $api->patch('{id}/portal-proveedor', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@updatePortalProveedor')->where(['id' => '[0-9]+']);
            $api->patch('{id}/portal-proveedor/enviar', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@enviar')->where(['id' => '[0-9]+']);
            $api->get('descargaLayoutProveedor/{id}', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@descargaLayoutProveedor')->where(['id' => '[0-9]+']);
            $api->post('layoutProveedor', 'App\Http\Controllers\v1\CADECO\Compras\CotizacionController@cargaLayoutProveedor');
            $api->delete('{id}/proveedor','App\Http\Controllers\v1\CADECO\Compras\CotizacionController@destroyProveedor')->where(['id' => '[0-9]+']);
        });

        // ORDEN DE COMPRA
        $api->group(['prefix' => 'orden-compra'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato-orden-compra', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@pdfOrdenCompra')->where(['id' => '[0-9]+']);
            $api->post('eliminarOrdenes', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@eliminarOrdenes')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@destroy')->where(['id' => '[0-9]+']);
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
            $api->get('/leerQR', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@leerQR');
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@paginate');
            $api->patch('{id}/aprobar', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@aprobar')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@destroy')->where(['id' => '[0-9]+']);
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@pdfSolicitudCompra')->where(['id' => '[0-9]+']);
            $api->get('{id}/comparativa-cotizaciones', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@getComparativaCotizaciones')->where(['id' => '[0-9]+']);
            $api->get('{id}/comparativa-cotizaciones/pdf', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@pdfComparativaCotizaciones')->where(['id' => '[0-9]+']);
            $api->get('{id}/getCotizaciones', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@getCotizaciones')->where(['id' => '[0-9]+']);
            $api->get('{id}/getCuerpoCorreo','App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@getCuerpoCorreo')->where(['id' => '[0-9]+']);
            $api->get('{id}/descargaLayoutAsignacion', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@descargaLayoutAsignacion')->where(['id' => '[0-9]+']);
            $api->post('cargaLayoutAsignacion', 'App\Http\Controllers\v1\CADECO\Compras\SolicitudCompraController@procesarLayoutAsignacion')->where(['id' => '[0-9]+']);
        });

        $api->group(['prefix' => 'invitacion-cotizar'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Compras\InvitacionController@paginate');
            $api->post('/','App\Http\Controllers\v1\CADECO\Compras\InvitacionController@store');
            $api->post('/contraoferta','App\Http\Controllers\v1\CADECO\Compras\InvitacionController@storeContraoferta');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\InvitacionController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\Compras\InvitacionController@destroy')->where(['id' => '[0-9]+']);
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Compras\InvitacionController@pdf')->where(['id' => '[0-9]+']);
            $api->get('abierto/{id}', 'App\Http\Controllers\v1\CADECO\Compras\InvitacionController@abrir')->where(['id' => '[0-9]+']);
        });

        // CATALOGOS
        $api->group(['prefix' => 'forma-pago-credito'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\FormaPagoCreditoController@index');
        });

    });

    /**
     * CONTRATOS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contratos'], function ($api) {
        /**
         * ASIGNACION DE CONTRATISTAS
         */
        $api->group(['prefix' => 'asignacion-contratista'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\AsignacionContratistaController@paginate');
            $api->get('getAsignaciones', 'App\Http\Controllers\v1\CADECO\Contratos\AsignacionContratistaController@getAsignaciones');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\AsignacionContratistaController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contratos\AsignacionContratistaController@store');
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\Contratos\AsignacionContratistaController@destroy')->where(['id' => '[0-9]+']);
            $api->post('generarSubcontrato', 'App\Http\Controllers\v1\CADECO\Contratos\AsignacionContratistaController@generarSubcontrato');
            $api->get('{id}/pdf', 'App\Http\Controllers\v1\CADECO\Contratos\AsignacionContratistaController@pdf')->where(['id' => '[0-9]+']);
        });

        /**
         * AVANCE SUBCONTRATOS
         */
        $api->group(['prefix' => 'avance-subcontrato'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\AvanceSubcontratoController@paginate');
            $api->post('/','App\Http\Controllers\v1\CADECO\Contratos\AvanceSubcontratoController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\AvanceSubcontratoController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/obtenerAvance', 'App\Http\Controllers\v1\CADECO\Contratos\AvanceSubcontratoController@obtenerAvance')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\AvanceSubcontratoController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\AvanceSubcontratoController@destroy')->where(['id' => '[0-9]+']);

        });

        /**
         * CONTRATO PROYECTADO
         */
        $api->group(['prefix' => 'contrato-proyectado'], function ($api){
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@show')->where(['id' => '[0-9]+']);
            $api->get('getArea', 'App\Http\Controllers\v1\SEGURIDAD_ERP\AreaSubcontratanteController@getArea');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@paginate');
            $api->post('/','App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@store');
            $api->post('layout', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@getLayoutData');
            $api->patch('{id}/actualizar', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@actualiza');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@update');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@destroy')->where(['id' => '[0-9]+']);
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@pdf')->where(['id' => '[0-9]+']);
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@index');
            $api->get('getContratos', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@getContratos');
            $api->get('{id}/getCotizaciones', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@getCotizaciones');
            $api->get('{id}/getCuerpoCorreo','App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@getCuerpoCorreo')->where(['id' => '[0-9]+']);
            $api->get('{id}/comparativa-cotizaciones', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@getComparativaCotizaciones')->where(['id' => '[0-9]+']);
            $api->get('{id}/comparativa-cotizaciones/pdf', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@pdfComparativaCotizaciones')->where(['id' => '[0-9]+']);
            $api->get('{id}/descargaLayoutAsignacion', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@descargaLayoutAsignacion')->where(['id' => '[0-9]+']);
            $api->post('cargalayout','App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@procesaLayoutAsigancion');
            $api->patch('{id}/reclasificacion', 'App\Http\Controllers\v1\CADECO\Contratos\ContratoProyectadoController@reclasificacion');
        });

        /**
         * CONCEPTOS
         */
        $api->group(['prefix' => 'concepto'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\ContratoController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ContratoController@show')->where(['id' => '[0-9]+']);
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
            $api->get('descargaLayout/{id}', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@descargaLayout')->where(['id' => '[0-9]+']);
            $api->post('layout', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@cargaLayout');
            $api->get('descargaLayoutEdicion/{id}', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@descargaLayoutEdicion')->where(['id' => '[0-9]+']);
            $api->post('layoutEdit', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@cargaLayoutEdicion');

            /**
             * FORMATO ORDEN DE PAGO DE ESTIMACION
             */
            $api->get('{id}/formato-orden-pago', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@pdfOrdenPago')->where(['id' => '[0-9]+']);
        });

        /**
         * PRESUPUESTO
         */
        $api->group(['prefix' => 'presupuesto'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@destroy')->where(['id' => '[0-9]+']);
            $api->post('/','App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@store');
            $api->get('descargaLayout/{id}', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@descargaLayout')->where(['id' => '[0-9]+']);
            $api->post('layout', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@cargaLayout');
            $api->get('{id}/pdf', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@pdf')->where(['id' => '[0-9]+']);
            $api->post('/portal-proveedor','App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@registrarPresupuestoProveedor');
            $api->patch('{id}/portal-proveedor', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@updatePortalProveedor')->where(['id' => '[0-9]+']);
            $api->get('descargaLayoutProveedor/{id}', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@descargaLayoutProveedor')->where(['id' => '[0-9]+']);
            $api->post('layoutProveedor', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@cargaLayoutProveedor');
            $api->delete('{id}/proveedor', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@destroyProveedor')->where(['id' => '[0-9]+']);
            $api->patch('{id}/portal-proveedor/enviar', 'App\Http\Controllers\v1\CADECO\Contratos\PresupuestoContratistaController@enviar')->where(['id' => '[0-9]+']);
        });


        /**
         * SUBCONTRATO
         */
        $api->group(['prefix' => 'subcontrato'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/ordenarConceptos', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@ordenarConceptos')->where(['id' => '[0-9]+']);
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@paginate');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@updateContrato')->where(['id' => '[0-9]+']);
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@destroy')->where(['id' => '[0-9]+']);
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@pdf')->where(['id' => '[0-9]+']);
            $api->get('{id_subcontrato}/descargar-layout-cambios-precio-volumen', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@descargarLayoutCambiosPrecioVolumen')->where(['id' => '[0-9]+']);
            $api->get('/proveedor', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@indexSinContexto');
            $api->patch('{id}/sinContexto', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@showSinContexto')->where(['id' => '[0-9]+']);
            $api->patch('{id}/proveedorConceptos', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@ordenarConceptosProveedor')->where(['id' => '[0-9]+']);
            $api->get('{id}/ordenarConceptosAvance', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@ordenarConceptosAvance')->where(['id' => '[0-9]+']);
        });

        /**
         * SOLICITUD DE CAMBIO
         */

        $api->group(['prefix' => 'solicitud-cambio'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@registrar');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/aplicar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@aplicar')->where(['id' => '[0-9]+']);
            $api->patch('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@cancelar')->where(['id' => '[0-9]+']);
            $api->patch('{id}/rechazar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@rechazar')->where(['id' => '[0-9]+']);
            $api->patch('{id}/revertirAprobacion', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@revertirAprobacion')->where(['id' => '[0-9]+']);
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@paginate');
            $api->get('{id}/formato', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@pdf')->where(['id' => '[0-9]+']);
            $api->post('procesar-layout-extraordinarios', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@procesarLayoutExtraordinarios');
            $api->post('procesar-layout-cambio-precio-volumen', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudCambioController@procesarLayoutCambioPrecioVolumen');
        });

        /**
         * TIPOS CONTRATOS
         */
        $api->group(['prefix' => 'tipo-contrato'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contratos\TipoContratoController@index');
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

        $api->group(['prefix' => 'invitacion-cotizar'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\InvitacionController@paginate');
            $api->post('/','App\Http\Controllers\v1\CADECO\Contratos\InvitacionController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\InvitacionController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\Contratos\InvitacionController@destroy')->where(['id' => '[0-9]+']);
            $api->get('pdf/{id}', 'App\Http\Controllers\v1\CADECO\Contratos\InvitacionController@pdf')->where(['id' => '[0-9]+']);
            $api->get('abierto/{id}', 'App\Http\Controllers\v1\CADECO\Contratos\InvitacionController@abrir')->where(['id' => '[0-9]+']);
            $api->post('/contraoferta','App\Http\Controllers\v1\CADECO\Contratos\InvitacionController@storeContraoferta');
            $api->get('/tipos-archivo', 'App\Http\Controllers\v1\CADECO\Contratos\InvitacionController@getTiposArchivo');
        });
    });

    /**
     * CONTROL DE OBRA
     */
    $api->group(['middleware' => 'api', 'prefix' => 'control-obra'], function ($api) {
        /**
         * AVANCE OBRA
         **/
        $api->group(['prefix' => 'avance'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ControlObra\AvanceObraController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\ControlObra\AvanceObraController@store');
            $api->patch('{id}/editar', 'App\Http\Controllers\v1\CADECO\ControlObra\AvanceObraController@update')->where(['id' => '[0-9]+']);
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ControlObra\AvanceObraController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/aprobar', 'App\Http\Controllers\v1\CADECO\ControlObra\AvanceObraController@aprobar')->where(['id' => '[0-9]+']);
            $api->patch('{id}/revertir', 'App\Http\Controllers\v1\CADECO\ControlObra\AvanceObraController@revertir')->where(['id' => '[0-9]+']);
            $api->delete('{id}','App\Http\Controllers\v1\CADECO\ControlObra\AvanceObraController@destroy')->where(['id' => '[0-9]+']);
        });
    });


    /**
     * CONTROL DE CAMBIOS AL PRESUPUESTO
     */
    $api->group(['middleware' => 'api', 'prefix' => 'control-presupuesto'], function ($api){

        // TIPOS ORDENES
        $api->group(['prefix' => 'tipo-orden'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\TipoOrdenController@index');
        });

        // TARJETAS
        $api->group(['prefix' => 'tarjeta'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\TarjetaController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\TarjetaController@show')->where(['id' => '[0-9]+']);
        });

        // CONCEPTOS TARJETAS
        $api->group(['prefix' => 'concepto-tarjeta'], function ($api){
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\ConceptoTarjetaController@conceptosTarjeta')->where(['id' => '[0-9]+']);
        });

        // SOLICITUD DE CAMBIO
        $api->group(['prefix' => 'solicitud-cambio'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\SolicitudCambioController@paginate');
        });

        // VARIACIÓN DE VOLUMEN
        $api->group(['prefix' => 'variacion-volumen'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\VariacionVolumenController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\VariacionVolumenController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\VariacionVolumenController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\VariacionVolumenController@destroy')->where(['id' => '[0-9]+']);
            $api->post('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\VariacionVolumenController@autorizar')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato-variacion-volumen', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\VariacionVolumenController@pdfVariacionVolumen')->where(['id' => '[0-9]+']);
        });

        // EXTRAORDINARIO
        $api->group(['prefix' => 'extraordinario'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\ExtraordinarioController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\ExtraordinarioController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\ExtraordinarioController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\ExtraordinarioController@destroy')->where(['id' => '[0-9]+']);
            $api->post('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\ExtraordinarioController@autorizar')->where(['id' => '[0-9]+']);
            $api->get('{id}/formato', 'App\Http\Controllers\v1\CADECO\ControlPresupuesto\ExtraordinarioController@pdf')->where(['id' => '[0-9]+']);
        });

    });

    /**
     * FINANZAS GENERAL
     */
    $api->group(['middleware' => 'api', 'prefix' => 'finanzas-general'], function ($api) {
        $api->group(['prefix' => 'solicitud-pago-aplicada'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaController@paginate');
            $api->get('indicador-aplicadas', 'App\Http\Controllers\v1\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaController@getIndicadorAplicadas');
            $api->get('descarga-excel', 'App\Http\Controllers\v1\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaController@descargarExcel');
        });

        $api->group(['prefix' => 'solicitud-pago'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@paginate');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@index');
            $api->patch('{id}/rechazar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@rechazar')->where(['id' => '[0-9]+']);
            $api->get('{id}/autorizar', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@autorizar')->where(['id' => '[0-9]+']);
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController@show')->where(['id' => '[0-9]+']);
        });

    });

    /**
     * FINANZAS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'finanzas'], function ($api) {

        /**
         * COMPROBANTE FONDO
         */
        $api->group(['prefix' => 'comprobante-fondo'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\ComprobanteFondoController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\ComprobanteFondoController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\ComprobanteFondoController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\ComprobanteFondoController@destroy')->where(['id' => '[0-9]+']);
        });

        /**
         * CUENTA BANCARIA EMPRESA
         */
        $api->group(['prefix' => 'cuenta-bancaria-empresa'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\CuentaBancariaEmpresaController@show')->where(['id' => '[0-9]+']);
        });

        /**
         * DATOS ESTIMACIONES
         */
        $api->group(['prefix' => 'estimacion'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\ConfiguracionEstimacionController@store');
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\ConfiguracionEstimacionController@index');
            $api->post('/proveedor', 'App\Http\Controllers\v1\CADECO\Finanzas\ConfiguracionEstimacionController@indexSinContexto');
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
            $api->get('{id}/cfdi-pdf', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@pdfCFDI')->where(['id' => '[0-9]+']);
            $api->get('{id}/getDocumentos', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@getDocumentos')->where(['id' => '[0-9]+']);
            $api->post('/storeRevision', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@storeRevision');
            $api->post('/storeRevisionVarios', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@storeRevisionVarios');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@update')->where(['id' => '[0-9]+']);
            $api->get('/leerQR', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@leerQR');
            $api->get('{id}/aplicacionManual', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@aplicacionManual');


            /**
             * FORMATO DE CONTRARECIBO
             */
            $api->get('{id}/formato-cr', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@pdfCR')->where(['id' => '[0-9]+']);

            /**
             * FORMATO PDF FACTURA DE VARIOS
             */
            $api->get('{id}/formato-fv', 'App\Http\Controllers\v1\CADECO\Finanzas\FacturaController@pdfFV')->where(['id' => '[0-9]+']);
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
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@destroy')->where(['id' => '[0-9]+']);
            $api->post('/aplicarPago', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@aplicarPago');
            $api->get('/documentosParaPagar', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@documentosParaPagar');
            $api->get('{id}/documentoParaPagar', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@documentoParaPagar')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@store');
            $api->post('/porConciliar', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@porConciliar');
            $api->post('/conciliar', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@conciliar');
            $api->post('/totalesConciliar', 'App\Http\Controllers\v1\CADECO\Finanzas\PagoController@totalesConciliar');

            $api->group(['prefix' => 'carga-masiva'], function ($api) {
                $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@paginate');
                $api->post('layout', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@procesaLayoutPagos');
                $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@store');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@show')->where(['id' => '[0-9]+']);
                $api->get('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@autorizar')->where(['id' => '[0-9]+']);
                $api->get('descarga-layout', 'App\Http\Controllers\v1\CADECO\Finanzas\CargaLayoutPagoController@descargarLayout');
            });
        });

        // RUBROS
        $api->group(['prefix' => 'rubro'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\RubroController@index');
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
            $api->get('indicador-aplicadas', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@getIndicadorAplicadas');
            $api->get('indicador-aplicadas-general', 'App\Http\Controllers\v1\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaController@getIndicadorAplicadas');
            $api->patch('{id}/solicitar-autorizacion', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@solicitarAutorizacion')->where(['id' => '[0-9]+']);
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

        //CFDSAT

        $api->group(['prefix' => 'cfd-sat'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Finanzas\CFDSATController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\CFDSATController@paginate');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\CFDSATController@update')->where(['id' => '[0-9]+']);
            $api->get('descargar', 'App\Http\Controllers\v1\CADECO\Finanzas\CFDSATController@descargar');
            $api->get('{id}/cfdi-pdf', 'App\Http\Controllers\v1\CADECO\Finanzas\CFDSATController@pdfCFDI')->where(['id' => '[0-9]+']);
            $api->get('descargaLayout', 'App\Http\Controllers\v1\CADECO\Finanzas\CFDSATController@descargaLayout');
            $api->post('cargar-xml-comprobacion', 'App\Http\Controllers\v1\CADECO\Finanzas\CFDSATController@cargaXMLComprobacion');
            $api->get('cfdi-rep-pendiente-xls', 'App\Http\Controllers\v1\CADECO\Finanzas\CFDSATController@descargaCFDIREPPendienteXLS');

        });
    });

    /**
     * PRESUPUESTO
     */
    $api->group(['middleware' => 'api', 'prefix' => 'presupuesto'], function ($api) {
        //CONCEPTO
        $api->group(['prefix' => 'concepto'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/editar', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@show')->where(['id' => '[0-9]+']);
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@list');
            $api->get('/{id}/hijos', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@list')->where(['id' => '[0-9]+']);;
            $api->get('{id}/activar', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@activar')->where(['id' => '[0-9]+']);
            $api->get('{id}/desactivar', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@desactivar')->where(['id' => '[0-9]+']);
            $api->patch('actualiza-claves', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@actualizarClaves')->where(['id' => '[0-9]+']);
            $api->patch('actualiza-clave', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@actualizarClave')->where(['id' => '[0-9]+']);
            $api->patch('{id}/actualiza-datos-seguimiento', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@actualizaDatosSeguimiento')->where(['id' => '[0-9]+']);
            $api->patch('{id}/toggle-activo', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@toggleActivo')->where(['id' => '[0-9]+']);
            $api->delete('/responsable/{id}', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@eliminaResponsable')->where(['id' => '[0-9]+']);
            $api->post('/responsable', 'App\Http\Controllers\v1\CADECO\Presupuesto\ConceptoController@storeResponsable');
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
     * SEGUIMIENTO
     */
    $api->group(['middleware' => 'api', 'prefix' => 'seguimiento'], function ($api){

        $api->group(['prefix'=>'cliente'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\ClienteController@index');
        });

        $api->group(['prefix'=>'empresa'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\EmpresaController@index');
        });

        $api->group(['prefix'=>'factura'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\FacturaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\FacturaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/cancelar', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\FacturaController@cancelar')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\FacturaController@store');
            $api->post('CFDI', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\FacturaController@cargarArchivo');
        });

        $api->group(['prefix'=>'ingreso-partida'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\IngresoPartidaController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\IngresoPartidaController@show')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\IngresoPartidaController@store');
        });

        $api->group(['prefix'=>'moneda'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\MonedaController@index');
        });

        $api->group(['prefix'=>'proyecto'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\ProyectoController@index');
        });

        $api->group(['prefix'=>'tipo-ingreso'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\TipoIngresoController@index');
            $api->post('/', 'App\Http\Controllers\v1\SEGUIMIENTO\Finanzas\TipoIngresoController@store');
        });
    });

    /**
     * SEGURIDAD ERP
     */
    $api->group(['middleware' => 'api', 'prefix' => 'SEGURIDAD_ERP'], function ($api) {

        $api->group(['prefix' => 'configuracion-obra'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\ConfiguracionObraController@index');
            $api->get('contexto', 'App\Http\Controllers\v1\SEGURIDAD_ERP\ConfiguracionObraController@contexto');
            $api->get('/configuracion', 'App\Http\Controllers\v1\SEGURIDAD_ERP\ConfiguracionObraController@configuracion');
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
            $api->get('aviso/{id}/leer', 'App\Http\Controllers\v1\SEGURIDAD_ERP\SistemaController@leerAviso')->where(['user_id' => '[0-9]+']);
            $api->get('aviso/{ruta}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\SistemaController@getAviso')->where(['user_id' => '[0-9]+']);
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
            $api->post('calcular-fechas-limite', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\EfosController@calcularFechasLimite');
            $api->post('obtener-informe', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@obtenerInforme');
            $api->post('obtener-informe-desglosado', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@obtenerInformeDesglosado');
            $api->get('obtener-informe/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@obtenerInformePDF');
            $api->get('obtener-informe-desglosado/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@obtenerInformeDesglosadoPDF');
            $api->get('obtener-informe-cfdi-desglosado', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@descargaInformeCFDIDesglosado');
            $api->get('obtener-informe-definitivos/pdf', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@obtenerInformeDefinitivoPDF');
            $api->get('ultimos-cambios-efos-txt', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@getUltimosCambiosEFOSTXT');
            $api->get('ultimas-listas-txt', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@getUltimasListasEFOSTXT');
            $api->get('correcciones-pendientes-txt', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@getCorreccionesPendientesTXT');
            $api->get('en-aclaracion-txt', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@getEFOSEnAclaracionTXT');
            $api->get('ultimas-correcciones-txt', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\CtgEfosController@getUltimasCorreccionesTXT');
        });
        $api->group(['prefix' => 'transaccion-efo'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\TransaccionesEfosController@paginate');
            $api->get('descarga-csv', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\TransaccionesEfosController@descargarCSV');
        });


        $api->group(['prefix' => 'incidencia'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\SEGURIDAD_ERP\ControlInterno\IncidenciaController@paginate');
        });

        $api->group(['prefix' => 'empresa-facturera'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\EmpresaFactureraController@index');
            $api->post('/buscar-coincidencias', 'App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal\EmpresaFactureraController@buscarCoincidencias');
        });
    });

    /**
     * SUBCONTRATOS ESTIMACIONES
     */
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

    /**
     * Ventas
     */
    $api->group(['middleware' => 'api', 'prefix' => 'ventas'], function ($api){

        $api->group(['prefix'=>'venta'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@show')->where(['id' => '[0-9]+']);
            $api->get('{id}/pdf_venta', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@pdfVenta')->where(['id' => '[0-9]+']);
            $api->get('{id}/pdf_factura', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@pdfFactura')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@destroy')->where(['id' => '[0-9]+']);
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Ventas\VentaController@store');
        });
    });

    /**
     * IGH
     */
    $api->group(['middleware' => 'api', 'prefix' => 'IGH'], function ($api) {
        $api->group(['prefix' => 'usuario'], function ($api) {
            $api->get('currentUser', 'App\Http\Controllers\v1\IGH\UsuarioController@currentUser');
            $api->get('/', 'App\Http\Controllers\v1\IGH\UsuarioController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\IGH\UsuarioController@show')->where(['id' => '[0-9]+']);
            $api->get('/por-correo/{correo}', 'App\Http\Controllers\v1\IGH\UsuarioController@buscaUsuarioEmpresaPorCorreo');
            $api->post('/por-correos', 'App\Http\Controllers\v1\IGH\UsuarioController@buscaUsuariosEmpresaPorCorreo');
        });

        $api->group(['prefix' => 'menu'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\IGH\MenuController@index');
        });

        $api->group(['prefix' => 'tipo-cambio'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\IGH\TipoCambioController@index');
        });

        $api->group(['prefix' => 'ubicacion'], function ($api){
            $api->get('/', 'App\Http\Controllers\v1\IGH\UbicacionController@index');
        });
    });

    /**
     * SCI
     */
    $api->group(['middleware'=>'api', 'prefix'=> 'SCI'], function ($api){

        $api->group(['prefix' => 'marca'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SCI\MarcaController@index');
        });

        $api->group(['prefix' => 'modelo'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\SCI\ModeloController@index');
        });
    });

    /**
     * REMESAS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'remesas'], function ($api) {
        $api->group(['prefix' => 'documento-no-localizado'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\MODULOSSAO\DocumentoDeNoLocalizadoController@paginate');
            $api->get('/', 'App\Http\Controllers\v1\MODULOSSAO\DocumentoDeNoLocalizadoController@index');
            $api->patch('{id}/rechazar', 'App\Http\Controllers\v1\MODULOSSAO\DocumentoDeNoLocalizadoController@rechazar')->where(['id' => '[0-9]+']);
            $api->get('{id}/autorizar', 'App\Http\Controllers\v1\MODULOSSAO\DocumentoDeNoLocalizadoController@autorizar')->where(['id' => '[0-9]+']);
            $api->get('{id}', 'App\Http\Controllers\v1\MODULOSSAO\DocumentoDeNoLocalizadoController@show')->where(['id' => '[0-9]+']);
        });

        $api->group(['prefix' => 'folio'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\MODULOSSAO\RemesaFolioController@paginate');
            $api->get('', 'App\Http\Controllers\v1\MODULOSSAO\RemesaFolioController@show');
            $api->patch('', 'App\Http\Controllers\v1\MODULOSSAO\RemesaFolioController@update');
        });

        $api->group(['prefix' => 'proyecto'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\MODULOSSAO\ProyectoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\MODULOSSAO\ProyectoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\MODULOSSAO\ProyectoController@update')->where(['id' => '[0-9]+']);
        });
    });
});
