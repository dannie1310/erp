<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:10 AM
 */

namespace App\Models\CADECO;

use App\Facades\Context;
use App\Models\CADECO\Finanzas\PagoEliminado;
use App\Models\CADECO\Finanzas\PagoEliminadoLog;
use Illuminate\Support\Facades\DB;

class Pago extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_antecedente',
        'numero_folio',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'referencia',
        'observaciones',
        'tipo_transaccion',
        "id_cuenta",
        "id_empresa",
        "id_moneda",
        "saldo",
        "destino",
        "id_usuario"
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 82)
                ->where('estado', '!=', -2);
        });
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function cuenta()
    {
        return $this->hasOne(Cuenta::class, 'id_cuenta', 'id_cuenta');
    }

    public function pagoReposicionFF()
    {
        return $this->hasOne(PagoReposicionFF::class, 'id_transaccion', 'id_transaccion');
    }

    public function pagoVario()
    {
        return $this->belongsTo(PagoVario::class, 'id_transaccion', 'id_transaccion');
    }

    public function pagoEliminadoRespaldo()
    {
        return $this->belongsTo(PagoEliminado::class, 'id_transaccion', 'id_transaccion');
    }

    public function getEstadoStringAttribute()
    {
        $estado = "";
        if ($this->estado==0){
            $estado='Por Autorizar';
        }
        elseif ($this->estado==1){
            $estado='Por Conciliar';
        }
        elseif($this->estado==2){
            $estado='Conciliado';
        }
        return $estado;
    }

    public function eliminar($motivo)
    {
        try {
           // $this->validarEliminacion();
            DB::connection('cadeco')->beginTransaction();
            $this->respaldar($motivo);
            switch($this->opciones)
            {
                case 0: //Pago Factura
                    dd("pago", $this->poliza);
                    echo "i es igual a 0";
                    break;

                case 1: //Pago varios o Reposicion FF
                    if (!is_null($this->id_antecedente))
                    {
                        $this->pagoReposicionFF->elimina();
                    }
                    elseif ($this->pagoVario) //Pago varios
                    {
                        $this->pagoVario->delete();
                    }
                    else{
                        abort(400, "No se encuentra el tipo de pago");
                    }
                    break;

                case 65537:
                    $referente = Transaccion::withoutGlobalScopes()->where('id_transaccion', '=', $this->id_referente)->where('id_obra', '=', Context::getIdObra())->first();
                    $saldo_a_modificar = $referente->saldo - $this->monto;
                    $consulta = "'Pago 65537: id_referente = ".$this->id_referente." saldo = ".$referente->saldo." monto= ".$this->monto." saldo(cambio)= ".$saldo_a_modificar." estado = ".$referente->estado." cambio a 1'";
                    $referente->update([
                        'saldo'  => $saldo_a_modificar,
                        'estado' => 1
                    ]);
                    $this->crearLogRespaldo($consulta);

                    if($referente->tipo_transaccion == 99) //Lista de Raya
                    {
                        $lista_raya = ListaRaya::where('id_transaccion', '=', $this->id_referente)->first();
                        $monto_a_modificar = 0;
                        foreach ($lista_raya->items as $item)
                        {
                            $monto_a_modificar = $item->inventario->monto_pagado - $lista_raya->importe * (-$this->monto / $referente->monto);
                            $item->inventario->update([
                                'monto_pagado' => 'ROUND(' . $monto_a_modificar . ', 2)'
                            ]);
                            $this->crearLogRespaldo("'Pago 65537: listaraya (".$lista_raya->id_transaccion.") editado id_inventario"  . $item->inventario->id_lote . " monto_pagado = " . $item->inventario->monto_pagado . " importe_lista_raya = " . $lista_raya->importe . " monto_pagado = " . $monto_a_modificar . "'");
                        }
                    }
                    if($referente->tipo_transaccion == 102)
                    {
                        $monto_a_modificar = 0;
                        foreach ($referente->items as $item)
                        {
                            $inventario = Inventario::where('id_iotem', '=', $item->id_item)->first();
                            $monto_a_modificar = $inventario->monto_pagado - $referente->importe * (-$this->monto / $referente->monto);
                            $item->inventario->update([
                                'monto_pagado' => 'ROUND(' . $monto_a_modificar . ', 2)'
                            ]);
                            $this->crearLogRespaldo("'Pago 65537: otro (".$referente->id_transaccion.") editado id_inventario"  . $inventario->id_lote . " monto_pagado = " . $inventario->monto_pagado . " importe_lista_raya = " . $referente->importe . " monto_pagado = " . $monto_a_modificar . "'");
                        }
                    }
                    $this->delete();
                    break;

                case 131073: //Pago anticipo destajo
                    echo "131073";
                    break;

                case 262145://probar**
                    //reactivar el cheque
                    $cheque = Transaccion::withoutGlobalScopes()->where('id_transaccion', '=', $this->id_referente)->where('id_obra', '=', Context::getIdObra())->first();
                    $consulta = "'Pago 262145: id_referente = ".$this->id_referente." estado = ".$cheque->estado." cambio a 1'";
                    $cheque->update([
                        'estado' => 1
                    ]);
                    $this->crearLogRespaldo($consulta);
                    $this->delete();
                    break;

                case 327681: // Pago a cuenta o a cuenta por aplicar
                    //Desaplicar --- linea 80 sp_desaplicar_cheque
                    $this->desaplicarCheque();
                    break;
            }

            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        } dd($motivo, "FIN...");
    }

    private function validarEliminacion()
    {
        if($this->estado == 2)
        {
            abort(400, "No se puede eliminar este pago porque se encuentra conciliado.");
        }
        if($this->poliza && $this->poliza->estatus != -3)
        {
            abort(400, "No se puede eliminar este pago porque tiene la poliza ".(strlen($this->poliza->concepto)>25 ? substr($this->poliza->concepto, 0, 25) : $this->poliza->concepto)." con estado: ".$this->poliza->estatusPrepoliza->descripcion);
        }
    }

    private function respaldar($motivo)
    {
        PagoEliminado::create([
            'id_transaccion'   => $this->id_transaccion,
            'id_antecedente'   => $this->id_antecedente,
            'id_referente'     => $this->id_referente,
            'tipo_transaccion' => $this->tipo_transaccion,
            'numero_folio'     => $this->numero_folio,
            'fecha'            => $this->fecha,
            'estado'           => $this->estado,
            'impreso'          => $this->impreso,
            'id_obra'          => $this->id_obra,
            'id_cuenta'        => $this->id_cuenta,
            'id_empresa'       => $this->id_empresa,
            'id_moneda'        => $this->id_moneda,
            'cumplimiento'     => $this->cumplimiento,
            'vencimiento'      => $this->vencimiento,
            'opciones'         => $this->opciones,
            'monto'            => $this->monto,
            'saldo'            => $this->saldo,
            'autorizado'       => $this->autorizado,
            'impuesto'         => $this->impuesto,
            'impuesto_retenido'=> $this->impuesto_retenido,
            'diferencia'       => $this->diferencia,
            'anticipo_monto'   => $this->anticipo_monto,
            'anticipo_saldo'   => $this->anticipo_saldo,
            'anticipo'         => $this->anticipo,
            'retencion'        => $this->retencion,
            'tipo_cambio'      => $this->tipo_cambio,
            'referencia'       => $this->referencia,
            'destino'          => $this->destino,
            'comentario'       => $this->comentario,
            'observaciones'    => $this->observaciones,
            'FechaHoraRegistro'=> $this->FechaHoraRegistro,
            'id_usuario'       => $this->id_usuario,
            'retencionIVA_2_3' => $this->retencionIVA_2_3,
            'motivo'           => $motivo,
            'usuario_elimina'  => auth()->id(),
            'fecha_eliminacion'=> date('Y-m-d H:i:s')
        ]);
    }

    private function crearLogRespaldo($consulta)
    {
        PagoEliminadoLog::create([
            'id_transaccion' => $this->id_transaccion,
            'consulta' => $consulta
        ]);
    }

    /**
     * Este método implementa la lógica del procedimiento almacenado
     * sp_desaplicar_cheque
     */
    private function desaplicarCheque()
    {
        $ordenes_pago = Transaccion::withoutGlobalScopes()
            ->where('numero_folio', '=', $this->numero_folio)
            ->where('tipo_transaccion', '=', 68)
            ->where('opciones', '=', 2)
            ->where('id_obra', '=', Context::getIdObra())
            ->get();

        if ($ordenes_pago)
        {
            foreach ($ordenes_pago as $orden_pago)
            {
                /**
                 * sp_desaplicar_pago
                 */
                $this->desaplicarPago($orden_pago);
                $orden_pago->delete();
            }
        }

        if($this->opciones != 0)
        {
            $aplicaciones_manual = AplicacionManual::where('id_antecedente', '=', $this->id_transaccion)->get();
            if($aplicaciones_manual)
            {
                foreach ($aplicaciones_manual as $aplicacion)
                {
                    $aplicacion->delete();
                }
            }
        }

        $this->update([
           'saldo' => $this->monto,
            'opciones' => 327681
        ])->where('estado', '=', 1);



    }

    private function desaplicarPago($orden_pago)
    {
        $pago_a_desaplicar = Transaccion::withoutGlobalScopes()
            ->where('id_transaccion', '=', $orden_pago->id_referente)
            ->where('id_obra', '=', Context::getIdObra())->first();
        if ($pago_a_desaplicar && $pago_a_desaplicar->tipo_transaccion == 52) //validacion especial cuando su referente es una estimación
        {
            $this->validacionEstimacionPago();
        }

        //inicia validaciones con orden pago
        if ($orden_pago->items->count('id_item') == 0)
        {
            abort(400, "Imposible determinar los items de la factura, no puede ser anulado.");
        }

        foreach ($orden_pago->items as $partida) {
            $aplicado = $partida->importe;
            $item_antecedente = $partida->partida_antecedente->item_antecedente;
            switch ($partida->partida_antecedente->numero) {
                case 0:
                    // actualizamos el monto pagado del lote original
                    // atencion, falta el tipo_cambio del registro 68
                    $inventario = Inventario::where('id_item', '=', $partida->partida_antecedente->id_item)->first();
                    if ($inventario) {
                        $monto_a_modificar = $inventario->monto_pagado - ($aplicado * $pago_a_desaplicar->tipo_cambio);

                        $consulta = "'Pago 327681: tipo 0 - editar inventario: id_lote = " . $inventario->id_lote . " monto_pagado = " . $inventario->monto_pagado . " cambio a " . $monto_a_modificar . "'";

                        $inventario->update([
                            'monto_pagado' => $monto_a_modificar
                        ]);
                        $this->crearLogRespaldo($consulta);

                        if (is_null($inventario->almacen->tipo_almacen))
                        {
                            abort(400, "no existe el lote ");
                        }

                        //ejecuta sp_distribuir_pagado_inventarios
                        $inventario->distribuirPagoInventarios();
                    } else {
                        $movimiento = Movimiento::where('id_item', '=', $partida->partida_antecedente->id_item)->first();
                        $monto_a_modificar = $movimiento->monto_pagado - $partida->partida_antecedente->importe;
                        $movimiento->update([
                            'monto_pagado' => $monto_a_modificar
                        ]);
                    }

                    $monto_a_modificar = $partida->partida_antecedente->saldo + ROUND($aplicado * $pago_a_desaplicar->tipo_cambio, 2);
                    $consulta =  "'Pago 327681: tipo 0 - editar item antecedente: id_item = " .  $partida->partida_antecedente->id_item. " saldo = " .  $partida->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                    $partida->partida_antecedente->update([
                        'saldo' => $monto_a_modificar
                    ]);
                    $this->crearLogRespaldo($consulta);
                    break;
                case 1:
                    $transaccion_antecedente = $partida->partida_antecedente->transaccion_antecedente;
                    if($transaccion_antecedente->tipo_transaccion == 51)
                    {
                        $factor = $aplicado / $transaccion_antecedente->anticipo_monto;
                        $movimientos = Movimiento::join('items', 'items.id_item', 'movimientos.id_item')
                            ->join('EstimacionesSubcontratos', 'EstimacionesSubcontratos.id_transaccion', 'items.id_transaccion')
                            ->where('EstimacionesSubcontratos.id_antecedente', '=', $transaccion_antecedente->id_transaccion)->get();

                        foreach ($movimientos as $movimiento)
                        {
                            $monto_a_modificar = $movimiento->monto_pagado - ROUND($movimiento->monto_total * $factor * ((100 - $movimiento . retencion) / 100 - ($movimiento . monto - $movimiento . impuesto) / $movimiento . suma_importes), 2);
                            $consulta = "'Pago 327681: tipo 1 - editar movimiento(subcontrato - " . $transaccion_antecedente->id_transaccion . " ) : id_movimiento = " . $movimiento->id_movimiento . " monto_pagado = " . $movimiento->monto_pagado . " cambio a " . $monto_a_modificar . "'";
                            $movimiento->update([
                                'monto_pagado' => $monto_a_modificar
                            ]);
                            $this->crearLogRespaldo($consulta);
                        }
                    }
                    if($transaccion_antecedente->tipo_transaccion == 52)
                    {
                        $monto_total = $this->monto_total($transaccion_antecedente);
                        if($monto_total > 0)
                        {
                            foreach ($transaccion_antecedente->items as $partida_estimacion)
                            {
                                $monto_a_modificar = ROUND(($partida_estimacion->movimiento->monto_pagado - $partida_estimacion->movimiento->monto_total * $aplicado * $pago_a_desaplicar->tipo_cambio / $monto_total), 2);
                                $consulta = "'Pago 327681: tipo 1 - editar movimiento(estimación - " . $partida_estimacion->id_transaccion . "): id_movimiento = " . $partida_estimacion->movimiento->id_movimiento . " monto_pagado = " . $partida_estimacion->movimiento->monto_pagado . " cambio a " . $monto_a_modificar . "'";
                                $partida_estimacion->movimiento->update([
                                    'monto_pagado' => $monto_a_modificar
                                ]);
                                $this->crearLogRespaldo($consulta);
                            }
                        }
                    }
                    break;
                case 2:
                    $transaccion_antecedente = $partida->partida_antecedente->transaccion_antecedente;
                    if($transaccion_antecedente->tipo_transaccion == 19 && ($transaccion_antecedente->opciones == 1 || $transaccion_antecedente->opciones == 65537 || $transaccion_antecedente->opciones == 327681))
                    {
                        if($transaccion_antecedente->opciones == 65535)
                        {
                            dd($partida->partida_antecedente->item_antecedente);
                        }else{
                            $anticipo_aplicado = $this->anticipoAplicado($aplicado,$transaccion_antecedente);
                            $remisiones = Item::join('transacciones', 'items.id_transaccion', 'transacciones.id_transaccion')
                                ->where('transacciones.tipo_transaccion', '=', 33)
                                ->where('items.item_antecedente', '=', $item_antecedente)
                                ->where('transacciones.id_obra', '=', Context::getIdObra())
                                ->where('items.anticipo', '>', 0)
                                ->orderBy('numero_folio', 'desc')
                                ->get();

                            foreach ($remisiones as $remision)
                            {

                                if($anticipo_aplicado > 0)
                                {
                                    $pagado_anticipo = $this->pagadoAnticipo($remision->id_item);
                                    dd($pagado_anticipo);
                                }
                            }
                            dd($remisiones);

                        }
                    }

                    if($transaccion_antecedente->tipo_transaccion == 19)
                    {

                    }
                    break;
                case 3:
                    break;
                case 4:
                    break;
                case 5:
                    break;
                case 6:
                    break;
                case 7:
                    break;

                default:
                    abort(400, "Tipo de item no soportado, no puede ser anulado... ");
                    break;
            }
        }
        dd("orden_p", $orden_pago->items);

        dd("PASO");
    }

    private function validacionEstimacionPago($estimacion)
    {
        if ($estimacion->estado != 2) {
            //El estado de la estimacion es erroneo...
            dd("error en la estimacion estado...");
        }

        /**
         * Editar el estado de la estimación
         */
        $consulta = "'Pago 327681: editar estimación: id_referente = " . $estimacion->id_transaccion . " estado = " . $estimacion->estado . " cambio a 1'";
        $estimacion->update([
            'estado' => 1
        ]);
        $this->crearLogRespaldo($consulta);
        /*
        * ejecuta sp_revertir_transaccion
        */
        if (Item::where('id_antecedente', '=', $estimacion->id_transaccion)->count('id_item') != 0) {
            //El estado de la estimacion es erroneo...
            dd("error: La transaccion %d está asociada a otras transacciones'..");
        }
        foreach ($estimacion->items as $item) {
            $consulta = "'Pago 327681: eliminar movimiento: " . $item->movimiento->id_movimiento . " ," . $item->movimiento->id_item . "'";
            $this->crearLogRespaldo($consulta);
            $item->movimiento->delete();
        }
        $consulta = "'Pago 327681: editar estimación: id_transaccion = " . $estimacion->id_transaccion . " estado = " . $estimacion->estado . " cambio a 0, impreso 0, saldo = monto" . $estimacion->monto . "'";
        $estimacion->update([
            'estado' => 0,
            'impreso' => 0,
            'saldo' => $estimacion->monto
        ]);
        $this->crearLogRespaldo($consulta);
    }

    private function monto_total($transaccion_antecedente)
    {
        $suma = 0;
        foreach ($transaccion_antecedente->items as $partida_estimacion)
        {
           $suma +=$partida_estimacion->movimiento->monto_total;
        }
        return $suma;
    }

    private function anticipoAplicado($aplicado,$transaccion_antecedente)
    {
        $suma = 0;
        foreach ($transaccion_antecedente->items as $partida)
        {
            $suma += $aplicado - ROUND(($partida->cantidad * $partida->precio_unitario * $partida->anticipo) / 100 *
                    (($transaccion_antecedente->anticipo_monto - $transaccion_antecedente->anticipo_saldo) /$transaccion_antecedente->anticipo_monto - 1) + $partida->saldo, 2);
        }
        return $suma;
    }

    private function pagadoAnticipo($item_remision)
    {
        $suma = Item::join('ItemsFacturados', 'ItemsFacturados.id_item', 'items.id_item')
                    ->where('ItemsFacturados.id_item', '=', $item_remision)->get();
dd($suma);
    }
}
