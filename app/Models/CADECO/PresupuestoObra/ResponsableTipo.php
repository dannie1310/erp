<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 17/12/18
 * Time: 07:13 PM
 */

namespace App\Models\CADECO\PresupuestoObra;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Scopes\ObraScope;
use Illuminate\Database\Eloquent\Model;

class ResponsableTipo extends Model
{
    protected $connection = 'cadeco';
    protected $primaryKey = 'tipo';
    protected $fillable = [
        'tipo',
        'descripcion',
    ];

    protected $table = 'PresupuestoObra.responsables_tipo';

}
