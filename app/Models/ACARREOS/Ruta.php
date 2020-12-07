<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'rutas';
    public $primaryKey = 'IdRuta';

    /**
     * Relaciones Eloquent
     */


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



    /**
     * Métodos
     */
}
