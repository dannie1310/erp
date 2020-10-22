<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tiro extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'tiros';
    public $timestamps = false;
    public $primaryKey = 'IdTiro';


    /**
     * Relaciones Eloquent
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'IdProyecto', 'IdProyecto');
    }

    /**
     * Scopes
     */


    /**
     * Attributes
     */
    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuario->nombre_completo;
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
        }
    }

    public function getClaveFormatAttribute()
    {
        return $this->Clave.$this->IdTiro;
    }

    public function getFechaRegistroCompletaFormatAttribute()
    {
        $date = date_create($this->created_at);
        return date_format($date,"d/m/Y H:i");
    }

    /**
     * Métodos
     */

}
