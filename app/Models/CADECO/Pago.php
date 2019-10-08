<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:10 AM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Moneda;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Cuenta;

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
                ->where('opciones', '=', 0)
                ->where('estado', '!=', -2);
        });
    }
    public function moneda(){
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function cuenta(){
        return $this->hasOne(Cuenta::class, 'id_cuenta', 'id_cuenta');
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function verificaPago($data)
    {
        $pago = Pago::query()->where('numero_folio','=', $data['numero_folio'])->get()->first();

        if(is_null($pago)){
            $datos = [
                'numero_folio' => $data['numero_folio'],
                'fecha'=>$data['fecha'],
                'monto'=>$data['monto'],
                'id_empresa'=>$data['id_empresa'],
                'observaciones'=>$data['observaciones'],
                'id_moneda'=>$data['id_moneda'],
            ];

            $pago = Pago::query()->create($datos);
            return $pago;

        }else{
            return $pago;
        }



    }
}
