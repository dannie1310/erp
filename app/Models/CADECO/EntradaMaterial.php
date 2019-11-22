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
use App\Models\CADECO\Contabilidad\HistPoliza;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use Illuminate\Support\Facades\DB;
use DateTime;

class EntradaMaterial extends Transaccion
{
    public const TIPO_ANTECEDENTE = 19;
    public const OPCION_ANTECEDENTE = 1;

    protected $fillable = [
        'id_antecedente',
        'tipo_transaccion',
        'id_empresa',
        'id_sucursal',
        'referencia',
        'observaciones',
        'id_usuario',
        'id_moneda',
        'estado',
        'opciones',
        'comentario',
        'FechaHoraRegistro',
        'id_obra',
        'fecha',
        'anticipo'
    ];
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 33)
                ->where('opciones', '=', 1);
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

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'id_antecedente','id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(EntradaMaterialPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function entregasContratista()
    {
        return $this->hasManyThrough(ItemContratista::class,EntradaMaterialPartida::class,"id_transaccion","id_item","id_transaccion","id_item");
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
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

    public function getTransaccionesRelacionadasAttribute()
    {

        $transacciones = [];
        $poliza = Poliza::query()->where('id_transaccion_sao',$this->id_transaccion)->first();
        if ($poliza != null){
            if($poliza->estatus != -3) {
                $transacciones[] =[
                    "numero_folio"=>$poliza->numero_folio_format,
                    "fecha"=>$poliza->fecha_format,
                    "fecha_hora_registro"=>$poliza->fecha_hora_registro_format,
                    "fecha_hora_registro_orden"=>$poliza->fecha_hora_registro_orden,
                    "tipo_transaccion"=>"Prepoliza",
                    "concepto"=>$poliza->concepto,
                ];
            };
        }
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item){
            $inventario = Inventario::query()->where('id_item', $item['id_item'])->first();
            $movimiento = Movimiento::query()->where('id_item', $item['id_item'])->first();

            $factura_part = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->first();
            if($factura_part != null) {
                $factura = Factura::query()->where('id_transaccion', $factura_part->id_transaccion)->first();
                $contra_recibo = ContraRecibo::query()->where('id_transaccion', $factura->id_antecedente)->first();
                $transacciones[] =[
                    "numero_folio"=>$contra_recibo->numero_folio_format,
                    "fecha"=>$contra_recibo->fecha_format,
                    "fecha_hora_registro"=>$contra_recibo->fecha_hora_registro_format,
                    "fecha_hora_registro_orden"=>$contra_recibo->fecha_hora_registro_orden,
                    "tipo_transaccion"=>"Contrarecibo",
                    "concepto"=>$contra_recibo->observaciones
                ];
                $transacciones[] =[
                    "numero_folio"=>$factura->numero_folio_format,
                    "fecha"=>$factura->fecha_format,
                    "fecha_hora_registro"=>$factura->fecha_hora_registro_format,
                    "fecha_hora_registro_orden"=>$factura->fecha_hora_registro_orden,
                    "tipo_transaccion"=>"Factura",
                    "concepto"=>$factura->observaciones
                ];

            }


            if($inventario != null && $inventario->cantidad != $inventario->saldo){
                $movimientos_salidas = Movimiento::query()->where('lote_antecedente', $inventario->id_lote)->get();
                $inventarios_transferencias =  Inventario::query()->where('lote_antecedente', $inventario->id_lote)->get();

                if($movimientos_salidas->toArray() != []) {
                    foreach ($movimientos_salidas as $movimientos_salida) {
                        $item_salida = SalidaAlmacenPartida::query()->where('id_item', $movimientos_salida->id_item)->first();
                        $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();
                        $transacciones[] =[
                            "numero_folio"=>$salida->numero_folio_format,
                            "fecha"=>$salida->fecha_format,
                            "fecha_hora_registro"=>$salida->fecha_hora_registro_format,
                            "fecha_hora_registro_orden"=>$salida->fecha_hora_registro_orden,
                            "tipo_transaccion"=>"Salida (Consumo)",
                            "concepto"=>$salida->observaciones
                        ];
                    }
                }
                if($inventarios_transferencias->toArray() != []){
                    foreach ($inventarios_transferencias as $inventarios_transferencia) {
                        $item_salida = SalidaAlmacenPartida::query()->where('id_item', $inventarios_transferencia->id_item)->first();
                        $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();
                        $transacciones[] =[
                            "numero_folio"=>$salida->numero_folio_format,
                            "fecha"=>$salida->fecha_format,
                            "fecha_hora_registro"=>$salida->fecha_hora_registro_format,
                            "tipo_transaccion"=>"Salida (Transferencia)",
                            "fecha_hora_registro_orden"=>$salida->fecha_hora_registro_orden,
                            "concepto"=>$salida->observaciones
                        ];
                    }
                }
            }
        }

        uasort($transacciones, $this->ordenar('fecha_hora_registro_orden'));

        $transacciones = array_values(array_unique($transacciones,SORT_REGULAR ));

        if($transacciones != [])
        {

            return $transacciones;
        }
        else
            {
                return null;
            }
    }

    public function ordenar($clave)
    {
        return function ($a, $b) use ($clave)
        {
            return strcmp($a[$clave], $b[$clave]);
        };
    }



    /**
     * Reglas de negocio que debe cumplir la eliminación
     */
    private function validarParaEliminar()
    {
        $mensaje = "";
        $mensaje_items = [];
        $poliza = Poliza::query()->where('id_transaccion_sao',$this->id_transaccion)->first();
        if ($poliza != null){
            if($poliza->estatus != -3) {
                $mensaje = "-Prepoliza: #".$poliza->id_int_poliza." \n";
            };
        }
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item){
            $inventario = Inventario::query()->where('id_item', $item['id_item'])->first();
            $movimiento = Movimiento::query()->where('id_item', $item['id_item'])->first();

            $factura_part = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->first();
            if($factura_part != null) {
                $factura = Factura::query()->where('id_transaccion', $factura_part->id_transaccion)->first();
                $contra_recibo = ContraRecibo::query()->where('id_transaccion', $factura->id_antecedente)->first();
                array_push($mensaje_items,  "-ContraRecibo: # " . $contra_recibo->numero_folio . " Factura: # " . $factura->numero_folio ." \n" );
            }

            if($item['id_almacen'] != null && $inventario == null && $movimiento == null){
                array_push($mensaje_items,  "-No existe un inventario ni movimiento \n". $inventario . $movimiento. $item['id_item'] );
            }

            if($inventario != null && $inventario->cantidad != $inventario->saldo){
                $movimientos_salidas = Movimiento::query()->where('lote_antecedente', $inventario->id_lote)->get();
                $inventarios_transferencias =  Inventario::query()->where('lote_antecedente', $inventario->id_lote)->get();

                if($movimientos_salidas->toArray() != []) {
                    foreach ($movimientos_salidas as $movimientos_salida) {
                        $item_salida = SalidaAlmacenPartida::query()->where('id_item', $movimientos_salida->id_item)->first();
                        $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();
                        array_push($mensaje_items, "-Salida (Consumo) #" . $salida->numero_folio . " \n");
                    }
                }
                if($inventarios_transferencias->toArray() != []){
                    foreach ($inventarios_transferencias as $inventarios_transferencia) {
                        $item_salida = SalidaAlmacenPartida::query()->where('id_item', $inventarios_transferencia->id_item)->first();
                        $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();
                        array_push($mensaje_items, "-Salida (Transferencia) #" . $salida->numero_folio . " \n");
                    }
                }
                if($movimientos_salidas->toArray() == [] && $inventarios_transferencias->toArray() == []){
                    $material = Material::query()->where('id_material', $item['id_material'])->first();
                    array_push($mensaje_items,"-Las cantidades del insumo ".$material->descripcion." (Entrada = ".$inventario->cantidad.", Saldo=  ".$inventario->saldo.") no concuerdan y no se encuentra ninguna salida relacionada.\n");
                }
            }
        }
        $mensaje_items = array_values(array_unique($mensaje_items));

        if($mensaje_items != [])
        {
            $mensaje_fin = "";
            foreach ($mensaje_items as $mensaje_item) {
                $mensaje_fin = $mensaje_fin . $mensaje_item;
            }
            $mensaje = $mensaje.$mensaje_fin;
        }

        if($mensaje != "")
        {
            abort(400, "No se puede eliminar la entrada de almacén debido a que existen las siguientes transacciones relacionadas:\n". $mensaje. "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
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
            if ($partida['id_almacen'] != null  && $inventario == null && $movimiento == null)
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
     * Elimina las partidas
     */
    public function eliminar_partidas($partidas)
    {
        $poliza = Poliza::query()->where('id_transaccion_sao',$this->id_transaccion)->first();
        if ($poliza != null){
            $poliza_historico = HistPoliza::query()->where('id_transaccion_sao',$this->id_transaccion)->first();
            $poliza_movimiento = PolizaMovimiento::query()->where('id_transaccion_sao',$this->id_transaccion)->get();

            if($poliza_historico != null) {
                    HistPoliza::query()->where('id_int_poliza', $poliza_historico->id_int_poliza)->update(['id_transaccion_sao' => NULL]);
            }
            if($poliza_movimiento != null){
                foreach ($poliza_movimiento as $i) {
                    PolizaMovimiento::query()->where('id_int_poliza_movimiento', $i->id_int_poliza_movimiento)->update(['id_transaccion_sao' => NULL]);
                }
            }
            Poliza::query()->where('id_int_poliza',$poliza->id_int_poliza)->update(['id_transaccion_sao' => NULL]);
        }
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

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $ordencompra = OrdenCompra::find($data['id_antecedente']);

            $entrada = $this->create([
                'id_antecedente' => $data['id_antecedente'],
                'id_empresa' => $ordencompra->id_empresa,
                'id_sucursal' => $ordencompra->id_sucursal,
                'referencia' => $data['remision'],
                'id_moneda' => $ordencompra->id_moneda,
                'anticipo' => $ordencompra->anticipo,
                'fecha' => date_format(new DateTime($data['fecha']), 'Y-m-d'),
                'observaciones' => $data['observaciones']
            ]);

            foreach ($data['partidas'] as $item){
                $item_antecedente = OrdenCompraPartida::find($item['id']);
                if(isset($item['cantidad_ingresada']) == true) {
                    if ($item['destino']['tipo_destino'] == 1) {
                        $item_guardado = $entrada->partidas()->create([
                            'item_antecedente' => $item['id'],
                            'id_transaccion' => $entrada->id_transaccion,
                            'id_antecedente' => $entrada->id_antecedente,
                            'id_concepto' => $item['destino']['id_destino'],
                            'id_material' => $item['id_material'],
                            'unidad' => $item['unidad'],
                            'numero' => 0,
                            'cantidad_material' => $item['cantidad_material'],
                            'cantidad' => $item['cantidad_ingresada'],
                            'cantidad_original1' => $item['cantidad_ingresada'],
                            'importe' => $item['precio_unitario'] * $item['cantidad_ingresada'],
                            'saldo' => $item['precio_unitario'] * $item['cantidad_ingresada'],
                            'precio_unitario' => $item['precio_unitario'],
                            'anticipo' => $item_antecedente->anticipo
                        ]);
                    }
                    if ($item['destino']['tipo_destino'] == 2) {
                        $item_guardado = $entrada->partidas()->create([
                            'item_antecedente' => $item['id'],
                            'id_transaccion' => $entrada->id_transaccion,
                            'id_antecedente' => $entrada->id_antecedente,
                            'id_almacen' => $item['destino']['id_destino'],
                            'id_material' => $item['id_material'],
                            'unidad' => $item['unidad'],
                            'numero' => 0,
                            'cantidad_material' => $item['cantidad_material'],
                            'cantidad' => $item['cantidad_ingresada'],
                            'cantidad_original1' => $item['cantidad_ingresada'],
                            'importe' => $item['precio_unitario'] * $item['cantidad_ingresada'],
                            'saldo' => $item['precio_unitario'] * $item['cantidad_ingresada'],
                            'precio_unitario' => $item['precio_unitario'],
                            'anticipo' => $item_antecedente->anticipo
                        ]);
                    }

                    if(isset($item['contratista_seleccionado']) ){
                        ItemContratista::query()->create( ['id_item' => $item_guardado->id_item,
                            'id_empresa' => $item['contratista_seleccionado']['empresa_contratista'],
                            'con_cargo' => $item['contratista_seleccionado']['opcion']]);
                    }
                }
            }

            $ordencompra->cerrar();
            DB::connection('cadeco')->commit();
            return $entrada;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    /*public function validarCantidades($partidas)
    {
        foreach ($partidas as $i){
            if(isset($i['cantidad_ingresada']) == true) {
                $cantidad_entradas = EntradaMaterialPartida::query()->where('item_antecedente', '=', $i['id'])->sum('cantidad');
                $cantidad_item_orden = OrdenCompraPartida::query()->where('id_item', '=', $i['id'])->pluck('cantidad')->first();
                if ($cantidad_item_orden < $cantidad_entradas)
                {
                    abort(400, 'El material: ' . $i['material']['descripcion'] . '  está entregado completamente.');
                }

                if(!($cantidad_item_orden > $cantidad_entradas && $cantidad_item_orden >= $cantidad_entradas+$i['cantidad_ingresada']))
                {
                    abort(400, 'El material: ' . $i['material']['descripcion'] . '  sobrepasa la cantidad ingresada.');
                }
            }
        }
    }*/

    /*public function validarOrdenCompraCumplida($partidas)
    {
        $suma_totales = 0;
        foreach ($partidas as $i) {
            if(isset($i['cantidad_ingresada']) == true) {
                $cantidad_entradas = EntradaMaterialPartida::query()->where('item_antecedente', '=', $i['item_antecedente'])->sum('cantidad');
                $cantidad_item_orden = OrdenCompraPartida::query()->where('id_item', '=', $i['item_antecedente'])->pluck('cantidad')->first();

                if ((float)$cantidad_item_orden == (float)$cantidad_entradas + (float)$i['cantidad_ingresada']) {
                    $suma_totales = $suma_totales + 1;
                }
            }
        }

        if($suma_totales == count($partidas)){
           return true;
        }
        return false;
    }*/
}
