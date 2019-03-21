<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 01/03/2019
 * Time: 04:46 PM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class NaturalezaPoliza extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.naturaleza_poliza';
    protected $primaryKey = 'id_naturaleza_poliza';
    public  $timestamps = false;

}