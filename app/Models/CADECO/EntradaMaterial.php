<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:04 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Compras\EntradaEliminada;
use App\Models\CADECO\Compras\InventarioEliminado;
use App\Models\CADECO\Compras\ItemContratista;
use App\Models\CADECO\Compras\ItemEntradaEliminada;
use App\Models\CADECO\Compras\MovimientoEliminado;
use App\Models\CADECO\Contabilidad\Poliza;
use Illuminate\Support\Facades\DB;

class EntradaMaterial extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 33)
                ->where('opciones', '=', 1);
        });

        self::deleting(function ($entrada) {
                $items = $entrada->partidas()->get()->toArray();
                $entrada->eliminar_partidas($items);
                $entrada->liberarOrdenCompra();
        });
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 0 :
                return 'Registrada';
                break;
        }
    }

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'id_antecedente','id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(EntradaMaterialPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarParaEliminar();
            $this->respaldar($motivo);
            $this->revisar_respaldos();
            $this->delete();
            DB::connection('cadeco')->commit();
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reglas de negocio que debe cumplir la eliminación
     */
    private function validarParaEliminar()
    {
        $mensaje = "";
        $poliza = Poliza::query()->where('id_transaccion_sao',$this->id_transaccion)->first();
        if ($poliza != null){
            $mensaje = "-Poliza: #".$poliza->id_int_poliza;
        }
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item){
            $inventario = Inventario::query()->where('id_item', $item['id_item'])->get()->toArray();
            $movimiento = Movimiento::query()->where('id_item', $item['id_item'])->get()->toArray();

            $factura_part = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->first();
            if($factura_part != null) {
                $factura = Factura::query()->where('id_transaccion', $factura_part->id_transaccion)->first();
                if($mensaje != ""){
                    $mensaje = $mensaje." \n";
                }
                $mensaje = $mensaje."-Factura: # ". $factura->numero_folio;
            }

            if($inventario == [] && $movimiento == []){
                if($mensaje != ""){
                    $mensaje = $mensaje." \n";
                }
                $mensaje = $mensaje."No existe un inventario ni movimiento";
            }

            if(count($inventario) > 1){
                if($mensaje != ""){
                    $mensaje = $mensaje." \n";
                }
                $mensaje = $mensaje."Existen ".count($inventario)." en el inventario.";
            }

            if(count($movimiento) > 1){
                if($mensaje != ""){
                    $mensaje = $mensaje." \n";
                }
                $mensaje = $mensaje."Existen ".count($movimiento)." en el movimiento.";
            }

            if($inventario != [] && $inventario[0]['cantidad'] != $inventario[0]['saldo']){
                $movimiento_salida = Movimiento::query()->where('lote_antecedente', $inventario[0]['id_lote'])->first();

                if($movimiento_salida != null) {
                    $item_salida = SalidaAlmacenPartida::query()->where('id_item', $movimiento_salida->id_item)->first();
                    $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();

                    if ($mensaje != "") {
                        $mensaje = $mensaje . " \n";
                    }
                    if($salida->tipo_transaccion== 34 && $salida->opciones == 1){
                        $mensaje = $mensaje . "-Salida (Consumo) #".$salida->numero_folio;
                    }
                    if($salida->tipo_transaccion== 34 && $salida->opciones == 65537){
                        $mensaje = $mensaje . "-Salida (Transferencia) #".$salida->numero_folio;
                    }
                }else{
                    if($mensaje != ""){
                        $mensaje = $mensaje." \n";
                    }
                    $mensaje = $mensaje."-Las cantidades (Cantidad = ".$inventario[0]['cantidad']." Saldo=  ".$inventario[0]['saldo'].") no concuerdan y no se encuentra ninguna salida relacionada.";
                }
            }
        }

        if($mensaje != "")
        {
            abort(400, 'No se puede eliminar la entrada de almacén debido a que existen transacciones relacionadas:'."\n". $mensaje);
        }

    }

    /**
     *  Realiza funciones para despaldar todo lo implicado en la entrada material y realizar los respaldos pertinentes.
     */
    private function respaldar($motivo)
    {
            $partidas = $this->partidas()->get()->toArray();
            foreach ($partidas as $partida) {

                /**
                 * Respaldar el Inventario (existe cuando se envia la entrada un almacén)
                 */
                $inventario = Inventario::query()->where('id_item', $partida['id_item'])->first();

                if($inventario != null)
                {
                    $respaldo_inventario = InventarioEliminado::query()->create(
                        [
                            'id_lote' => $inventario->id_lote,
                            'lote_antecedente' => $inventario->lote_antecedente,
                            'id_almacen' => $inventario->id_almacen,
                            'id_material' => $inventario->id_material,
                            'id_item' => $inventario->id_item,
                            'saldo' => $inventario->saldo,
                            'monto_total' => $inventario->monto_total,
                            'monto_pagado' => $inventario->monto_pagado,
                            'monto_aplicado' => $inventario->monto_aplicado,
                            'fecha_desde' => $inventario->fecha_desde,
                            'referencia' => $inventario->referencia,
                            'monto_original' => $inventario->monto_original
                        ]
                    );
                }

                /**
                 * Respaldar el Movimiento (existe cuando se envia la entrada un concepto)
                 */
                $movimiento = Movimiento::query()->where('id_item', $partida['id_item'])->first();

                if($movimiento != null)
                {
                    $respaldo_movimiento = MovimientoEliminado::query()->create(
                        [
                            'id_movimiento' => $movimiento->id_movimiento,
                            'id_concepto' => $movimiento->id_concepto,
                            'id_item' => $movimiento->id_item,
                            'id_material' => $movimiento->id_material,
                            'cantidad' => $movimiento->cantidad,
                            'monto_total' => $movimiento->monto_total,
                            'monto_pagado' => $movimiento->monto_pagado,
                            'monto_original' => $movimiento->monto_original,
                            'creado' => $movimiento->creado
                        ]
                    );
                }

                /**
                 * Respaldar el Item
                 */
                $respaldo_item = ItemEntradaEliminada::query()->create(
                    [
                        'id_item' => $partida['id_item'],
                        'id_transaccion' => $partida['id_transaccion'],
                        'id_antecedente' => $partida['id_antecedente'],
                        'item_antecedente' => $partida['item_antecedente'],
                        'id_almacen' => $partida['id_almacen'],
                        'id_concepto' => $partida['id_concepto'],
                        'id_material' => $partida['id_material'],
                        'unidad' => $partida['unidad'],
                        'numero' => $partida['numero'],
                        'cantidad' => $partida['cantidad'],
                        'cantidad_material' => $partida['cantidad_material'],
                        'importe' => $partida['importe'],
                        'saldo' => $partida['saldo'],
                        'precio_unitario' => $partida['precio_unitario'],
                        'anticipo' => $partida['anticipo'],
                        'precio_material' => $partida['precio_material'],
                        'referencia' => $partida['referencia'],
                        'estado' => $partida['estado'],
                        'cantidad_original1' => $partida['cantidad_original1'],
                        'precio_original1' => $partida['precio_original1'],
                        'id_asignacion' => $partida['id_asignacion']
                    ]
               );
            }

            /**
             * Respaldo de Entrada Almacén
             */
            $respaldo_entrada = EntradaEliminada::query()->create(
                [
                    'id_transaccion' => $this->id_transaccion,
                    'id_antecedente' => $this->id_antecedente,
                    'tipo_transaccion' => $this->tipo_transaccion,
                    'numero_folio' => $this->numero_folio,
                    'fecha' => $this->fecha,
                    'id_obra' => $this->id_obra,
                    'id_empresa' => $this->id_empresa,
                    'id_sucursal' => $this->id_sucursal,
                    'id_moneda' => $this->id_moneda,
                    'cumplimiento' => $this->cumplimiento,
                    'vencimiento' => $this->vencimiento,
                    'opciones' => $this->opciones,
                    'anticipo' => $this->anticipo,
                    'referencia' => $this->referencia,
                    'comentario' => $this->comentario,
                    'observaciones' => $this->observaciones,
                    'TipoLiberacion' => $this->TipoLiberacion,
                    'FechaHoraRegistro' => $this->FechaHoraRegistro,
                    'motivo_eliminacion' => $motivo
                ]
            );
    }


    /**
     *  Revisar los respaldos antes de proceder a eliminar
     */
    private function revisar_respaldos()
    {
        $partidas = $this->partidas()->get()->toArray();
        foreach ($partidas as $partida) {

            $inventario = InventarioEliminado::query()->where('id_item', $partida['id_item'])->first();
            $movimiento = MovimientoEliminado::query()->where('id_item', $partida['id_item'])->first();
            if ($inventario == null && $movimiento == null)
            {
                DB::connection('cadeco')->rollBack();
                abort(400, 'Error en el proceso de eliminación de entrada de almacén.');
            }

            $item = ItemEntradaEliminada::query()->where('id_item', $partida['id_item'])->first();
            if ($item == null)
            {
                DB::connection('cadeco')->rollBack();
                abort(400, 'Error en el proceso de eliminación de entrada de almacén.');
            }
        }

        $entrada = EntradaEliminada::query()->where('id_transaccion', $this->id_transaccion)->first();
        if ($entrada == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de entrada de almacén.');
        }
    }

    /**
     * Antes de eliminar deben regresar los saldos en la tabla de entrega
     */
    private function saldosOrdenCompra($item, $saldoInventario)
    {
        $entregas = Entrega::query()->where('id_item', $item)->first();
        return $entregas->update( ['surtida' => $entregas['surtida']-$saldoInventario]);
    }

    /**
     * Antes de eliminar liberar la orden de compra
     */
    private function liberarOrdenCompra()
    {
        $oc = OrdenCompra::query()->where('id_transaccion', $this->id_antecedente)->first();
        if($oc->estado == 2){
            $oc->update(['estado' => 1]);
        }
    }

    /**
     * Elimina las partidas
     */
    private function eliminar_partidas($partidas)
    {
        foreach ($partidas as $item) {
            $inventario = Inventario::query()->where('id_item', $item['id_item'])->first();
            $movimiento = Movimiento::query()->where('id_item', $item['id_item'])->first();

            if($inventario != null)
            {
                $entregas = $this->saldosOrdenCompra($item['item_antecedente'], $inventario['saldo']);
                if($entregas == true) {
                    Inventario::destroy($inventario['id_lote']);
                }else{
                    DB::connection('cadeco')->rollBack();
                    abort(400, 'Error al cambiar los saldos en la orden de compra');
                }
            }

            if($movimiento != null)
            {
                $entregas = $this->saldosOrdenCompra($item['item_antecedente'], $movimiento['cantidad']);
                if($entregas == true) {
                    Movimiento::destroy($movimiento['id_movimiento']);
                }else{
                    DB::connection('cadeco')->rollBack();
                    abort(400, 'Error al cambiar los saldos en la orden de compra');
                }
            }
            ItemContratista::query()->where('id_item','=',$item['id_item'])->delete();
            Item::destroy($item['id_item']);
        }
    }
}