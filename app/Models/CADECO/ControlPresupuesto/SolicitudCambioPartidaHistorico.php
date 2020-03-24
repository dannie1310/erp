<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:00 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioPartidas;

class SolicitudCambioPartidaHistorico extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.solicitud_cambio_partidas_historico';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_solicitud_cambio_partida',
        'nivel',
        'cantidad_presupuestada_original',
        'cantidad_presupuestada_actualizada',
        'monto_presupuestado_original',
        'monto_presupuestado_actualizado',
        'precio_unitario_original',
        'precio_unitario_actualizado',
    ];

    public $timestamps = false;

    public function solicitudcambio(){
        return $this->belongsTo(SolicitudCambioPartidas::class, 'id_solicitud_cambio_partida', 'id');
    }

}