<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 13/03/2020
 * Time: 12:20 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use Illuminate\Database\Eloquent\Model;

class ConceptoTarjeta extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.concepto_tarjeta';
    protected $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

}