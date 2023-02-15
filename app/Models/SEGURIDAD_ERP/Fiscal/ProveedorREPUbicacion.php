<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Obra;
use Illuminate\Database\Eloquent\Model;

class ProveedorREPUbicacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.vw_proveedor_rep_ubicaciones';
    public $timestamps = false;
    public $fillable = [
    ];

    public function obraGlobal()
    {
        return $this->belongsTo(Obra::class, "id_obra_global", "id");
    }

}
