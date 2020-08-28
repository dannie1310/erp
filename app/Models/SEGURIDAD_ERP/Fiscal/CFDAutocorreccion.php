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

    public function ctgEstado()
    {
        return $this->belongsTo(CtgEstadoCFD::class, 'estado', 'id');
    }

    public function validarCreacion()
    {
        if ($this->cfdSat->estado != 0)
        {
            abort(400, "El CFD (" . $this->uuid . ") tiene estado: ".$this->cfdSat->ctgEstado->descripcion.".");
        }
    }

    public function validarAplicacion()
    {
        if ($this->cfdSat->estado != 5)
        {
            abort(400, "El CFD (" . $this->uuid . ") tiene estado: ".$this->cfdSat->ctgEstado->descripcion.".");
        }
    }
}
