<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/12/18
 * Time: 11:48 AM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistPoliza extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.hist_int_polizas';
    protected $primaryKey = 'id_hist_int_poliza';
}