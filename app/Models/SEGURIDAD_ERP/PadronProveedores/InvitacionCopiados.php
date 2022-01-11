<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\CADECO\Obra;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use App\Utils\Util;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InvitacionCopiados extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.invitaciones_copiados';
    public $timestamps = false;

    protected $fillable = [
        'id_invitacion',
        'direccion'
    ];
    /*
     * Relaciones*/

    public function invitacion()
    {
        return $this->belongsTo(Invitacion::class, "id_invitacion", "id");
    }

}
