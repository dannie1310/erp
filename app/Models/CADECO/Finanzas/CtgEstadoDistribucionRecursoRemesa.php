<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 07:19 PM
 */

namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgEstadoDistribucionRecursoRemesa extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.ctg_estado_distribucion_recursos_rem';
}