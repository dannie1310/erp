<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:00 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ControlPresupuesto\TipoOrden;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;

class SolicitudCambioPartidas extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.solicitud_cambio_partidas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_solicitud_cambio',
        'id_tipo_orden',
        'id_concepto',
        'nivel',
        'id_material',
        'unidad',
        'descripcion',
        'cantidad_presupuestada_original',
        'cantidad_presupuestada_nueva',
        'precio_unitario_original',
        'precio_unitario_nuevo',
        'monto_presupuestado',
        'variacion_volumen',
        'rendimiento_original',
        'rendimiento_nuevo',
        'tipo_agrupador',
    ];

    public $timestamps = false;

    public function solicitudcambio(){
        return $this->belongsTo(SolicitudCambio::class, 'id_solicitud_cambio', 'id');
    }

    public function tipoOrden(){
        return $this->belongsTo(TipoOrden::class, 'id_tipo_orden', 'id');
    }
}