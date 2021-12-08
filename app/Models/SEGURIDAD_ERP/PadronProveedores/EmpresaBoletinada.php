<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmpresaBoletinada extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.empresas_boletinadas';
    public $timestamps = false;
   // protected $primaryKey = "rfc";

    protected $fillable = [
        'id_tipo_boletinadas',
        'rfc',
        'razon_social',
        'observaciones'
    ];

    public function motivo()
    {
        return $this->belongsTo(CtgMotivoBoletinada::class, "id_tipo_boletinadas", "id");
    }
}
