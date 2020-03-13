<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 13/03/2020
 * Time: 12:20 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use Illuminate\Database\Eloquent\Model;

class TipoOrden extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.tipo_orden';
    protected $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('estatus', '=', 1);
        });
    }
}