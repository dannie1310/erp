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

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('camiones.Estatus',  1);
    }

    public function scopeMarcaDescripcion($query)
    {
        return $query->join('marcas','marcas.IdMarca', 'camiones.IdMarca');
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
