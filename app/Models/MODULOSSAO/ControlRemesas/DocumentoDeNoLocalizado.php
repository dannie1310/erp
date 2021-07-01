<?php


namespace App\Models\MODULOSSAO\ControlRemesas;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return $this->hasOne(\App\Models\MODULOSSAO\Seguridad\Usuario::class,  'IDUsuario','id_usuario_registro');
    }

    public function aprobo()
    {
        return $this->hasOne(Usuario::class,  'idusuario','id_usuario_aprobo');
    }

    public function rechazo()
    {
        return $this->hasOne(Usuario::class, 'idusuario', 'id_usuario_rechazo');
    }

    /**
     * Scopes
     */

    public function scopeAutorizacionPendiente($query)
    {
        return $query->where('estado', 0);
    }

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
                return '#F1C40F';
                break;

            case 1:
                return '#00a65a';
                break;

            case 2:
                return '#EC7063';
                break;
        }
    }

    /**
     * Métodos
     */
    public function autorizar()
    {
        try {
            DB::connection('modulosao')->beginTransaction();
            $this->estado = 1;
            $this->id_usuario_aprobo = auth()->id();
            $this->fecha_hora_aprobacion = date('Y-m-d H:i:s');
            $this->save();
            DB::connection('modulosao')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('modulosao')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function rechazar($motivo)
    {
        try {
            DB::connection('modulosao')->beginTransaction();
            $this->estado = 2;
            $this->id_usuario_rechazo = auth()->id();
            $this->fecha_hora_rechazo =date('Y-m-d H:i:s');
            $this->motivo_rechazo = $motivo;
            $this->save();
            DB::connection('modulosao')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('modulosao')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}
