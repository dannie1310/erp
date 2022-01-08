<?php


namespace App\Services\CADECO\Compras;


use App\Facades\Context;
use App\Utils\ValidacionSistema;
use App\Imports\CotizacionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CADECO\SolicitudCompra;
use App\CSV\Compras\AsignacionProveedorLayout;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\CotizacionCompraPartida;
use App\Models\CADECO\ItemSolicitudCompra;
use App\PDF\CADECO\Compras\SolicitudCompraFormato;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
use App\Repositories\CADECO\Compras\Solicitud\Repository;


class SolicitudCompraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCompraService constructor.
     * @param SolicitudCompra $model
     */
    public function __construct(SolicitudCompra $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $solicitudes = $this->repository;

        if(isset($data['numero_folio']))
        {
            $solicitudes = $solicitudes->where([['numero_folio', '=', $data['numero_folio']]]);
        }

        if (isset($data['fecha_registro'])) {
            $solicitudes->whereBetween( ['FechaHoraRegistro', [ request( 'fecha_registro' )." 00:00:00",request( 'fecha_registro' )." 23:59:59"]] );
        }

        if (isset($data['fecha'])) {
            $solicitudes->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if (isset($data['estado'])) {
            $solicitudes = $solicitudes
                ->join("Compras.solicitud_complemento", "transacciones.id_transaccion","=","solicitud_complemento.id_transaccion")
                ->join("Compras.ctg_estados_solicitud", "solicitud_complemento.estado","=","ctg_estados_solicitud.id")
                ->where([['ctg_estados_solicitud.descripcion', 'LIKE', '%'.$data['estado'].'%']]);

        }

        if(isset($data['observaciones']))
        {
            $solicitudes = $solicitudes->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }

        if(isset($data['concepto']))
        {
            $solicitudes = $solicitudes
                ->join("Compras.solicitud_complemento", "transacciones.id_transaccion","=","solicitud_complemento.id_transaccion")
                ->where([['solicitud_complemento.concepto', 'LIKE', '%'.$data['concepto'].'%']]);
        }

        if(isset($data['numero_folio_compuesto']))
        {
            $solicitudes = $solicitudes
                ->join("Compras.solicitud_complemento", "transacciones.id_transaccion","=","solicitud_complemento.id_transaccion")
                ->where([['solicitud_complemento.folio_compuesto', 'LIKE', '%'.$data['numero_folio_compuesto'].'%']]);
        }

        return $solicitudes->paginate($data);

    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function store($data)
    {
        try {
            /*Validación de Partidas*/
            foreach ($data['partidas'] as $key => $item){
                if( $this->validarPartidas($data['partidas'], $item, $key)){
                    abort(400, 'No esta permitido incluir más de una vez un insumo en una solicitud '.strval($item['material']['descripcion']).'.');
                }
            }
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function validarPartidas($items, $item, $i)
    {
        foreach ($items as $key => $value) {
            if ($key != $i) {
                if ($value['id_material'] === $item['id_material']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function delete($data, $id)
    {
        return $this->repository->show($id)->eliminar($data['data']);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function aprobar($data, $id)
    {
        return $this->repository->aprobar($data, $id);
    }

    public function update(array $data, $id)
    {
        try {
            /*Validación de Partidas al Actualizar Solicitud*/
            foreach ($data['partidas']['data'] as $key => $item){
                if( $this->validarPartidasActualizacion($data['partidas']['data'], $item, $key)){
                    abort(400, 'No esta permitido incluir más de una vez un insumo en una solicitud '.strval($item['material']['descripcion']).'.');
                }
            }
            return $this->repository->update($data, $id);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function validarPartidasActualizacion($items, $item, $i)
    {
        foreach ($items as $key => $value) {
            if ($key != $i) {
                if ($value['material']['id'] === $item['material']['id']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function pdfSolicitudCompra($id)
    {
        return $this->repository->show($id)->pdfSolicitudCompra();
    }

    public function pdfComparativaCotizaciones($id)
    {
        $cotizacion = $this->repository->show($id)->cotizaciones()->first();
        $pdf = new CotizacionTablaComparativaFormato($cotizacion);
        return $pdf;
    }

    public function getCuerpoCorreo($id)
    {
        return $this->repository->show($id)->getCuerpoCorreoInvitacion();
    }

    public function getComparativaCotizaciones($data,$id)
    {
        //$cotizacion = $this->repository->show($id)->cotizaciones()->first();
        return $this->repository->show($id)->datosComparativos($data);
    }

    public function getCotizaciones($id){
        $precios = [];
        $items = array();
        $cotizaciones = array();
        $solicitud = $this->repository->show($id);
        $solicitud_partidas = $solicitud->partidas;
        $solicitud_cotizaciones = $solicitud->cotizaciones;
        foreach($solicitud_partidas as $i => $partida){
            $cantidad_asig_previamente = $partida->asignaciones()->sum('cantidad_asignada');
            $items[$i] = [
                'id_item' => $partida->id_item,
                'id_material' => $partida->id_material,
                'descripcion' => $partida->material->descripcion,
                'descripcion_corta' => ucfirst((mb_substr($partida->material->descripcion,0,35,'UTF-8'))),
                'unidad' => $partida->material->unidad,
                'cantidad_solicitada' => number_format($partida->cantidad, 4, '.', ''),
                'cantidad_asignada' => number_format($cantidad_asig_previamente, 4, '.', ''),
                'cantidad_disponible' => number_format($partida->cantidad - $cantidad_asig_previamente, 4, '.', ''),
                'cantidad_base' => number_format($partida->cantidad - $cantidad_asig_previamente, 4, '.', ''),
                'item_pendiente' => $partida->cantidad - $cantidad_asig_previamente > 0?true:false,
            ];
            foreach($solicitud_cotizaciones as $cotizacion){
                if(!$cotizacion->id_empresa)continue;

                foreach ($cotizacion->partidas as $p) {
                    if (key_exists($p->id_material, $precios)) {
                        if($p->precio_unitario_compuesto > 0 && $precios[$p->id_material] > $p->precio_unitario_compuesto)
                            $precios[$p->id_material] = (float) $p->precio_unitario_compuesto;
                            $importes[$p->id_material] =  $precios[$p->id_material] * $p->cantidad;
                    } else {
                        if($p->precio_unitario_compuesto > 0) {
                            $precios[$p->id_material] = (float) $p->precio_unitario_compuesto;
                            $importes[$p->id_material] = $precios[$p->id_material]  * $p->cantidad;
                        }
                    }
                }

                if(!array_key_exists($cotizacion->id_transaccion, $cotizaciones)){
                    $cotizaciones[$cotizacion->id_transaccion] = [
                        'id_transaccion' => $cotizacion->id_transaccion,
                        'rfc' => $cotizacion->empresa->rfc,
                        'razon_social' => $cotizacion->empresa->razon_social,
                        'sucursal' => $cotizacion->sucursal->descripcion,
                        'direccion' => $cotizacion->sucursal->direccion,
                        'folio_format' => $cotizacion->numero_folio_format,
                        'justificar' => false,
                    ];
                    $cotizaciones[$cotizacion->id_transaccion]['partidas'] = array();
                }
                array_key_exists($cotizacion->id_transaccion, $cotizaciones)?'': $cotizaciones[$cotizacion->id_transaccion] = array();
                $cot = CotizacionCompraPartida::where('id_transaccion', '=', $cotizacion->id_transaccion)->where('id_material', '=', $partida->id_material)->first();
                if($cot && $cot->precio_unitario > 0){
                    $descuento = $cot->descuento?number_format($cot->descuento, 2, '.', ','):0;
                    $importe = $partida->cantidad * $cot->precio_unitario - ($partida->cantidad * $cot->precio_unitario * $descuento / 100);
                    $t_cambio = $cot->moneda->tipo == 1?1: number_format($cot->moneda->cambio->cambio, 4, '.', '');
                    $cotizaciones[$cotizacion->id_transaccion]['partidas'][$i] = [
                        'id_material' => $cot->id_material,
                        'id_item' => $partida->id_item,
                        'id_transaccion' => $cot->id_transaccion,
                        'cantidad_asignada' => '',
                        'precio_unitario' => $cot->precio_unitario,
                        'precio_unitario_format' => '$' . number_format($cot->precio_unitario, 2, '.', ','),
                        'precio_con_descuento' => $cot->precio_compuesto,
                        'precio_con_descuento_mn' => $cot->precio_compuesto * $cot->tipo_cambio,
                        'precio_unitario_compuesto' => $cot->precio_unitario_compuesto,
                        'moneda' => $cot->moneda->abreviatura,
                        'tipo_cambio' => $t_cambio,
                        'importe' => number_format($importe, 2, '.', ','),
                        'importe_total_moneda' => $cot->total_precio_descuento_partida_moneda_comparativa,
                        'importe_moneda_conversion' =>  number_format($importe * $t_cambio, 2, '.', ','),
                        'importe_moneda_conversion_sf' =>  $importe * $t_cambio,
                        'descuento' => $descuento,
                        'descuento_format' => $cot->partida && $cot->partida->descuento_partida>0? number_format($cot->partida->descuento_partida,2,".",",")."%" : '-',
                        'mejor_opcion' => $cot->mejor_opcion,
                        'color' => $cot->color_opcion,
                        'justificacion' => '',
                    ];
                }else{
                    $cotizaciones[$cotizacion->id_transaccion]['partidas'][$i] = null;
                }
            }
        }
        return ['items'=>$items,'cotizaciones'=> $cotizaciones, 'precios_menores' => $precios];
    }

    public function leerQR($data)
    {
        $verifica = new ValidacionSistema();

        $datos = $verifica->desencripta($data);
        $json = json_decode($datos);

        if($json) {
            return $json->titulo . "_" . $json->obra;
        }else{
            return "Error de lectura";
        }
    }
    
    public function getLayoutAsignacion($id){
        $cotizaciones = $this->getCotizaciones($id);
        $file_name = $this->show($id)->numero_folio_format.'.xlsx';
        return Excel::download(new AsignacionProveedorLayout($cotizaciones, $id),$file_name);
    }

    public function procesarLayoutAsignacion($data){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $items = array();
        $cotizaciones = array();
        $precios = [];
        $partidas_no_validas = false;
        $file_xls = $this->getFileXls($data['name'], $data['file']);
        $celdas = $this->getDatosAsignacionLayout($file_xls);
        $this->verifica = new ValidacionSistema();

        $cadena_validacion = $this->verifica->desencripta($celdas[0][0]);
        $cadena_validacion_exp = explode("|", $cadena_validacion);

        $base_datos = $cadena_validacion_exp[0];
        $id_obra = $cadena_validacion_exp[1];
        $id_validar = $cadena_validacion_exp[2];

        if ($base_datos != Context::getDatabase() || $id_obra != Context::getIdObra() || $data['id'] != $id_validar)
        {
            abort(400, 'El archivo  XLS no corresponde al contrato proyectado');
        }

        $cant_cot =(count($celdas[0]) - 6) / 6;
        $cant_cotizaciones = [];
        for($i = 4; $i < count($celdas);$i++){
            $id_item = $this->verifica->desencripta($celdas[$i][0]);
            $partida = ItemSolicitudCompra::where('id_item', '=', $id_item)->first();
            $cantidad_pendiente = 0;
            $cantidad_asig_previamente = $partida->asignaciones()->sum('cantidad_asignada');
            $items[$i] = [
                'id_item' => $partida->id_item,
                'id_material' => $partida->id_material,
                'descripcion' => $partida->material->descripcion,
                'descripcion_corta' => ucfirst((mb_substr($partida->material->descripcion,0,35,'UTF-8'))),
                'unidad' => $partida->material->unidad,
                'cantidad_solicitada' => number_format($partida->cantidad, 4, '.', ''),
                'cantidad_asignada' => number_format($cantidad_asig_previamente, 4, '.', ''),
                'cantidad_disponible' => number_format($partida->cantidad - $cantidad_asig_previamente, 4, '.', ''),
                'cantidad_base' => number_format($partida->cantidad - $cantidad_asig_previamente, 4, '.', ''),
                'item_pendiente' => $partida->cantidad - $cantidad_asig_previamente > 0?true:false,
                'cotizado' => false,
                'asignadas_mayor_disponible' => false,
            ];
            $indx_id_cotizacion = 6;
            $cant_asignada = 0;
            for($j = 0; $j < $cant_cot; $j++){
                $id_cotizacion = $this->verifica->desencripta($celdas[2][$indx_id_cotizacion]);
                $cotizacion = CotizacionCompra::where('id_transaccion', '=',$id_cotizacion)->first();
                if(!array_key_exists($cotizacion->id_transaccion, $cotizaciones)){
                    $cotizaciones[$cotizacion->id_transaccion] = [
                        'id_transaccion' => $cotizacion->id_transaccion,
                        'rfc' => $cotizacion->empresa->rfc,
                        'razon_social' => $cotizacion->empresa->razon_social,
                        'sucursal' => $cotizacion->sucursal->descripcion,
                        'direccion' => $cotizacion->sucursal->direccion,
                        'folio_format' => $cotizacion->numero_folio_format,
                        'justificar' => false,
                        'partidas_no_validas' => false,
                        'partidas_asignadas' => false,
                    ];
                    $cotizaciones[$cotizacion->id_transaccion]['partidas'] = array();
                }

                $partida_presupuestada = CotizacionCompraPartida::where('id_transaccion', '=',$cotizacion->id_transaccion)->where('id_material', '=', $partida->id_material)->first();
                if (key_exists($partida_presupuestada->id_material, $precios)) {
                    if($partida_presupuestada->precio_unitario_compuesto > 0 && $precios[$partida_presupuestada->id_material] > $partida_presupuestada->precio_unitario_compuesto)
                        $precios[$partida_presupuestada->id_material] = (float) $partida_presupuestada->precio_unitario_compuesto;
                        $importes[$partida_presupuestada->id_material] =  $precios[$partida_presupuestada->id_material] * $partida_presupuestada->cantidad;
                } else {
                    if($partida_presupuestada->precio_unitario_compuesto > 0) {
                        $precios[$partida_presupuestada->id_material] = (float) $partida_presupuestada->precio_unitario_compuesto;
                        $importes[$partida_presupuestada->id_material] = $precios[$partida_presupuestada->id_material]  * $partida_presupuestada->cantidad;
                    }
                }
                if($celdas[$i][$indx_id_cotizacion] != null){
                    $c_asig = 0;
                    $c_valida = true;
                    if(is_numeric($celdas[$i][$indx_id_cotizacion+5]) && $celdas[$i][$indx_id_cotizacion+5] > 0){
                        $c_asig = (float)$celdas[$i][$indx_id_cotizacion+5];
                        $cant_asignada += (float)$celdas[$i][$indx_id_cotizacion+5];
                    }else if(!is_numeric($celdas[$i][$indx_id_cotizacion+5])){
                        $c_asig = 'N/V';
                        $c_valida = false;
                        $partidas_no_validas = true;
                        $cotizaciones[$cotizacion->id_transaccion]['partidas_no_validas'] = true;
                    }else{
                        $cotizaciones[$cotizacion->id_transaccion]['partidas'][$i] = null;
                        $indx_id_cotizacion +=6;
                        continue;
                    }
                    $descuento = $partida_presupuestada->descuento?number_format($partida_presupuestada->descuento, 2, '.', ','):0.00;
                    $importe = $partida->cantidad * $partida_presupuestada->precio_unitario - ($partida->cantidad * $partida_presupuestada->precio_unitario * $descuento / 100);
                    $t_cambio = $partida_presupuestada->moneda->tipo == 1?1: number_format($partida_presupuestada->moneda->cambio->cambio, 4, '.', '');
                    $cotizaciones[$cotizacion->id_transaccion]['partidas'][$i] = [
                        'id_material' => $partida_presupuestada->id_material,
                        'id_item' => $partida->id_item,
                        'id_transaccion' => $partida_presupuestada->id_transaccion,
                        'cantidad_asignada' => $c_asig,
                        'precio_unitario' => $partida_presupuestada->precio_unitario,
                        'precio_unitario_format' => '$' . number_format($partida_presupuestada->precio_unitario, 2, '.', ','),
                        'precio_con_descuento' => $partida_presupuestada->precio_compuesto,
                        'precio_con_descuento_mn' => $partida_presupuestada->precio_compuesto * $partida_presupuestada->tipo_cambio,
                        'precio_unitario_compuesto' => $partida_presupuestada->precio_unitario_compuesto,
                        'moneda' => $partida_presupuestada->moneda->abreviatura,
                        'tipo_cambio' => $t_cambio,
                        'importe' => number_format($importe, 2, '.', ','),
                        'importe_total_moneda' => $partida_presupuestada->total_precio_descuento_partida_moneda_comparativa,
                        'importe_moneda_conversion' =>  number_format($importe * $t_cambio, 2, '.', ','),
                        'importe_moneda_conversion_sf' =>  $importe * $t_cambio,
                        'descuento' => $descuento,
                        'descuento_format' => $partida_presupuestada->partida && $partida_presupuestada->partida->descuento_partida>0? number_format($partida_presupuestada->partida->descuento_partida,2,".",",")."%" : '-',
                        'mejor_opcion' => $partida_presupuestada->mejor_opcion,
                        'color' => $partida_presupuestada->color_opcion,
                        'justificacion' => '',
                        'cantidad_valida' => $c_valida,
                    ];
                    $cotizaciones[$cotizacion->id_transaccion]['partidas_asignadas'] = true;
                    $cant_cotizaciones[$cotizacion->id_transaccion] = 1;
                }
                $indx_id_cotizacion +=6;
            }
            if((float)$cant_asignada > (float)$items[$i]['cantidad_disponible']){
                $items[$i]['asignadas_mayor_disponible'] = true;
                $partidas_no_validas = true;
                $cotizaciones[$cotizacion->id_transaccion]['partidas_no_validas'] = true;
            }
        }
        return ['items'=>$items,'cotizaciones'=> $cotizaciones, 'precios_menores' => $precios, 'cantidad_cotizaciones'=>count($cant_cotizaciones), 'partidas_no_validas' => $partidas_no_validas];
    }

    private function getDatosAsignacionLayout($file_xls)
    {
        $rows = Excel::toArray(new CotizacionImport, $file_xls);
        unlink($file_xls);
        return $rows[0];
    }

    private function getFileXLS($nombre_archivo, $archivo_xls)
    {
        $paths = $this->generaDirectorios($nombre_archivo);
        $exp = explode("base64,", $archivo_xls);
        $data = base64_decode($exp[1]);
        $file_xls = public_path($paths["path_xls"]);
        file_put_contents($file_xls, $data);
        return $file_xls;
    }

    private function generaDirectorios($nombre_archivo)
    {
        $nombre = $nombre_archivo . "_" . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/contratos/contrato_proyectado/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }
}
