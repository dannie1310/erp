<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgSeccion;

class CtgTipoArchivo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.ctg_tipos_archivos';
    public $timestamps = false;
    
    public function ctgSeccion(){
        return $this->belongsTo(CtgSeccion::class, 'id_seccion', 'id');
    }
}
