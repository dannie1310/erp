<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmpresaBoletinada extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.vw_empresas_boletinadas';
    public $timestamps = false;
   // protected $primaryKey = "rfc";

    protected $fillable = [
       'motivo'
    ];
}
