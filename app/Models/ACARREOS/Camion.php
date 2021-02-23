<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

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


    /**
     * Métodos
     */
}
