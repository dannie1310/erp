<?php


namespace App\Views\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgMotivoBoletinada;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmpresaBoletinadaVw extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.vw_empresas_boletinadas';
    public $timestamps = false;

    protected $fillable = [
    ];
}
