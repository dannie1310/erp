<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class TipoOrigen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'tiposorigenes';
    public $primaryKey = 'IdTipoOrigen';

    /**
     * Relaciones Eloquent
     */


    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus',  1);
    }

    /**
     * Attributes
     */


    /**
     * Métodos
     */
}
