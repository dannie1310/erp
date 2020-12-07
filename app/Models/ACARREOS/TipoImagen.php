<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class TipoImagen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'cat_tipos_imagenes';
    public $primaryKey = 'id';

    /**
     * Relaciones Eloquent
     */

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estado',  1);
    }

    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
