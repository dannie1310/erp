<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\InventarioEliminado;
use App\Models\CADECO\Compras\ItemSalidaEliminada;
use App\Models\CADECO\Compras\ItemContratista;
use App\Models\CADECO\Compras\MovimientoEliminado;
use App\Models\CADECO\Compras\SalidaEliminada;
use App\Models\CADECO\Contabilidad\Poliza;
use Illuminate\Support\Facades\DB;

class SalidaAlmacen extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo',function ($query) {
            return $query->where('tipo_transaccion', '=', 34);
        });

        self::deleting(function ($salida) {
            if($salida->opciones == 65537 ) {
                $salida->eliminar_transferencia();
            }
            if ($salida->opciones == 1){
                $salida->eliminar_salida();
                }
        });
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class,'id_almacen','id_almacen');
    }

    public function partidas()
    {
        return $this->hasMany(SalidaAlmacenPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 0 :
                return 'Registrada';
                break;
        }
    }

    public function getOperacionAttribute()
    {
        switch ($this->opciones){
            case 1 :
                return 'Salida de Almacén';
                break;
            case 65537 :
                return 'Transferencia';
                break;
        }
    }

    private function eliminar_salida(){
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item) {
            $contratista  = ItemContratista::query()->where('id_item','=',$item['id_item'])->delete();

            $movimiento = Movimiento::query()->where('id_item', $item['id_item'])->get()->toArray();
            foreach ($movimiento as $mov){
                $inventarios = Inventario::query()->where( 'id_lote', $mov['lote_antecedente'] )->get()->toArray();
                foreach ($inventarios as $inv) {
                    $saldo = $inv['saldo'] + $mov['cantidad'];
                    Inventario::query()->where( 'id_lote', $mov['lote_antecedente'] )->update( ['saldo' => $saldo] );
                    $saldo_inventario = Inventario::query()->where( 'id_lote', $mov['lote_antecedente'] )->first();
                    if($saldo_inventario->saldo != $saldo){
                        DB::connection('cadeco')->rollBack();
                    }
                }
                Movimiento::destroy($mov['id_movimiento']);
            }
            Item::destroy($item['id_item']);
        }
    }
    private function eliminar_transferencia(){
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item) {
            $inventario = Inventario::query()->where( 'id_item', $item['id_item'] )->get()->toArray();
            foreach ($inventario as $inv) {
                $inv_antecedente = Inventario::query()->where( 'id_lote', $inv['lote_antecedente'] )->get()->toArray();
                foreach ($inv_antecedente as $inv_ant){
                    $saldo = $inv['cantidad'] + $inv_ant['saldo'];
                    Inventario::query()->where( 'id_lote', $inv['lote_antecedente'] )->update( ['saldo' => $saldo] );
                    $saldo_inventario = Inventario::query()->where( 'id_lote', $inv['lote_antecedente'] )->first();
                    if($saldo_inventario->saldo != $saldo){
                        DB::connection('cadeco')->rollBack();
                    }
                }
            }
            Inventario::destroy( $inventario[0]['id_lote'] );
            Item::destroy($item['id_item']);
        }

    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validar();
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

    private function validar()
    {
        $mensaje = '';
        $poliza = Poliza::query()->where('id_transaccion_sao',$this->id_transaccion)->first();
        if ($poliza != []){
            $mensaje = "-La salida se encuentra asociada a la Prepoliza: #".$poliza->id_int_poliza." \n";
        }
        $items = $this->partidas()->get()->toArray();

        foreach ($items as $item){
            $factura_part = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->first();
            if($factura_part != null){
                $factura = Factura::query()->where('id_transaccion', $factura_part->id_transaccion)->first();
                $mensaje = $mensaje."-Factura: # ". $factura->numero_folio." \n";
            }
            if ($this->opciones == 65537){

                $inventarios = Inventario::query()->where('id_item', $item['id_item'])->get()->toArray();

                if($inventarios == []){
                    $mensaje = $mensaje."-No existe inventario\n";
                }
                $cadena='-Existen transacciones relacionadas:
                ';
                foreach ($inventarios as $inventario){
                    $movimientos = Movimiento::query()->where('lote_antecedente','=', $inventario['id_lote'])->get()->toArray();
                    if($movimientos != []){
                        $i=0;
                        foreach ($movimientos as $mov){
                            $i++;
                            $partida =Item::query()->where('id_item','=',$mov['id_item'])->first();
                            $transa = Transaccion::query()->where('id_transaccion','=',$partida['id_transaccion'])->first();
                            if($mov != []){
                                if($transa['tipo_transaccion'] == 34 && $transa['opciones'] == 1){
                                    $cadena.=$i.') Salida: #'.
                                        $transa['numero_folio'].'
                                        ';
                                }
                                if($transa['tipo_transaccion'] == 34 && $transa['opciones'] == 65537){
                                    $cadena.=$i.') Transferencia: #'.
                                        $transa['numero_folio'].'
                                        ';
                                }
                                $mensaje = $cadena;
                            }
                        }
                    }
                    if($inventario['cantidad'] != $inventario['saldo']){
                        $mensaje = $mensaje."-La cantidad es diferente al saldo del inventario\n";
                    }
                    $inventario_antecedente = Inventario::query()->where('id_lote', $inventario['lote_antecedente'])->get()->toArray();
                    if($inventario_antecedente[0]['saldo']+$inventario['cantidad'] > $inventario_antecedente[0]['cantidad']){
                        $mensaje = $mensaje."-El saldo es mayor a la cantidad del inventario antecedente\n";
                    }
                }
            }
            if ($this->opciones == 1){
                $movimientos = Movimiento::query()->where('id_item', $item['id_item'])->get()->toArray();
                if($movimientos == []){
                    $mensaje = $mensaje."-No existe movimiento\n";
                }
                foreach ($movimientos as $movimiento){
                    $inv_antecedente = Inventario::query()->where('id_lote',$movimiento['lote_antecedente'])->get()->toArray();
                    if($inv_antecedente[0]['saldo']+$movimiento['cantidad'] > $inv_antecedente[0]['cantidad']){
                        $mensaje = $mensaje."-El saldo es mayor a la cantidad del inventario antecedente\n";
                    }
                }
            }

        }
        if($mensaje != "")
        {
            abort(400, "No se puede eliminar la salida de almacén debido a las siguientes razones:\n" . $mensaje . "Favor de comunicarse con Soporte a Aplicaciones y Coordinación SAO.");
        }
    }

    /**
     *  Realiza funciones para despaldar todo lo implicado en la salida material y realizar los respaldos pertinentes.
     */
    private function respaldar($motivo)
    {

            $partidas = $this->partidas()->get()->toArray();
            foreach ($partidas as $partida) {

                if($this->opciones == 65537 )
                {
                    /**
                     * Respaldar el Inventario
                     */
                    $inventario = Inventario::query()->where('id_item', $partida['id_item'])->first()->toArray();
                    $respaldo_inventario = InventarioEliminado::query()->create(
                        [
                            'id_lote' => $inventario['id_lote'],
                            'lote_antecedente' => $inventario['lote_antecedente'],
                            'id_almacen' => $inventario['id_almacen'],
                            'id_material' => $inventario['id_material'],
                            'id_item' => $inventario['id_item'],
                            'saldo' => $inventario['saldo'],
                            'monto_total' => $inventario['monto_total'],
                            'monto_pagado' => $inventario['monto_pagado'],
                            'monto_aplicado' => $inventario['monto_aplicado'],
                            'fecha_desde' => $inventario['fecha_desde'],
                            'referencia' => $inventario['referencia'],
                            'monto_original' => $inventario['monto_original']
                        ]
                    );
                }

                if($this->opciones == 1){
                    /**
                     * Respaldar el movimiento
                     */
                    $movimiento = Movimiento::query()->where('id_item', $partida['id_item'])->first()->toArray();
                    $respaldo_movimiento = MovimientoEliminado::query()->create(
                        [
                            'id_movimiento' => $movimiento['id_movimiento'],
                            'id_concepto' => $movimiento['id_concepto'],
                            'id_item' => $movimiento['id_item'],
                            'id_material' => $movimiento['id_material'],
                            'cantidad' => $movimiento['cantidad'],
                            'monto_total' => $movimiento['monto_total'],
                            'monto_pagado' => $movimiento['monto_pagado'],
                            'monto_original' => $movimiento['monto_original'],
                            'creado' => $movimiento['creado']
                        ]
                    );
                }

                /**
                 * Respaldar el Item
                 */
                $respaldo_item = ItemSalidaEliminada::query()->create(
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
            $respaldo_entrada = SalidaEliminada::query()->create(
                [
                    'id_transaccion' => $this->id_transaccion,
                    'tipo_transaccion' => $this->tipo_transaccion,
                    'numero_folio' => $this->numero_folio,
                    'fecha' => $this->fecha,
                    'id_obra' => $this->id_obra,
                    'id_concepto' => $this->id_concepto,
                    'id_empresa' => $this->id_empresa,
                    'opciones' => $this->opciones,
                    'diferencia' => $this->diferencia,
                    'comentario' => $this->comentario,
                    'observaciones' => $this->observaciones,
                    'FechaHoraRegistro' => $this->FechaHoraRegistro,
                    'NumeroFolioAlt' => $this->NumeroFolioAlt,
                    'motivo_eliminacion' => $motivo
                ]
            );

    }

    private function revisar_respaldos()
    {
        $partidas = $this->partidas()->get()->toArray();
        foreach ($partidas as $partida) {

            if($this->opciones == 65537) {
                $inventario = InventarioEliminado::query()->where( 'id_item', $partida['id_item'] )->first();
                if ($inventario == null) {
                    abort( 400, 'Error en el proceso de eliminación de salida de almacén.' );
                }
            }

            if($this->opciones == 1){
                $movimiento = MovimientoEliminado::query()->where('id_item', $partida['id_item'])->first();
                if ($movimiento == null) {
                    abort(400, 'Error en el proceso de eliminación de salida de almacén.');
                }
            }

            $item = ItemSalidaEliminada::query()->where('id_item', $partida['id_item'])->first();
            if ($item == null) {
                abort(400, 'Error en el proceso de eliminación de salida de almacén.');
            }
        }

        $salida = SalidaEliminada::query()->where('id_transaccion', $this->id_transaccion)->first();
        if ($salida == null) {
            abort(400, 'Error en el proceso de eliminación de salida de almacén.');
        }
    }



}