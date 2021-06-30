<?php


namespace App\Models\MODULOSSAO\ControlRemesas;


use App\Models\IGH\Usuario;
use App\Models\MODULOSSAO\Seguridad\Usuario as UsuarioModuloSAO;
use Illuminate\Database\Eloquent\Model;

class DocumentoDeNoLocalizado extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ModulosSAO.ControlRemesas.DocumentosDeNoLocalizados';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function documento()
    {
        return $this->hasOne(Documento::class, 'IDDocumento', 'id_documento')->withoutGlobalScopes();
    }

    public function registro()
    {
        return $this->belongsTo(UsuarioModuloSAO::class,  'IDUsuario','id_usuario_registro');
    }

    public function aprobo()
    {
        return $this->belongsTo(Usuario::class,  'idusuario','id_usuario_aprobo');
    }

    public function rechazo()
    {
        return $this->belongsTo(Usuario::class, 'idusuario', 'id_usuario_rechazo');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */
    public function getFechaAprobacionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_aprobacion);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaRechazoFormatAttribute()
    {
        $date = date_create($this->fecha_hora_rechazo);
        return date_format($date,"d/m/Y H:i");
    }

    public function getNombreRegistroAttribute()
    {
        try{
            return $this->registro->Nombre;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreAproboAttribute()
    {
        try{
            return $this->aprobo->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreRechazoAttribute()
    {
        try {
            return $this->rechazo->nombre_completo;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado)
        {
            case 0:
                return 'REGISTRADO';
                break;

            case 1:
                return 'APROBADO';
                break;

            case 2:
                return 'RECHAZADO';
                break;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ($this->estado)
        {
            case 0:
                return '#706e70';
                break;

            case 1:
                return '#00a65a';
                break;

            case 2:
                return '#d1cfd1';
                break;
        }
    }

    /**
     * Métodos
     */
}
