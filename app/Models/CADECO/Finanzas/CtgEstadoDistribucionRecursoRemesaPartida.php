<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 07:21 PM
 */

namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgEstadoDistribucionRecursoRemesaPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.ctg_estado_distribucion_recursos_rem_partidas';
    protected $primaryKey = 'id';
}