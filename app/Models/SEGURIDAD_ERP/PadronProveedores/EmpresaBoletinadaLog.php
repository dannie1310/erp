<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmpresaBoletinadaLog extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.empresas_boletinadas_log';
    public $timestamps = false;
   // protected $primaryKey = "rfc";

    protected $fillable = [
        'id_empresa_boletinada',
        'id_tipo_boletinadas',
        'rfc',
        'razon_social',
        'observaciones',
        'usuario_registro',
        'fecha_hora_registro',
        'usuario_edito',
        'fecha_hora_edicion',
        'usuario_elimino',
        'fecha_hora_eliminacion',
        'motivo_eliminacion'
    ];

    public function motivo()
    {
        return $this->belongsTo(CtgMotivoBoletinada::class, "id_tipo_boletinadas", "id");
    }

    public function eliminar($motivo)
    {

        $this->destroy();
    }
}
