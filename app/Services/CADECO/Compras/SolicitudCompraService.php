<?php


namespace App\Services\CADECO\Compras;


use App\Utils\ValidacionSistema;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\CotizacionCompraPartida;
use App\PDF\CADECO\Compras\SolicitudCompraFormato;
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
            $solicitudes = $solicitudes->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
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
                    abort(400, 'No pueden existir dos partidas con el mismo material ('.strval($item['material']['descripcion']).') y mismo destino ('.$item['destino']['destino']['descripcion'].') en la misma solicitud.');
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
        return $this->repository->update($data, $id);
    }

    public function pdfSolicitudCompra($id)
    {
        return $this->repository->show($id)->pdfSolicitudCompra();
    }

    public function getCotizaciones($id){
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
                'descripcion_corta' => substr($partida->material->descripcion, 0, 35),
                'unidad' => $partida->material->unidad,
                'cantidad_solicitada' => number_format($partida->cantidad, 4, '.', ''),
                'cantidad_asignada' => number_format($cantidad_asig_previamente, 4, '.', ''),
                'cantidad_disponible' => number_format($partida->cantidad - $cantidad_asig_previamente, 4, '.', ''),
                'cantidad_base' => number_format($partida->cantidad - $cantidad_asig_previamente, 4, '.', ''),
                'item_pendiente' => $partida->cantidad - $cantidad_asig_previamente > 0?true:false,
            ];
            foreach($solicitud_cotizaciones as $cotizacion){
                if(!$cotizacion->id_empresa)continue;
                if(!array_key_exists($cotizacion->id_transaccion, $cotizaciones)){
                    $cotizaciones[$cotizacion->id_transaccion] = [
                        'id_transaccion' => $cotizacion->id_transaccion,
                        'razon_social' => $cotizacion->empresa->razon_social,
                        'sucursal' => $cotizacion->sucursal->descripcion,
                        'direccion' => $cotizacion->sucursal->direccion,
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
                        'precio_unitario_format' => '$ ' . number_format($cot->precio_unitario, 2, '.', ','),
                        'moneda' => $cot->moneda->abreviatura,
                        'tipo_cambio' => $t_cambio,
                        'importe' => number_format($importe, 2, '.', ','),
                        'importe_moneda_conversion' =>  number_format($importe * $t_cambio, 2, '.', ','),
                        'descuento' => $descuento,
                    ];
                }else{
                    $cotizaciones[$cotizacion->id_transaccion]['partidas'][$i] = null;
                }
            }
        }
        return ['items'=>$items,'cotizaciones'=> $cotizaciones];
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
}
