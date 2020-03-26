<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 24/03/2020
 * Time: 09:38 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;

use Ghi\Domain\Core\Models\BaseModel;

class SolicitudCambioRechazada extends BaseModel
{
    protected $table = 'ControlPresupuesto.solicitud_cambio_rechazada';
    protected $connection = 'cadeco';
    protected $fillable = [
        'id_solicitud_cambio',
        'id_rechazo',
        'motivo'
    ];
}