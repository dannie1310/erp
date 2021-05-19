<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class CFDNoDeducido extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.cfd_no_deducidos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_no_deducido',
        'registro',
        'fecha_hora_registro',
        'id_cfd_sat',
        'estado',
        'uuid'
    ];

    public $timestamps = false;

    public function noDeducido()
    {
        return $this->belongsTo(NoDeducido::class, 'id_no_deducido', 'id');
    }

    public function cfdSat()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfd_sat', 'id');
    }

    public function ctgEstado()
    {
        return $this->belongsTo(CtgEstadoCFD::class, 'estado', 'id');
    }

    public function validar()
    {
        if ($this->cfdSat->estado != 0)
        {
            abort(400, "El CFDI (" . $this->uuid . ") tiene estado: ".$this->cfdSat->ctgEstado->descripcion.".");
        }
    }
}
