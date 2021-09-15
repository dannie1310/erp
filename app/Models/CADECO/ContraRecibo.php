<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:08 AM
 */

namespace App\Models\CADECO;


class ContraRecibo extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;
    protected $fillable = [
        'tipo_transaccion',
        'fecha',
        "id_empresa",
        "id_moneda",
        'monto',
        "saldo",
        "observaciones",
    ];
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 67)
                ->where('opciones', '=', 0);
        });
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'id_antecedente', 'id_transaccion');
    }

    public function notasCredito()
    {
        return $this->hasMany(NotaCredito::class, 'id_antecedente', 'id_transaccion');
    }

    public function disminuyeSaldo(Transaccion $pago){
        $this->saldo = number_format($this->saldo - ($pago->orden_pago->monto * -1),2,".","");
        $this->estado = 1;
        $this->save();
        if($this->saldo<1){
            $this->actualizaEstadoPagada();
        }
    }

    public function actualizaEstadoPagada(){
        $this->estado = 2;
        $this->save();
    }
}
