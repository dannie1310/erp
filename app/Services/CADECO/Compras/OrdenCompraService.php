<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/05/2019
 * Time: 02:16 PM
 */

namespace App\Services\CADECO\Compras;

use DateTime;
use DateTimeZone;
use App\Models\CADECO\Empresa;
use App\Repositories\Repository;
use App\Models\CADECO\OrdenCompra;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\SolicitudCompra;
use App\PDF\Compras\OrdenCompraFormato;
use App\Models\CADECO\Compras\OrdenCompraEliminada;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Models\CADECO\Compras\OrdenCompraPartidaEliminada;


class OrdenCompraService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(OrdenCompra $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
    public function paginate($data)
    {
        $ordenes = $this->repository;

        if(isset($data['numero_folio'])){
            $ordenes = $ordenes->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['id_antecedente'])){
            $solicitud = SolicitudCompra::query()->where([['numero_folio', 'LIKE', '%'.$data['id_antecedente'].'%']])->get();
            foreach ($solicitud as $e){
                $ordenes = $ordenes->whereOr([['id_antecedente', '=', $e->id_transaccion]]);
            }
        }

        if(isset($data['id_empresa'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();
            foreach ($empresa as $e){
                $ordenes = $ordenes->whereOr([['id_empresa', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['observaciones'])){
            $ordenes = $ordenes->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }

        return $ordenes->paginate($data);
    }
    public function pdfOrdenCompra($id)
    {
        $pdf = new OrdenCompraFormato($id);
        return $pdf;
    }

    public function eliminarOrdenes($data){
        try{
            DB::connection('cadeco')->beginTransaction();
            $orden_compra_eliminada = null;
            $item_antecedente= null;
            foreach($data['data'] as $orden){
                $orden_compra = $this->repository->show($orden);

                $orden_compra_eliminada = OrdenCompraEliminada::create([
                    'id_transaccion' => $orden_compra->id_transaccion,
                    'id_antecedente' => $orden_compra->id_antecedente,
                    'id_referente' => $orden_compra->id_referente,
                    'tipo_transaccion' => $orden_compra->tipo_transaccion,
                    'id_obra' => $orden_compra->id_obra,
                    'id_empresa' => $orden_compra->id_empresa,
                    'id_sucursal' => $orden_compra->id_sucursal,
                    'id_moneda' => $orden_compra->id_moneda,
                    'opciones' => $orden_compra->opciones,
                    'observaciones' => $orden_compra->observaciones,
                    'fecha' => $orden_compra->fecha,
                    'comentario' => $orden_compra->comentario,
                    'FechaHoraRegistro' => $orden_compra->FechaHoraRegistro,
                    'numero_folio' => $orden_compra->numero_folio,
                    'monto' => $orden_compra->monto,
                    'saldo' => $orden_compra->saldo,
                    'impuesto' => $orden_compra->impuesto,
                    'anticipo_monto' => $orden_compra->anticipo_monto,
                    'anticipo_saldo' => $orden_compra->anticipo_saldo,
                    'porcentaje_anticipo_pactado' => $orden_compra->porcentaje_anticipo_pactado,
                    'id_costo' => $orden_compra->id_costo,
                    'idserie' => $orden_compra->complemento->idserie?$orden_compra->complemento->idserie:0,
                    'idrqctoc_tabla_comparativa' => $orden_compra->complemento->idrqctoc_tabla_comparativa?$orden_compra->complemento->idrqctoc_tabla_comparativa:0,
                    'plazos_entrega_ejecucion' => $orden_compra->complemento->plazos_entrega_ejecucion,
                    'timestamp_registro' => $orden_compra->complemento->timestamp_registro,
                    'registro' => $orden_compra->complemento->registro,
                    'estatus' => $orden_compra->complemento->estatus,
                    'id_forma_pago' => $orden_compra->complemento->id_forma_pago,
                    'id_forma_pago_credito' => $orden_compra->complemento->id_forma_pago_credito,
                    'id_tipo_credito' => $orden_compra->complemento->id_tipo_credito,
                    'domicilio_entrega' => $orden_compra->complemento->domicilio_entrega,
                    'otras_condiciones' => $orden_compra->complemento->otras_condiciones,
                    'fecha_entrega' => $orden_compra->complemento->fecha_entrega,
                    'con_fianza' => $orden_compra->complemento->con_fianza?$orden_compra->complemento->con_fianza:0,
                    'id_tipo_fianza' => $orden_compra->complemento->id_tipo_fianza,
                    'elimino' => auth()->id(),
                    'motivo' => $data['motivo'],
                ]);

                foreach($orden_compra->partidas as $partida){
                    $item_antecedente = $partida->item_antecedente;
                    $orden_compra_partida = OrdenCompraPartidaEliminada::create([
                        'id_orden_compra_eliminada' => $orden_compra_eliminada->id,
                        'id_item' => $partida->id_item,
                        'id_transaccion' => $partida->id_transaccion,
                        'id_antecedente' => $partida->id_antecedente,
                        'id_material' => $partida->id_material,
                        'unidad' => $partida->unidad,
                        'cantidad' => $partida->cantidad,
                        'anticipo' => $partida->anticipo,
                        'descuento' => $partida->descuento,
                        'precio_material' => $partida->precio_material,
                        'item_antecedente' => $partida->item_antecedente,
                        'precio_unitario' => $partida->precio_unitario,
                        'importe' => $partida->importe,
                        'saldo' => $partida->saldo,
                        'elimino' => auth()->id(),
                        'id_moneda' => $orden_compra->id_moneda,
                    ]);
                }

                $this->repository->delete([], $orden);
            }
/*
            $pendientes = $this->repository->where([['id_referente', '=', $orden_compra_eliminada->id_referente]])->all();

            if($pendientes->count() == 0){
                $asignacion_partida = AsignacionProveedorPartida::where('id_item_solicitud', '=', $item_antecedente)->where('id_transaccion_cotizacion', '=', $orden_compra_eliminada->id_referente)->first();
                $asignacion_partida->asignacion->estado = 1;
                $asignacion_partida->asignacion->save();
            }*/

            DB::connection('cadeco')->commit();
            return response()->json("{}", 200);
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function update(array $data, $id)
    {
        $fecha_e = New DateTime($data['complemento']['fecha_entrega']);
        $fecha_e->setTimezone(new DateTimeZone('America/Mexico_City'));
        $orden_compra = $this->repository->show($id);
        $orden_compra->id_costo = $data['id_costo'];
        $orden_compra->porcentaje_anticipo_pactado = $data['porcentaje_anticipo_pactado'];
        $orden_compra->impuesto = $data['impuesto'];
        $orden_compra->monto = $data['subtotal'] + $data['impuesto'];

        $orden_compra->complemento->plazos_entrega_ejecucion = $data['complemento']['plazos_entrega_ejecucion'];
        $orden_compra->complemento->id_forma_pago = $data['complemento']['id_forma_pago'];
        $orden_compra->complemento->domicilio_entrega = $data['complemento']['domicilio_entrega'];
        $orden_compra->complemento->otras_condiciones = $data['complemento']['otras_condiciones'];
        $orden_compra->complemento->fecha_entrega = $fecha_e;

        $orden_compra->save();
        $orden_compra->complemento->save();

        return $orden_compra;
    }
}
