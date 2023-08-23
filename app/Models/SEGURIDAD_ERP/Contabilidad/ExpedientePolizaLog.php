<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class ExpedientePolizaLog extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.expediente_poliza_log';
    protected $primaryKey = 'id';
    protected $fillable =[
        "id_expediente",
        "guid_relacionado",
        "guid_pertenece",
        "uuid",
        "alias_bdd",
        "id_poliza",
        "id_usuario_asocio",
        "timestamp_asociacion",
        "id_usuario_desasocio",
        "timestamp_desasocio",
        "estado",
    ];
    public $timestamps = false;

    public function cfdi()
    {
        return $this->belongsTo(CFDSAT::class, "uuid", "uuid");
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }
}
