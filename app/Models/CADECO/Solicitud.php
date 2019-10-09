<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/10/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO;


class Solicitud extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 72);
        });
    }

    public function pago(){
        return $this->belongsTo(Transaccion::class, 'id_antecedente', 'id_transaccion')
            ->where('tipo_transaccion', '=', 82);
    }

    public function fondo(){
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }

    public function verificaPago($data){
        $pago = Pago::query()->where('id_referente','=', $data['id_referente'])
            ->where('id_empresa','>',0)->get()->first();


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
