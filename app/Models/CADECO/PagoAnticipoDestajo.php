<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:12 AM
 */

namespace App\Models\CADECO;

use Illuminate\Support\Facades\DB;

class PagoAnticipoDestajo extends Pago
{
    public const TIPO_ANTECEDENTE = 72;
    public const OPCION_ANTECEDENTE = 131073;

    protected $fillable = [
        'id_antecedente',
        'id_referente',
        'fecha',
        'estado',
        "id_cuenta",
        "id_empresa",
        "id_moneda",
        'cumplimiento',
        'vencimiento',
        'monto',
        "saldo",
        "tipo_cambio",
        'referencia',
        "destino",
    ];
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 82)
                ->where('opciones', '=', 131073)
                ->where('estado', '!=', -2);
        });
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudAnticipoDestajo::class, 'id_antecedente', 'id_transaccion');
    }

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'id_referente', 'id_transaccion');
    }

    public function anticipoTransaccion()
    {
        return $this->hasOne(Anticipo::class, "id_antecedente", "id_transaccion");
    }

    public function generaAnticipo()
    {
        $anticipo = $this->anticipoTransaccion;
        if($anticipo){
            return $anticipo;
        }else{
            DB::connection('cadeco')->beginTransaction();
            $datos_anticipo = array(
                "id_antecedente" => $this->id_transaccion,
                "id_referente" => $this->id_referente,
                "fecha" => $this->fecha,
                "estado" => 2,
                "id_empresa" =>  $this->id_empresa,
                "id_moneda" =>  $this->id_moneda,
                "monto" => $this->monto,
                "saldo" => $this->saldo,
            );
            $anticipo = $this->anticipoTransaccion()->create($datos_anticipo);
            $this->validaAnticipo($anticipo);
            DB::connection('cadeco')->commit();
            return $anticipo;
        }
    }

    private function validaAnticipo(Anticipo $anticipo)
    {
        if(!$anticipo){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante el registro del anticipo');
        }
    }

    /**
     * Implementa lógica de SP borra_transacción para eliminar pagos
     */
    public function recalculaAnticipo()
    {
        if ($this->transaccion->opciones < 65536)
        {
            if($this->anticipoTransaccion)
            {
                $saldo = $this->transaccion->anticipo_saldo - $this->anticipoTransaccion->saldo;
                $consulta = "'Pago 131073: Pago anticipado destajo edita transaccion id_referente".$this->id_referente." anticipo_saldo = ".$this->transaccion->anticipo_saldo." cambio a ".$saldo."'";
                $this->transaccion->update([
                    'anticipo_saldo' => $saldo
                ]);
                $this->crearLogRespaldo($consulta);
                $consulta = "'Pago 131073: Pago anticipado destajo elimina anticipo id_transaccion".$this->anticipoTransaccion->id_transaccion."'";
                $this->anticipoTransaccion->delete();
                $this->crearLogRespaldo($consulta);
            }
        }
        else{
            $saldo = $this->transaccion->anticipo_saldo - $this->monto;
            $consulta = "'Pago 131073: Pago anticipado destajo edita transaccion id_referente".$this->id_referente." anticipo_saldo = ".$this->transaccion->anticipo_saldo." cambio a ".$saldo."'";
            $this->transaccion->update([
                'anticipo_saldo' => $saldo
            ]);
        }
    }

    public function ajustarOC()
    {
        if($this->transaccion->where('tipo_transaccion', '=', 19)->where('estado', '=', 1)->first())
        {
            if(is_null(Transaccion::where('id_antecedente', '=', $this->id_referente)->first()) && is_null(Transaccion::where('id_referente', $this->id_referente)->first()))
            {
                $this->transaccion->update([
                    'estado' => 0
                ]);
            }
        }
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
}
