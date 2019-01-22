<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 17/01/19
 * Time: 08:11 PM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolizaValido extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_polizas_valido';

    protected $fillable = [
        'id_int_poliza',
        'valido',
    ];
}