<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:07 PM
 */

namespace App\Models\CADECO\FinanzasCBE;


use Illuminate\Database\Eloquent\Model;

class SolicitudMovimiento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'FinanzasCBE.solicitud_movimiento';

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id');
    }

    public function tipoMovimientoSolicitud()
    {
        return $this->belongsTo(CtgTipoMovimientoSolicitud::class, 'id_tipo_movimiento', 'id');
    }

    public function solicitudAntecedente()
    {
        return $this->belongsTo(SolicitudMovimiento::class, 'id_movimiento_antecedente', 'id');
    }
}