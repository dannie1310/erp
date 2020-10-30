<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 17/12/18
 * Time: 07:13 PM
 */

namespace App\Models\CADECO\PresupuestoObra;

use Illuminate\Database\Eloquent\Model;

class DatoConcepto extends Model
{
    protected $connection = 'cadeco';
    protected $primaryKey = 'id_concepto';
    protected $fillable = [
        'calificacion',
        'fecha_inicio',
        'fecha_fin',
        'revision_diaria',
        'revision_semanal',
        'revision_mensual',
        'fecha_hora_registro',
        'id_usuario_registro',
    ];

    protected $table = 'PresupuestoObra.datos_concepto';

}
