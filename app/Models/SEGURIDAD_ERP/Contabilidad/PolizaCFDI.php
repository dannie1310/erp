<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class PolizaCFDI extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.polizas_cfdi';
    protected $primaryKey = 'id';
    protected $fillable =[
        "base_datos_contpaq",
        "id_asociacion",
        "id_poliza_contpaq",
        "guid_poliza_contpaq",
        "uuid",
        "id_cfdi",
        "ejercicio",
        "periodo",
        "fecha",
        "monto",
        "tipo",
        'folio',
        "solicitud_asociacion_registro",
        'concepto',
        'usuario_codigo',
        'usuario_nombre'
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
