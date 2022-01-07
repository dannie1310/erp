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
        'tamanio_kb'
    ];
    /*
     * Relaciones*/

    public function invitacion()
    {
        return $this->belongsTo(Invitacion::class, "id_invitacion", "id");
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoArchivoInvitacion::class, "id_tipo_archivo", "id");
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function getFechaRegistroFormatAttribute()
    {
        if($this->fecha_hora_registro){
            $date = date_create($this->fecha_hora_registro);
            return date_format($date,"d/m/Y H:i");
        }
        return '';
    }

    /*
     * Scope*/

    public function scopeCargados($query)
    {
        return $query->whereNotNull("hashfile");
    }

    /*
     * Atributos*/

    public function getTipoArchivoTxtAttribute()
    {
        try{
            return $this->tipo->descripcion;
        } catch (\Exception $e){
            return $this->tipo->descripcion;
        }
    }

    public function getRegistroAttribute()
    {
        return $this->usuarioRegistro->nombre_completo;
    }

    /*
     * Métodos*/

    public function registrar($data)
    {
        $archivo = InvitacionArchivo::create($data);
        return $archivo;
    }

}
