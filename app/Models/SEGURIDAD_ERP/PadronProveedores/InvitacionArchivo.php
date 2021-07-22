<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\CADECO\Obra;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InvitacionArchivo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.invitaciones_archivos';
    public $timestamps = false;

    protected $fillable = [
        'id_invitacion',
        'id_tipo_archivo',
        'hashfile',
        'nombre',
        'extension',
        'descripcion',
        'observaciones',
        'usuario_registro',
        'fecha_hora_registro',

    ];
    /*
     * Relaciones*/

    public function invitacion()
    {
        return $this->belongsTo(Invitacion::class, "id_invitacion", "id");
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoArchivo::class, "id_tipo_archivo", "id");
    }

    /*
     * Scope*/



    /*
     * Atributos*/

    /*
     * Métodos*/

    public function registrar($data)
    {
        $archivo = InvitacionArchivo::create($data);
        return $archivo;
    }

}
