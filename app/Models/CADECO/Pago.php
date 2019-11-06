<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:10 AM
 */

namespace App\Models\CADECO;

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
    public function moneda(){
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function cuenta(){
        return $this->hasOne(Cuenta::class, 'id_cuenta', 'id_cuenta');
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
}
