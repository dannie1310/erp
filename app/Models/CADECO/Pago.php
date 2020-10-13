<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:10 AM
 */

namespace App\Models\CADECO;

use App\Facades\Context;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Models\CADECO\Finanzas\PagoEliminado;
use App\Models\CADECO\Finanzas\PagoEliminadoLog;
use Illuminate\Support\Facades\DB;

class Pago extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 82;
    public const NOMBRE = "Pago";
    public const ICONO = "fa fa-hand-holding-usd";

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

    public function ordenPago()
    {
        return $this->belongsTo(OrdenPago::class, 'numero_folio', 'numero_folio');
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

    public function distribucionPartida()
    {
        return $this->belongsTo(DistribucionRecursoRemesaPartida::class, 'id_transaccion', 'id_transaccion_pago');
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

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = Pago::NOMBRE;
        $datos["tipo_numero"] = Pago::TIPO;
        $datos["icono"] = Pago::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }
    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        #PAGO
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;
        if($this->ordenPago){
            if($this->ordenPago->factura){
                $factura = $this->ordenPago->factura;
                foreach($factura->relaciones as $relacion){
                    if($relacion["tipo_numero"]!=82){
                        $relaciones[$i]=$relacion;
                        $relaciones[$i]["consulta"] = 0;
                        $i++;
                    }
                }
            }
        }

        $orden1 = array_column($relaciones, 'orden');

        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function eliminar($motivo)
    {
        try {
            $this->validarEliminacion();
            DB::connection('cadeco')->beginTransaction();
            $this->respaldar($motivo);

            if($this->opciones == 1 && $this->estado == 1)  //Pago varios o Reposicion FF
            {
                $fondo = Fondo::where('id_fondo', '=', $this->id_referente)->first();
                $saldo_a_modificar = $fondo->saldo + $this->monto;
                $consulta = "'Pago (1) : id_fondo = " . $fondo->id_fondo . " saldo = " . $fondo->saldo . " monto= " . $this->monto . " saldo(cambio)= " . $saldo_a_modificar . "'";
                $fondo->saldo = $saldo_a_modificar;
                $fondo->save();
                $this->crearLogRespaldo($consulta);
            }

            if($this->opciones == 65537 && $this->estado == 1) {
                $saldo_a_modificar = $this->transaccionReferente->saldo - $this->monto;
                $consulta = "'Pago 65537: id_referente = " . $this->id_referente . " saldo = " . $this->transaccionReferente->saldo . " monto= " . $this->monto . " saldo(cambio)= " . $saldo_a_modificar . " estado = " . $this->transaccionReferente->estado . " cambio a 1'";
                $this->transaccionReferente->saldo = $saldo_a_modificar;
                $this->transaccionReferente->estado = 1;
                $this->transaccionReferente->save();
                $this->crearLogRespaldo($consulta);

                if ($this->transaccionReferente->tipo_transaccion == 99) //Lista de Raya
                {
                    $lista_raya = ListaRaya::where('id_transaccion', '=', $this->id_referente)->first();
                    foreach ($lista_raya->items as $item) {
                        $monto_a_modificar = ROUND($item->inventario->monto_pagado - $item->importe * (-$this->monto / $lista_raya->monto), 2);
                        $item->inventario->monto_pagado = $monto_a_modificar;
                        $item->inventario->save();
                        $this->crearLogRespaldo("'Pago 65537: listaraya (" . $lista_raya->id_transaccion . ") editado id_inventario" . $item->inventario->id_lote . " monto_pagado = " . $item->inventario->monto_pagado . " importe_lista_raya = " . $lista_raya->importe . " monto_pagado = " . $monto_a_modificar . "'");
                    }
                }
                if ($this->transaccionReferente->tipo_transaccion == 102) //Prestación
                {
                    $prestacion = Prestacion::where('id_transaccion', '=', $this->id_referente)->first();
                    foreach ($prestacion->items as $item) {
                        $monto_a_modificar = ROUND($item->inventario->monto_pagado - $item->importe * (-$this->monto / $prestacion->monto), 2);
                        $item->inventario->monto_pagado = $monto_a_modificar;
                        $item->inventario->save();
                        $this->crearLogRespaldo("'Pago 65537: Prestación (" . $prestacion->id_transaccion . ") editado id_inventario" . $item->inventario->id_lote . " monto_pagado = " . $item->inventario->monto_pagado . " importe_lista_raya = " . $prestacion->importe . " monto_pagado = " . $monto_a_modificar . "'");
                    }
                }
            }

            if($this->opciones == 131073 && $this->estado == 1) //Pago anticipo destajo
            {
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
            }

            if($this->opciones == 262145)
            {
                //reactivar el cheque
                $consulta = "'Pago 262145: id_referente = ".$this->id_referente." estado = ".$this->transaccionReferente->estado." cambio a 1'";
                $this->transaccionReferente->estado = 1;
                $this->transaccionReferente->save();
                $this->crearLogRespaldo($consulta);
            }

            if($this->opciones == 327681 && $this->estado > 0) // Pago a cuenta o a cuenta por aplicar
            {
                $this->desaplicarCheque();
            }
            /**
             * Elimina Pago...
             */
            $this->delete();

            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
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
                $orden_pago->respaldar("Eliminar-orden pago...");
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

        if($this->estado == 1)
        {
            $this->update([
                'saldo' => $this->monto,
                'opciones' => 327681
            ]);
        }
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

        if ($pago->transaccionReferente->tipo_transaccion == 52)
        {
            $this->validacionEstimacionPago(Estimacion::find($pago->id_referente)->first());
        }

        //inicia validaciones con orden pago
        if (is_null($pago->items))
        {
            abort(400, "Imposible determinar los items de la orden de pago, no puede ser anulado.");
        }

        foreach ($pago->items as $partida_pago) {

            if($partida_pago->partida_antecedente->numero < 0 || $partida_pago->partida_antecedente->numero > 7)
            {
                abort(400, "Error al validar tipo de item no soportado, no puede ser anulado.");
            }

            if($partida_pago->partida_antecedente->numero == 0) {
                // actualizamos el monto pagado del lote original
                // atencion, falta el tipo_cambio del registro 68
                $this->recalculosInventario($partida_pago->partida_antecedente->item_antecedente, ROUND($partida_pago->importe * $pago->transaccionReferente->tipo_cambio, 2));
                $this->recalculosMovimiento($partida_pago->partida_antecedente->item_antecedente, $partida_pago->partida_antecedente->importe);

                $monto_a_modificar = $partida_pago->partida_antecedente->partida_antecedente->saldo + ROUND($partida_pago->importe * $pago->transaccionReferente->tipo_cambio, 2);
                $consulta = "'Pago 327681: tipo 0 - editar item antecedente: id_item = " . $partida_pago->partida_antecedente->partida_antecedente->id_item . " saldo = " . $partida_pago->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                $partida_pago->partida_antecedente->partida_antecedente->saldo = $monto_a_modificar;
                $partida_pago->partida_antecedente->partida_antecedente->save();
                $this->crearLogRespaldo($consulta);
            }
            elseif ($partida_pago->partida_antecedente->numero == 1) {
                $transaccion_antecedente = $partida_pago->partida_antecedente->transaccionAntecedente;

                if ($transaccion_antecedente->tipo_transaccion == 51) {
                    $movimientos = Movimiento::join('items', 'items.id_item', 'movimientos.id_item')
                        ->join('EstimacionesSubcontratos', 'EstimacionesSubcontratos.id_transaccion', 'items.id_transaccion')
                        ->where('EstimacionesSubcontratos.id_antecedente', '=', $partida_pago->partida_antecedente->id_antecedente)->get();

                    foreach ($movimientos as $movimiento) {
                        $monto_a_modificar = $movimiento->monto_pagado - ROUND($movimiento->monto_total * ($partida_pago->importe / $transaccion_antecedente->anticipo_monto) * ((100 - $movimiento->retencion) / 100 - ($movimiento->monto - $movimiento->impuesto) / $movimiento->suma_importes), 2);
                        $consulta = "'Pago 327681: tipo 1 - editar movimiento (transaccion: " . $transaccion_antecedente->id_transaccion . " ) : id_movimiento = " . $movimiento->id_movimiento . " monto_pagado = " . $movimiento->monto_pagado . " cambio a " . $monto_a_modificar . "'";
                        $movimiento->update([
                            'monto_pagado' => $monto_a_modificar
                        ]);
                        $this->crearLogRespaldo($consulta);
                    }
                }
                if ($transaccion_antecedente->tipo_transaccion == 52) {
                    $monto_total = $this->monto_total($transaccion_antecedente);
                    if ($monto_total > 0) {
                        foreach ($transaccion_antecedente->items as $partida_estimacion) {
                            $monto_a_modificar = ROUND(($partida_estimacion->movimiento->monto_pagado - $partida_estimacion->movimiento->monto_total * $partida_pago->importe * $pago->transaccionReferente->tipo_cambio / $monto_total), 2);
                            $consulta = "'Pago 327681: tipo 1 - editar movimiento(estimación - " . $partida_estimacion->id_transaccion . "): id_movimiento = " . $partida_estimacion->movimiento->id_movimiento . " monto_pagado = " . $partida_estimacion->movimiento->monto_pagado . " cambio a " . $monto_a_modificar . "'";
                            $partida_estimacion->movimiento->update([
                                'monto_pagado' => $monto_a_modificar
                            ]);
                            $this->crearLogRespaldo($consulta);
                        }
                    }
                }
            }
            elseif ($partida_pago->partida_antecedente->numero == 2)
            {
                $transaccion_antecedente = $partida_pago->partida_antecedente->transaccionAntecedente;
                if ($transaccion_antecedente->tipo_transaccion == 19 && in_array($transaccion_antecedente->opciones, [1, 65537, 327681, 65535])) {
                    if ($transaccion_antecedente->opciones == 65535) {
                        $monto_a_modificar = $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->saldo + $partida_pago->importe;
                        $consulta = "'Pago 327681: tipo 2 - editar item de tipo orden compra(" . $transaccion_antecedente->id_transaccion . "): id_item = " . $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->id_item . " saldo = " . $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                        // actualizacion de la remision
                        $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->saldo = $monto_a_modificar;
                        $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->save();
                        $this->crearLogRespaldo($consulta);

                        // actualizacion de la Orden de Compra
                        $monto_a_modificar = $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->importe - $partida_pago->importe;
                        $consulta = "'Pago 327681: tipo 2 - editar item de tipo orden compra(" . $transaccion_antecedente->id_transaccion . "): id_item = " . $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->id_item . " importe = " . $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->importe . " cambio a " . $monto_a_modificar . "'";
                        $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->importe = $monto_a_modificar;
                        $partida_pago->partida_antecedente->partida_antecedente->partida_antecedente->partida_antecedente->save();
                        $this->crearLogRespaldo($consulta);
                        $this->recalculosInventario($partida_pago->partida_antecedente->partida_antecedente->item_antecedente, $partida_pago->importe);
                    } else {
                        $acumulado = 0;
                        $anticipo_aplicado = $this->anticipoAplicado($partida_pago->importe, $transaccion_antecedente);
                        $remisiones = Item::join('transacciones', 'items.id_transaccion', 'transacciones.id_transaccion')
                            ->where('transacciones.tipo_transaccion', '=', 33)
                            ->where('items.item_antecedente', '=', $partida_pago->partida_antecedente->item_antecedente)
                            ->where('transacciones.id_obra', '=', Context::getIdObra())
                            ->where('items.anticipo', '>', 0)
                            ->orderBy('numero_folio', 'desc')->get();

                        foreach ($remisiones as $remision) {
                            $pagado_anticipo = ($remision->importe - $remision->saldo);
                            if ($anticipo_aplicado > 0) {
                                $factura = Factura::whereHas('items')->where('id_item', '=', $remision->id_item)->first();
                                $pagado_anticipo = $pagado_anticipo - $this->pagadoAnticipo($factura);

                                if ($pagado_anticipo > ($anticipo_aplicado - $acumulado)) {
                                    $pagado_anticipo = $anticipo_aplicado - $acumulado;
                                }

                                $monto_a_modificar = $remision->saldo + $pagado_anticipo;
                                $consulta = "'Pago 327681: tipo 2 - editar item remisión (transaccion: " . $remision->id_transaccion . " ) : id_item = " . $remision->id_item . " saldo = " . $remision->saldo . " cambio a " . $monto_a_modificar . " pago_anticipo_calculado:'" . $pagado_anticipo . "'";
                                $remision->saldo = $monto_a_modificar;
                                $remision->estado = 0;
                                $remision->save();
                                $this->crearLogRespaldo($consulta);
                                $this->recalculosInventario($remision->id_item, $pagado_anticipo);
                                $this->recalculosMovimiento($remision->id_item, $pagado_anticipo);

                                /**
                                 * Acumular el anticipo amortizado en este pago
                                 */
                                $acumulado = $acumulado + $pagado_anticipo;
                            }
                        }
                        $monto_a_modificar = $partida_pago->partida_antecedente->partida_antecedente->saldo + $acumulado;
                        $consulta = "'Pago 327681: tipo 2 - editar item: id_item = " . $partida_pago->partida_antecedente->partida_antecedente->id_item . " saldo = " . $partida_pago->partida_antecedente->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . " acumulado:'" . $acumulado . "'";
                        $partida_pago->partida_antecedente->partida_antecedente->saldo = $monto_a_modificar;
                        $partida_pago->partida_antecedente->partida_antecedente->save();
                        $this->crearLogRespaldo($consulta);
                    }
                } else if ($transaccion_antecedente->tipo_transaccion == 19) {
                    $inventarios = Inventario::join('items', 'items.id_item', 'inventarios.id_item')
                        ->join('transacciones', 'transacciones.id_transaccion', 'items.id_transaccion')
                        ->where('item_antecedente', '=', $partida_pago->partida_antecedente->item_antecedente)
                        ->where('tipo_transaccion', '=', 33)->get();

                    $numero_total = $inventarios->sum('inventarios.numero');
                    if ($numero_total > 0) {
                        foreach ($inventarios as $inventario) {
                            $monto_a_modificar = $inventario->monto_anticipo - ROUND($partida_pago->importe * $inventario->numero / $numero_total, 2);
                            $consulta = "'Pago 327681: tipo 2 - editar inventario: id_lote = " . $inventario->id_lote . " monto_aplicado = " . $inventario->monto_aplicado . " cambio a " . $monto_a_modificar . "'";
                            $inventario->monto_aplicado = $monto_a_modificar;
                            $inventario->save();
                            $this->crearLogRespaldo($consulta);
                        }

                        $monto_a_modificar = $partida_pago->partida_antecedente->partida_antecedente->saldo + $partida_pago->importe;
                        $consulta = "'Pago 327681: tipo 2 - editar item: id_item = " . $partida_pago->partida_antecedente->partida_antecedente->id_item . " saldo = " . $partida_pago->partida_antecedente->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                        $partida_pago->partida_antecedente->partida_antecedente->saldo = $monto_a_modificar;
                        $partida_pago->partida_antecedente->partida_antecedente->save();
                        $this->crearLogRespaldo($consulta);
                    }
                } else {
                    $inventario = Inventario::where('id_item', '=', $partida_pago->partida_antecedente->partida_antecedente->item_antecedente)->first();

                    if ($inventario) {
                        $monto_pagado = $inventario->monto_pagado - $partida_pago->importe;
                        $monto_anticipo = $inventario->monto_anticipo - $partida_pago->importe;
                        $consulta = "'Pago 327681: tipo 2 - editar inventario: id_lote = " . $inventario->id_lote . " monto_pagado = " . $inventario->monto_pagado . " cambio a " . $monto_pagado . "  monto_anticipo = " . $inventario->monto_anticipo . "cambio a " . $monto_anticipo . "'";
                        $inventario->monto_pagado = $monto_pagado;
                        $inventario->monto_anticipo = $monto_anticipo;
                        $inventario->save();
                        $inventario->distribuirPagoInventarios();
                        $this->crearLogRespaldo($consulta);
                    }
                    $monto_a_modificar = $partida_pago->partida_antecedente->partida_antecedente->saldo - $partida_pago->importe;
                    $consulta = "'Pago 327681: tipo 2 - editar item: id_item = " . $partida_pago->partida_antecedente->partida_antecedente->id_item . " saldo = " . $partida_pago->partida_antecedente->partida_antecedente->saldo . " cambio a " . $monto_a_modificar . "'";
                    $partida_pago->partida_antecedente->partida_antecedente->saldo = $monto_a_modificar;
                    $partida_pago->partida_antecedente->partida_antecedente->save();
                    $this->crearLogRespaldo($consulta);
                }
                //actualiza el saldo de la O/C u O/R
                if ($partida_pago->partida_antecedente->partida_antecedente->transaccion->tipo_transaccion == 19) {
                    $monto_a_modificar = $partida_pago->partida_antecedente->partida_antecedente->transaccion->anticipo_saldo + ROUND($partida_pago->importe * ($pago->transaccionReferente->monto / ($pago->transaccionReferente->monto - $pago->transaccionReferente->impuesto)), 2);
                    $consulta = "'Pago 327681: tipo 2 - editar transaccion: id_transaccion = " . $partida_pago->partida_antecedente->partida_antecedente->transaccion->id_transaccion . " anticipo_saldo = " . $partida_pago->partida_antecedente->partida_antecedente->transaccion->anticipo_saldo . " cambio a " . $monto_a_modificar . "'";
                    $partida_pago->partida_antecedente->partida_antecedente->transaccion->anticipo_saldo = $monto_a_modificar;
                    $partida_pago->partida_antecedente->partida_antecedente->transaccion->save();
                    $this->crearLogRespaldo($consulta);
                }
            }
            elseif ($partida_pago->partida_antecedente->numero == 3)
            {
                $this->recalculosInventario($partida_pago->partida_antecedente->item_antecedente, ROUND($partida_pago->importe * $pago->transaccionReferente->tipo_cambio, 2));
            }
            elseif ($partida_pago->partida_antecedente->numero == 4)
            {
                $lista_raya = ListaRaya::find($partida_pago->partida_antecedente->id_antecedente)->first();
                if ($lista_raya) {
                    $lista_raya->desaplicaPago();
                    $this->crearLogRespaldo("'Pago 327681: tipo 4 - editar inventario (listaRaya: " . $lista_raya->id_transaccion . " )todos los monto_pagado cambio a cero.'");
                } else {
                    $prestacion = Prestacion::find($partida_pago->partida_antecedente->id_antecedente)->first();
                    if ($prestacion) {
                        $factor = $partida_pago->partida_antecedente->importe / $prestacion->monto;
                        $prestacion->desaplicaPago($factor);
                        $this->crearLogRespaldo("'Pago 327681: tipo 4 - editar inventario (prestacion: " . $prestacion->id_transaccion . " )todos los monto_pagado se recalcula.");
                    }
                }
            }
            elseif ($partida_pago->partida_antecedente->numero == 7)
            {
                $this->recalculosInventario($partida_pago->item_antecedente, ROUND($partida_pago->importe * $pago->transaccionReferente->tipo_cambio, 2));
                $this->recalculosMovimiento($partida_pago->item_antecedente, ROUND($partida_pago->importe * $pago->transaccionReferente->tipo_cambio, 2));
            }

	        $monto_a_modificar = $partida_pago->partida_antecedente->saldo + $partida_pago->importe;
            $consulta = "'Pago 327681: id_item = ". $partida_pago->partida_antecedente->id_item." saldo  = ". $partida_pago->partida_antecedente->saldo." cambio a ".$monto_a_modificar."'";
            $partida_pago->partida_antecedente->saldo = $monto_a_modificar;
            $partida_pago->partida_antecedente->save();
            $this->crearLogRespaldo($consulta);
        }

        //Factura
        $monto_a_modificar =  $pago->transaccionReferente->saldo + (-$pago->monto);
        $consulta = "'Pago 327681:editar transaccion(Fatura): id_transaccion = " . $pago->transaccionReferente->id_transaccion . " saldo = " . $pago->transaccionReferente->saldo . " cambio a " . $monto_a_modificar . "'";
        $pago->transaccionReferente->saldo = $monto_a_modificar;
        $pago->transaccionReferente->estado = 1;
        $pago->transaccionReferente->save();
        $this->crearLogRespaldo($consulta);

        //Contrarecibo
        $contrarecibos = ContraRecibo::where('id_transaccion', '=', $pago->transaccionReferente->id_antecedente)->get();
        foreach ($contrarecibos as $contrarecibo)
        {
            $monto_a_modificar = $contrarecibo->saldo +  (-$pago->monto);
            $consulta = "'Pago 327681:editar transaccion(Contrarecibo): id_transaccion = " . $contrarecibo->id_transaccion . " saldo = " . $contrarecibo->saldo . " cambio a " . $monto_a_modificar . "'";
            $contrarecibo->saldo =  $monto_a_modificar;
            $contrarecibo->estado = 1;
            $contrarecibo->save();
            $this->crearLogRespaldo($consulta);
        }
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
        $estimacion->estado = 1;
        $estimacion->save();
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
        if($movimiento)
        {
            $monto_pagado = $movimiento->monto_pagado - $monto_a_restar;
            $consulta = "'Pago 327681: tipo 0 - editar movimiento: id_movimiento = " . $movimiento->id_movimiento . " monto_pagado = " . $movimiento->monto_pagado . " cambio a " . $monto_pagado . "'";
            $movimiento->monto_pagado = $monto_pagado;
            $movimiento->save();
            $this->crearLogRespaldo($consulta);
        }
    }
}
