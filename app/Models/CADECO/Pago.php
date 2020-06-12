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
            $this->validarEliminacion();
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
                    PagoEliminadoLog::create([
                        'id_transaccion' => $this->id_transaccion,
                        'consulta' => $consulta
                    ]);

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
                            PagoEliminadoLog::create([
                                'id_transaccion' => $this->id_transaccion,
                                'consulta' => "'Pago 65537: listaraya (".$lista_raya->id_transaccion.") editado id_inventario"  . $item->inventario->id_lote . " monto_pagado = " . $item->inventario->monto_pagado . " importe_lista_raya = " . $lista_raya->importe . " monto_pagado = " . $monto_a_modificar . "'"
                            ]);
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
                            PagoEliminadoLog::create([
                                'id_transaccion' => $this->id_transaccion,
                                'consulta' => "'Pago 65537: otro (".$referente->id_transaccion.") editado id_inventario"  . $inventario->id_lote . " monto_pagado = " . $inventario->monto_pagado . " importe_lista_raya = " . $referente->importe . " monto_pagado = " . $monto_a_modificar . "'"
                            ]);
                        }
                    }
                    $this->delete();
                    break;

                case 131073: //Pago anticipo destajo
                    echo "131073";
                    break;

                case 262145;
                    //reactivar el cheque
                    $cheque = Transaccion::withoutGlobalScopes()->where('id_transaccion', '=', $this->id_referente)->where('id_obra', '=', Context::getIdObra())->first();
                    $consulta = "'Pago 262145: id_referente = ".$this->id_referente." estado = ".$cheque->estado." cambio a 1'";
                    $cheque->update([
                        'estado' => 1
                    ]);
                    PagoEliminadoLog::create([
                        'id_transaccion' => $this->id_transaccion,
                        'consulta' => $consulta
                    ]);
                    $this->delete();
                    break;

                case 327681; // Pago a cuenta o a cuenta por aplicar
                    echo "327681";
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
}
