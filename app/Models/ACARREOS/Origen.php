<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Origen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'origenes';
    public $primaryKey = 'IdOrigen';
    protected $fillable = [
        'Clave',
        'IdTipoOrigen',
        'IdProyecto',
        'Descripcion',
        'FechaAlta',
        'HoraAlta',
        'usuario_registro',
        'Estatus',
        'interno',
    ];

    /**
     * Relaciones Eloquent
     */
    public function tipoOrigen()
    {
        return $this->belongsTo(TipoOrigen::class, 'IdTipoOrigen', 'IdTipoOrigen');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function usuarioDesactivo()
    {
        return $this->belongsTo(Usuario::class, 'usuario_desactivo', 'idusuario');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus',  1);
    }

    /**
     * Attributes
     */
    public function getClaveFormatAttribute()
    {
        return $this->Clave.$this->IdOrigen;
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->Estatus)
        {
            case 1:
                return 'ACTIVO';
                break;
            case 0:
                return 'INACTIVO';
                break;
            default:
                return '';
                break;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ($this->Estatus)
        {
            case 1:
                return '#00a65a';
                break;
            case 0:
                return '#706e70';
                break;
            default:
                return '#d1cfd1';
                break;
        }
    }

    public function getTipoOrigenFormatAttribute()
    {
        switch ($this->interno)
        {
            case 1:
                return 'INTERNO';
                break;
            case 0:
                return 'EXTERNO';
                break;
            default:
                return '';
                break;
        }
    }

    public function getFechaRegistroCompletaFormatAttribute()
    {
        $date = date_create($this->created_at);
        return date_format($date,"d/m/Y H:i");
    }

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuario->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getTipoOrigenDescripcionAttribute()
    {
        try{
            return $this->tipoOrigen->Descripcion;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreUsuarioDesactivoAttribute()
    {
        try{
            return $this->usuarioDesactivo->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getFechaDesactivoFormatAttribute()
    {
        $date = date_create($this->updated_at);
        return date_format($date,"d/m/Y H:i");
    }


    /**
     * Métodos
     */
    public function validarRegistro()
    {
        if (self::where('Descripcion', $this->Descripcion)->first()) {
            abort(400, "El origen (" . $this->Descripcion . ") ya se encuentra registrado previamente.");
        }
    }

    public function activar()
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->Estatus = 1;
            $this->usuario_desactivo = NULL;
            $this->motivo = NULL;
            $this->save();
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function desactivar($motivo)
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->Estatus = 0;
            $this->usuario_desactivo = auth()->id();
            $this->motivo = $motivo;
            $this->save();
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function editar($datos)
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->update($datos);
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}
