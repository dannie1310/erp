<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 10/12/18
 * Time: 06:27 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Sistema;


class Proyecto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'proyectos';
    public $timestamps = false;

    public function sistemas()
    {
        return $this->belongsToMany(Sistema::class,  'dbo.proyectos_sistemas', 'id_proyecto', 'id_sistema');
    }
}