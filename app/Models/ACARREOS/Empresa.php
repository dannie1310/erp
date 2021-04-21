<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'empresas';
    public $primaryKey = 'IdEmpresa';

    /**
     * Relaciones Eloquent
     */
    public function registro()
    {
        return $this->belongsTo(Usuario::class,  'usuario_registro', 'idusuario');
    }

    public function desactivo()
    {
        return $this->belongsTo(Usuario::class,  'usuario_desactivo', 'idusuario');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('Estatus',  1);
    }

    /**
     * Attributes
     */
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

    public function getFechaRegistroAttribute()
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

    public function getFechaDesactivacionFormatAttribute()
    {
        $date = date_create($this->updated_at);
        return date_format($date, "d/m/Y H:i");
    }

    public function getNombreDesactivoAttribute()
    {
        try{
            return $this->desactivo->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * Métodos
     */
}
