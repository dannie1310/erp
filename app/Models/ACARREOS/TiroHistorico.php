<?php


namespace App\Models\ACARREOS;


use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class TiroHistorico extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'tiros_historicos';
    protected $primaryKey = 'Id';

    /**
     * Relaciones Eloquent
     */
    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'IdProyecto', 'IdProyecto');
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
    public function getNombreRegistroAttribute()
    {
        try{
            return $this->registro->nombre_completo;
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

    public function getClaveFormatAttribute()
    {
        return $this->Clave.$this->IdTiro;
    }

    public function getFechaRegistroFormatAttribute()
    {
        $date = date_create($this->created_at);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaDesactivoFormatAttribute()
    {
        $date = date_create($this->updated_at);
        return date_format($date,"d/m/Y H:i");
    }
}
