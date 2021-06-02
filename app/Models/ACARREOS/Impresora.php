<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\ACARREOS\ImpresoraHistorico;

class Impresora extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'impresoras';
    public $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('estatus',  1);;
        });
    }

    /**
     * Relaciones Eloquent
     */

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function usuarioDesactivo()
    {
        return $this->belongsTo(Usuario::class, 'elimino', 'idusuario');
    }

    public function historicos()
    {
        return $this->hasMany(ImpresoraHistorico::class, 'IdOrigen', 'IdOrigen');
    }

    /**
     * Scopes
     */


    /**
     * Attributes
     */
    public function getColorEstadoAttribute()
    {
        switch ($this->estatus)
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

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuario->nombre_completo;
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


    /**
     * Métodos
     */
}
