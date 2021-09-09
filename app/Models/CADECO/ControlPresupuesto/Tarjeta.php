<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 13/03/2020
 * Time: 12:20 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use App\Facades\Context;
use App\Models\CADECO\Concepto;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ControlPresupuesto\ConceptoTarjeta;

class Tarjeta extends Concepto
{

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra())->where('concepto_medible', '=', 3);
        });
    }

    // public function conceptostarjeta(){
    //     return $this->hasMany(ConceptoTarjeta::class, 'id', 'id_tarjeta');
    // }
}