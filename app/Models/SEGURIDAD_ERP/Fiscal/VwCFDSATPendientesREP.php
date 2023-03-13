<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class VwCFDSATPendientesREP extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.vw_cfd_sat_rep_pendiente';
    public $timestamps = false;
    protected $fillable =[

    ];

    public function cfdiPagado()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfdi', 'id');
    }
}
