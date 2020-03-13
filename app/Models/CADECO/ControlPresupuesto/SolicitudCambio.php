<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:00 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use Illuminate\Database\Eloquent\Model;

class SolicitudCambio extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.solicitud_cambio';
    protected $primaryKey = 'id';

    protected $fillable = [
        'area_solicitante',
        'motivo',
        'numero_folio',
        'id_estatus',
        'id_tipo_orden',
        'id_obra',
        'importe_afectacion',
    ];

}