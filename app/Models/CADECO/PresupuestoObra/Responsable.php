<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 17/12/18
 * Time: 07:13 PM
 */

namespace App\Models\CADECO\Seguridad;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Scopes\ObraScope;
use Illuminate\Database\Eloquent\Model;

class Responsable extends Model
{
    protected $connection = 'cadeco';
    protected $primaryKey = 'id_responsable';
    protected $fillable = [
        'id_usuario_responsable',
        'id_concepto',
        'id_usuario_asigno',
        'fecha_hora_asignacion',
        'tipo',
    ];

    protected $table = 'PresupuestoObra.responsables';

}
