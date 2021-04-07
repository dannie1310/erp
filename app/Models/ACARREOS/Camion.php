<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Camion extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'camiones';
    public $primaryKey = 'IdCamion';

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

    public function imagenes()
    {
        return $this->hasMany(CamionImagen::class, 'IdCamion', 'IdCamion');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('camiones.Estatus',  1);
    }

    public function scopeMarcaDescripcion($query)
    {
        return $query->leftjoin('marcas','marcas.IdMarca', 'camiones.IdMarca');
    }

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

    /**
     * Métodos
     */
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
}
