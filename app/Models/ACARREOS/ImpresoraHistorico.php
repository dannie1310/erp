<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class ImpresoraHistorico extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'impresoras_historicos';
    protected $primaryKey = 'id';

    /**
     * Relaciones Eloquent
     */

    public function usuario_registro()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function usuario_desactivo()
    {
        return $this->belongsTo(Usuario::class, 'elimino', 'idusuario');
    }

    /**
     * Scopes
     */


    /**
     * Attributes
     */

    public function getEstadoFormatAttribute()
    {
        switch ($this->estatus)
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

    public function getFechaRegistroCompletaFormatAttribute()
    {
        $date = date_create($this->created_at);
        return date_format($date,"d/m/Y H:i");
    }

    public function getNombreDesactivoAttribute()
    {
        try{
            return $this->usuario_desactivo->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreRegistroAttribute()
    {
        try{
            return $this->usuario_registro->nombre_completo;
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
