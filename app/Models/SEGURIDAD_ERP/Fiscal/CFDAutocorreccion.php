<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class CFDAutocorreccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat_autocorrecciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_autocorreccion',
        'registro',
        'fecha_hora_registro',
        'aprobo',
        'fecha_hora_aprobacion',
        'rechazo',
        'fecha_hora_rechazo',
        'id_cfd_sat',
        'estado',
        'uuid'
    ];

    public $timestamps = false;

    public function autocorreccion()
    {
        return $this->belongsTo(Autocorreccion::class, 'id_autocorreccion', 'id');
    }

    public function cfdSat()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfd_sat', 'id');
    }
}
