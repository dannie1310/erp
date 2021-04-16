<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class CamionHistorico extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'camiones_historicos';
    public $primaryKey = 'Id';

    /**
     * Relaciones Eloquent
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'IdMarca', 'IdMarca');
    }

    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class, 'IdSindicato', 'IdSindicato');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'IdEmpresa', 'IdEmpresa');
    }

    public function operador()
    {
        return $this->belongsTo(Operador::class, 'IdOperador', 'IdOperador');
    }

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

    /**
     * Attributes
     */
    public function getDescripcionMarcaAttribute()
    {
        try{
            return $this->marca->Descripcion;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getNombreOperadorAttribute()
    {
        try{
            return $this->operador->Nombre;
        }catch (\Exception $exception)
        {
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

    public function getFechaRegistroAttribute()
    {
        return $this->FechaAlta.' '.$this->HoraAlta;
    }

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

    public function getDescripcionSindicatoAttribute()
    {
        try{
            return $this->sindicato->Descripcion;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getRazonSocialEmpresaAttribute()
    {
        try{
            return $this->empresa->razonSocial;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getFechaDesactivacionFormatAttribute()
    {
        $date = date_create($this->updated_at);
        return date_format($date,"d/m/Y H:i");
    }

    /**
     * Métodos
     */
}
