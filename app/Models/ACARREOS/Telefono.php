<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'telefonos';
    public $primaryKey = 'id';

    /**
     * Relaciones Eloquent
     */
    public function impresora()
    {
        return $this->belongsTo(Impresora::class, 'id_impresora', 'id');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus',  1);
    }

    public function scopeImpresoraActiva($query)
    {
        return $query->whereHas('impresora', function ($q)
        {
            return $q->activo();
        });
    }

    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
