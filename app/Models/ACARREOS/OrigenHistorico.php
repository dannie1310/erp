<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class OrigenHistorico extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'origenes_historicos';
    protected $primaryKey = 'Id';

    /**
     * Relaciones Eloquent
     */
    public function tipoOrigen()
    {
        return $this->belongsTo(TipoOrigen::class, 'IdTipoOrigen', 'IdTipoOrigen');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function desactivo()
    {
        return $this->belongsTo(Usuario::class, 'usuario_desactivo', 'idusuario');
    }

    /**
     * Scopes
     */


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

    public function getNombreRegistroAttribute()
    {
        try{
            return $this->registro->nombre_completo;
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

    public function getNombreDesactivoAttribute()
    {
        try{
            return $this->desactivo->nombre_completo;
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
}
