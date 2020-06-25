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

    public function pagoAnticipoDestajo()
    {
        return $this->belongsTo(PagoAnticipoDestajo::class, 'id_transaccion', 'id_transaccion');
    }

    public function transaccionReferente()
    {
        return $this->belongsTo(Transaccion::class, 'id_referente', 'id_transaccion');
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
                    $ordenes_pago = Transaccion::withoutGlobalScopes()
                        ->where('numero_folio', '=', $this->numero_folio)
                        ->where('tipo_transaccion', '=', 68)
                        ->where('id_obra', '=', Context::getIdObra())
                        ->where('monto', '<', 0)
                        ->distinct()
                        ->get();

                    if ($ordenes_pago)
                    {
                        foreach ($ordenes_pago as $orden_pago)
                        {
                            $this->desaplicarPago($orden_pago);
                            $orden_pago->delete();
                        }
                    }
                    $this->pagoAnticipoDestajo->recalculaAnticipo();
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
                    $this->desaplicarCheque();
                    break;
            }
            $this->delete();

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

    public function crearLogRespaldo($consulta)
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
        $opcion = $this->opciones == 327681 ? 2 : $this->opciones;
        $ordenes_pago = Transaccion::withoutGlobalScopes()
            ->where('numero_folio', '=', $this->numero_folio)
            ->where('tipo_transaccion', '=', 68)
            ->where('opciones', '=', $opcion)
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

    /**
     * @param $orden_pago
     */
    private function desaplicarPago($pago)
    {
        if(is_null($pago))
        {
            abort(400, "No se puede eliminar este pago porque no existe la orden de pago para ser desaplicada.");
        }
        $id_factura = $pago->id_referente;
        $pagado = -$pago->monto;
        $pago_a_desaplicar = Transaccion::withoutGlobalScopes()
            ->where('id_transaccion', '=', $pago->id_referente)
            ->where('id_obra', '=', Context::getIdObra())->first();

        if ($pago_a_desaplicar && $pago_a_desaplicar->tipo_transaccion == 52)
        {
            $this->validacionEstimacionPago(Estimacion::find($pago->id_referente)->first());
        }

        //inicia validaciones con orden pago
        if (is_null($pago->items))
        {
            abort(400, "Imposible determinar los items de la factura, no puede ser anulado.");
        }

        $id_contrarecibo = $pago_a_desaplicar->id_antecedente;
        $tipo_cambio = $pago_a_desaplicar->tipo_cambio;
        $factor_iva      = $pago_a_desaplicar->monto / ($pago_a_desaplicar->monto - $pago_a_desaplicar->impuesto);

        foreach ($pago->items as $partida) {
            $aplicado = $partida->importe;
            $id_item = $partida->item_antecedente;
           // $item_antecedente = $partida->partida_antecedente->item_antecedente;
	        $id_antecedente   = $partida->partida_antecedente->id_antecedente;
	        //$item_antecedente = $partida->partida_antecedente->item_antecedente;
	        $importe          = $partida->partida_antecedente->importe;
	        if($partida->partida_antecedente->numero < 0 || $partida->partida_antecedente->numero > 7)
            {
                abort(400, "Error al validar tipo de item no soportado, no puede ser anulado.");
            }
            switch ($partida->partida_antecedente->numero) {//$tipo_item
                case 0:
                    // actualizamos el monto pagado del lote original
                    // atencion, falta el tipo_cambio del registro 68

                    $this->recalculosInventario($partida->partida_antecedente->item_antecedente,  ROUND($partida->importe * $pago_a_desaplicar->tipo_cambio, 2));
                    $this->recalculosMovimiento($partida->partida_antecedente->item_antecedente, $partida->partida_antecedente->importe);

                    $monto_a_modificar = $partida->partida_antecedente->partida_antecedente->saldo + ROUND( $partida->importe * $pago_a_desaplicar->tipo_cambio, 2);
                    $consulta =  "'Pago 327681: tipo 0 - editar item antecedente: id_item = " .  $partida->partida_antecedente->partida_antecedente->id_item. " saldo = " .  $partida->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                    dd("por cambiar", $consulta);
                    $partida->partida_antecedente->partida_antecedente->update([
                        'saldo' => $monto_a_modificar
                    ]);
                    $this->crearLogRespaldo($consulta);
                    break;
                case 1:
                    $transaccion_antecedente = $partida->partida_antecedente->transaccionAntecedente;
                    if($transaccion_antecedente->tipo_transaccion == 51)
                    {
                        $factor = $partida->importe / $transaccion_antecedente->anticipo_monto;
                        $movimientos = Movimiento::join('items', 'items.id_item', 'movimientos.id_item')
                            ->join('EstimacionesSubcontratos', 'EstimacionesSubcontratos.id_transaccion', 'items.id_transaccion')
                            ->where('EstimacionesSubcontratos.id_antecedente', '=', $partida->partida_antecedente->id_antecedente)->get();
                        dd($movimientos, "movimiento ??? tipo 1");
                        foreach ($movimientos as $movimiento)
                        {
                            $monto_a_modificar = $movimiento->monto_pagado - ROUND($movimiento->monto_total * ($partida->importe / $transaccion_antecedente->anticipo_monto) * ((100 - $movimiento->retencion) / 100 - ($movimiento->monto - $movimiento->impuesto) / $movimiento->suma_importes), 2);
                            $consulta = "'Pago 327681: tipo 1 - editar movimiento (transaccion: " . $transaccion_antecedente->id_transaccion . " ) : id_movimiento = " . $movimiento->id_movimiento . " monto_pagado = " . $movimiento->monto_pagado . " cambio a " . $monto_a_modificar . "'";
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
                                $monto_a_modificar = ROUND(($partida_estimacion->movimiento->monto_pagado - $partida_estimacion->movimiento->monto_total * $partida->importe * $pago_a_desaplicar->tipo_cambio / $monto_total), 2);
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
                    $transaccion_antecedente = $partida->partida_antecedente->transaccionAntecedente;
                    if($transaccion_antecedente->tipo_transaccion == 19 && in_array($transaccion_antecedente->opciones, [1, 65537, 327681, 65535]))
                    {
                        if($transaccion_antecedente->opciones == 65535)
                        {
                            $monto_a_modificar = $partida->partida_antecedente->partida_antecedente->partida_antecedente->saldo + $partida->importe;
                            $consulta = "'Pago 327681: tipo 2 - editar item de tipo orden compra(" . $transaccion_antecedente->id_transaccion . "): id_item = " . $partida->partida_antecedente->partida_antecedente->partida_antecedente->id_item . " saldo = " . $partida->partida_antecedente->partida_antecedente->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                            // actualizacion de la remision
                            $partida->partida_antecedente->partida_antecedente->partida_antecedente->update([
                                'saldo' => $monto_a_modificar
                            ]);
                            $this->crearLogRespaldo($consulta);
                            // actualizacion de la Orden de Compra
                            dd($partida->partida_antecedente->partida_antecedente->partida_antecedente, $partida->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente);
                            $monto_a_modificar = $partida->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->importe - $partida->importe;
                            $consulta = "'Pago 327681: tipo 2 - editar item de tipo orden compra(" . $transaccion_antecedente->id_transaccion . "): id_item = " . $partida->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->id_item . " importe = " . $partida->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->importe . " cambio a " . $monto_a_modificar . "'";
                            $partida->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->update([
                                'importe' => $monto_a_modificar
                            ]);
                            $this->crearLogRespaldo($consulta);
                            $this->recalculosInventario($partida->partida_antecedente->partida_antecedente->item_antecedente, $partida->importe);
                        }
                        else {
                            $acumulado = 0;
                            $anticipo_aplicado = $this->anticipoAplicado($partida->importe,$transaccion_antecedente);
                            $remisiones = Item::join('transacciones', 'items.id_transaccion', 'transacciones.id_transaccion')
                                ->where('transacciones.tipo_transaccion', '=', 33)
                                ->where('items.item_antecedente', '=', $partida->partida_antecedente->item_antecedente)
                                ->where('transacciones.id_obra', '=', Context::getIdObra())
                                ->where('items.anticipo', '>', 0)
                                ->orderBy('numero_folio', 'desc')
                                ->get();


                            foreach ($remisiones as $remision)
                            {
                                $id_item_remision = $remision->id_item;
                                //esta variable si se queda we $pagado_anticipo
                                $pagado_anticipo = ($remision->importe - $remision->saldo);
                                if($anticipo_aplicado > 0)
                                {
                                    $factura = Factura::whereHas('items')->where('id_item', '=', $remision->id_item)->first();
                                    dd($factura);
                                    $pagado_anticipo = $pagado_anticipo - $this->pagadoAnticipo($factura);
                                    dd($pagado_anticipo);

                                    if($pagado_anticipo > ($anticipo_aplicado - $acumulado))
                                    {
                                        $pagado_anticipo = $anticipo_aplicado - $acumulado;
                                    }

                                    $monto_a_modificar = $remision->saldo + $pagado_anticipo;
                                    $consulta = "'Pago 327681: tipo 2 - editar item remisión (transaccion: " . $remision->id_transaccion . " ) : id_item = " . $remision->id_item . " saldo = " . $remision->saldo . " cambio a " . $monto_a_modificar . " pago_anticipo_calculado:'".$pagado_anticipo."'";
                                    $remision->update([
                                        'saldo'  => $monto_a_modificar,
                                        'estado' => 0
                                    ]);
                                    $this->crearLogRespaldo($consulta);
                                    $this->recalculosInventario($remision->id_item, $pagado_anticipo);
                                    $this->recalculosMovimiento($remision->id_item, $pagado_anticipo);
                                    //esta variable se debe quedar $acumulado
                                    /**
                                     * Acumular el anticipo amortizado en este pago
                                     */
                                    $acumulado = $acumulado + $pagado_anticipo;
                                    dd("acumulado", $acumulado);
                                }
                            }
                            $monto_a_modificar =  $partida->partida_antecedente->partida_antecedente->saldo + $acumulado;
                            $consulta = "'Pago 327681: tipo 2 - editar item: id_item = " . $partida->partida_antecedente->partida_antecedente->id_item . " saldo = " . $partida->partida_antecedente->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . " acumulado:'".$acumulado."'";
                            $partida->partida_antecedente->partida_antecedente->update([
                                'saldo' => $monto_a_modificar
                            ]);
                            $this->crearLogRespaldo($consulta);
                        }
                    }
                    else if($transaccion_antecedente->tipo_transaccion == 19)
                    {
                        $inventarios = Inventario::join('items', 'items.id_item', 'inventarios.id_item')
                            ->join('transacciones', 'transacciones.id_transaccion', 'items.id_transaccion')
                            ->where('item_antecedente', '=', $partida->partida_antecedente->item_antecedente)
                            ->where('tipo_transaccion', '=', 33);

                        dd("inventarios", $inventarios->sum('inventarios.numero'));
                        $numero_total = $inventarios->sum('inventarios.numero');
                        if($numero_total > 0)
                        {
                            foreach ($inventarios as $inventario)
                            {
                                $monto_a_modificar = $inventario->monto_anticipo - ROUND($partida->importe * $inventario->numero / $numero_total, 2);
                                $consulta = "'Pago 327681: tipo 2 - editar inventario: id_lote = " . $inventario->id_lote . " monto_aplicado = " . $inventario->monto_aplicado . " cambio a " . $monto_a_modificar . "'";
                                $inventarios->update([
                                    'monto_aplicado' => $monto_a_modificar
                                ]);
                                $this->crearLogRespaldo($consulta);
                            }

                            $monto_a_modificar = $partida->partida_antecedente->partida_antecedente->saldo + $partida->importe;
                            $consulta = "'Pago 327681: tipo 2 - editar item: id_item = " .$partida->partida_antecedente->partida_antecedente->id_item. " saldo = " .  $partida->partida_antecedente->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                            $partida->partida_antecedente->partida_antecedente->update([
                               'saldo' => $monto_a_modificar
                            ]);
                            $this->crearLogRespaldo($consulta);
                            dd("fin??", $monto_a_modificar);
                        }
                    }
                    else{
                        $inventario = Inventario::where('id_item', '=',  $partida->partida_antecedente->partida_antecedente->item_antecedente)->first();
                        dd("else",$inventario, $partida->partida_antecedente->partida_antecedente->item_antecedente);
                        if ($inventario)
                        {
                            $monto_pagado =  $inventario->monto_pagado - $partida->importe;
                            $monto_anticipo = $inventario->monto_anticipo - $partida->importe;
                            $consulta = "'Pago 327681: tipo 2 - editar inventario: id_lote = " . $inventario->id_lote . " monto_pagado = " . $inventario->monto_pagado . " cambio a " . $monto_pagado .  "  monto_anticipo = " . $inventario->monto_anticipo ."cambio a ".$monto_anticipo."'";
                            dd("cambio inventario ..",$monto_pagado, $monto_anticipo);
                            $inventario->update([
                                'monto_pagado' => $monto_pagado,
                                'monto_anticipo' => $monto_anticipo
                            ]);
                            $inventario->distribuirPagoInventarios();
                            $this->crearLogRespaldo($consulta);
                        }
                        $monto_a_modificar = $partida->partida_antecedente->partida_antecedente->saldo - $partida->importe;
                        $consulta = "'Pago 327681: tipo 2 - editar item: id_item = " .$partida->partida_antecedente->partida_antecedente->id_item. " saldo = " .  $partida->partida_antecedente->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                        $partida->partida_antecedente->partida_antecedente->update([
                            'saldo' => $monto_a_modificar
                        ]);
                        $this->crearLogRespaldo($consulta);
                    }
                    //actualiza el saldo de la O/C u O/R
                    if($partida->partida_antecedente->partida_antecedente->transaccion->tipo_transaccion == 19)
                    {
                        dd("OC o OR??");
                        $monto_a_modificar = $partida->partida_antecedente->partida_antecedente->transaccion->anticipo_saldo + ROUND($partida->importe * ($pago_a_desaplicar->monto / ($pago_a_desaplicar->monto - $pago_a_desaplicar->impuesto)), 2);
                        $consulta = "'Pago 327681: tipo 2 - editar transaccion: id_transaccion = " . $partida->partida_antecedente->partida_antecedente->transaccion->id_transaccion . " anticipo_saldo = " . $partida->partida_antecedente->partida_antecedente->transaccion->anticipo_saldo . " cambio a " . $monto_a_modificar . "'";
                        $partida->partida_antecedente->partida_antecedente->transaccion->update([
                            'anticipo_saldo' => $monto_a_modificar
                        ]);
                        $this->crearLogRespaldo($consulta);
                    }
                    dd("paso");
                    break;
                case 3:
                    dd("tipo 3");
                    $this->recalculosInventario($partida->partida_antecedente->item_antecedente, ROUND($partida->importe * $pago_a_desaplicar->tipo_cambio, 2));
                    break;
                case 4:
                    dd("inicio-4");
                    $lista_raya = ListaRaya::find($partida->partida_antecedente->id_antecedente);
                    if($lista_raya)
                    {
                        $lista_raya->desaplicaPago();
                        $this->crearLogRespaldo("'Pago 327681: tipo 4 - editar inventario (listaRaya: ".$lista_raya->id_transaccion." )todos los monto_pagado cambio a cero.'");
                    }
                    else{
                        $prestacion = Prestacion::find($partida->partida_antecedente->id_antecedente);
                        if($prestacion)
                        {
                            $factor = $partida->partida_antecedente->importe / $prestacion->monto;
                            //posible cambio cuando se sepa que es $partida->partida_antecedente->importe
                            $prestacion->desaplicaPago($factor);
                            $this->crearLogRespaldo("'Pago 327681: tipo 4 - editar inventario (prestacion: ".$prestacion->id_transaccion." )todos los monto_pagado se recalcula.");
                        }
                    }
                    dd("fin - 4");
                break;
                case 5:
                    break;
                case 6:
                    break;
                case 7:
                    $this->recalculosInventario($partida->item_antecedente, ROUND($partida->importe * $pago_a_desaplicar->tipo_cambio, 2));
                    $this->recalculosMovimiento($partida->item_antecedente, ROUND($partida->importe * $pago_a_desaplicar->tipo_cambio, 2));
                    break;

                default:
                    abort(400, "Tipo de item no soportado, no puede ser anulado... ");
                    break;
            }

	        $monto_a_modificar = $partida->partida_antecedente->saldo + $partida->importe;
            $consulta = "'Pago 327681: id_item = ". $partida->partida_antecedente->id_item." saldo  = ". $partida->partida_antecedente->saldo." cambio a ".$monto_a_modificar."'";
            $partida->partida_antecedente->update([
                'saldo' => $monto_a_modificar
            ]);
            $this->crearLogRespaldo($consulta);
            dd("FIN 327681");
        }

        $monto_a_modificar = $pagado_anticipo->saldo + (-$pago->monto);
        $consulta = "'Pago 327681:editar transaccion: id_transaccion = " . $pago_a_desaplicar->id_transaccion . " saldo = " . $pago_a_desaplicar->saldo . " cambio a " . $monto_pagado . "'";
        $pago_a_desaplicar->update([
            'saldo'  => $monto_a_modificar,
            'estado' => 1
        ]);
        $this->crearLogRespaldo($consulta);

        dd($pago_a_desaplicar->transaccionesRelacionadas, $pago_a_desaplicar->id_antecedente);
        $monto_a_modificar = $pago_a_desaplicar->transaccionesRelacionadas->saldo +  (-$pago->monto);
        $consulta = "'Pago 327681:editar transaccion: id_transaccion = " . $pago_a_desaplicar->transaccionesRelacionadas->id_transaccion . " saldo = " . $pago_a_desaplicar->transaccionesRelacionadas->saldo . " cambio a " . $monto_pagado . "'";
        $pago_a_desaplicar->transaccionesRelacionadas->update([
            'saldo'  => $monto_a_modificar,
            'estado' => 1
        ]);
        $this->crearLogRespaldo($consulta);
        dd("PASO");
    }

    /**
     * Ejecuta lógica: sp_desaplicar_pago
     * Validaciones para pagos tipo estimación
     * @param $estimacion
     */
    private function validacionEstimacionPago(Estimacion $estimacion)
    {
        if ($estimacion->estado != 2) {
            abort(400, "Error la estimación: " . $estimacion->numero_folio_format . " no se encuentra como Revisada.");
        }

        /**
         * Editar el estado de la estimación
         */
        $consulta = "'Pago 327681: editar estimación: id_referente = " . $estimacion->id_transaccion . " estado = " . $estimacion->estado . " cambio a 1'";
        $estimacion->update([
            'estado' => 1
        ]);
        $this->crearLogRespaldo($consulta);
        $consulta = "'Pago 327681: editar estimación: id_transaccion = " . $estimacion->id_transaccion . " estado = " . $estimacion->estado . " cambio a 0, impreso 0, saldo = monto" . $estimacion->monto . "'";
        $estimacion->revertir_estimacion();
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

    private function pagadoAnticipo($factura)
    {
        /**
         * checar que haga esto:
         *  SELECT @pagado_anticipo = @pagado_anticipo - COALESCE(SUM(importe-saldo),0)
        FROM ItemsFacturados
        WHERE ItemsFacturados.id_item = @id_item_remision
         */
        $suma = 0;
        foreach ($factura->items as $partida)
        {
            $suma += ($partida->importe - $partida->saldo);
        }
        return $suma;
    }

    private function recalculosInventario($id_item, $monto_a_restar)
    {
        $inventario = Inventario::where('id_item', '=', $id_item)->first();
        dd($inventario);
        if ($inventario)
        {
            $monto_pagado =  $inventario->monto_pagado - $monto_a_restar;
            $consulta = "'Pago 327681: editar inventario: id_lote = " . $inventario->id_lote . " monto_pagado = " . $inventario->monto_pagado . " cambio a " . $monto_pagado . "'";
            $inventario->cambiarMontoPagado($monto_pagado);
            $this->crearLogRespaldo($consulta);
        }
    }

    private function recalculosMovimiento($id_item, $monto_a_restar)
    {
        $movimiento = Movimiento::where('id_item', '=', $id_item)->first();
        dd($movimiento);
        if($movimiento)
        {
            $monto_pagado = $movimiento->monto_pagado - $monto_a_restar;
            $consulta = "'Pago 327681: tipo 0 - editar movimiento: id_movimiento = " . $movimiento->id_movimiento . " monto_pagado = " . $movimiento->monto_pagado . " cambio a " . $monto_pagado . "'";
            $movimiento->update([
                'monto_pagado' => $monto_pagado
            ]);
            $this->crearLogRespaldo($consulta);
        }
        /*
        $inventario = Inventario::where('id_item', '=', $partida->partida_antecedente->item_antecedente)->first();
        dd($inventario, $partida->partida_antecedente->item_antecedente);
        if ($inventario)
        {
            $monto_a_modificar = $inventario->monto_pagado -  ROUND($partida->importe * $pago_a_desaplicar->tipo_cambio, 2);
            $consulta = "'Pago 327681: tipo 0 - editar inventario: id_lote = " . $inventario->id_lote . " monto_pagado = " . $inventario->monto_pagado . " cambio a " . $monto_a_modificar . "'";
            $inventario->update([
                'monto_pagado' => $monto_a_modificar
            ]);
            $this->crearLogRespaldo($consulta);

            //ejecuta sp_distribuir_pagado_inventarios
            $inventario->distribuirPagoInventarios();
        } else {
            $movimiento = Movimiento::where('id_item', '=', $partida->partida_antecedente->item_antecedente)->first();
            if($movimiento)
            {
                $monto_a_modificar = $movimiento->monto_pagado - $partida->partida_antecedente->importe;
                $consulta =  "'Pago 327681: tipo 0 - editar movimiento: id_movimiento = " .  $movimiento->id_movimiento. " monto_pagado = " .  $movimiento->monto_pagado . " cambio a " . $monto_a_modificar . "'";
                $movimiento->update([
                    'monto_pagado' => $monto_a_modificar
                ]);
                $this->crearLogRespaldo($consulta);
            }else{
                dd("aqui??");
            }
        }
        */
    }
}
