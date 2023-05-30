<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class ProveedorUltimaUbicacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.etl_proveedor_rep_ultimas_ubicaciones';
    public $timestamps = false;

    public $fillable = [
    ];

    public function ultimo_cfdi(){
        return $this->hasOne(CFDSAT::class,"id","id_cfdi");
    }
}
