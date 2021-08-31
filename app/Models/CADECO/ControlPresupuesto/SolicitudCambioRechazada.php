<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 24/03/2020
 * Time: 09:38 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;

use Illuminate\Database\Eloquent\Model;

class SolicitudCambioRechazada extends Model
{
    protected $table = 'ControlPresupuesto.solicitudes_cambio_rechazadas';
    protected $connection = 'cadeco';
    protected $fillable = [
        'id_solicitud_cambio',
        'id_rechazo',
        'motivo'
    ];
}
