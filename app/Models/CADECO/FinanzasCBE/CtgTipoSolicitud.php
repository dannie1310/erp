<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:07 PM
 */

namespace App\Models\CADECO\FinanzasCBE;


use Illuminate\Database\Eloquent\Model;

class CtgTipoSolicitud extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'FinanzasCBE.ctg_tipos_solicitud';
    public $timestamps = false;
}