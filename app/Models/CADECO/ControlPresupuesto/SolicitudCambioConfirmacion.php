<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:00 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use App\Facades\Context;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ControlPresupuesto\Estatus;
use App\Models\CADECO\ControlPresupuesto\TipoOrden;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioPartidas;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioRechazada;

class SolicitudCambioConfirmacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.solicitudes_cambio_confirmaciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_solicitud_cambio',
        'id_usuario_confirmo',
        'fecha_hora_confirmacion',
    ];

    public function solicitudCambio()
    {
        return $this->belongsTo(SolicitudCambio::class, 'id_solicitud_cambio', 'id');
    }

    public function usuarioConfirmo()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_confirmo', 'idusuario');
    }

    public function getFechaHoraConfirmacionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_confirmacion);
        return date_format($date,"d/m/Y H:i");
    }
}
