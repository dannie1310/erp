<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 23/07/2019
 * Time: 07:19 PM
 */

namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgTipoFondo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.ctg_tipos_fondos';

    public function scopeTipoFondoActivo($query)
    {
        return $query->where('estado','=',1);
    }
}