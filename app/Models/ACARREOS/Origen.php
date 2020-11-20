<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Origen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'origenes';
    public $primaryKey = 'IdOrigen';

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
