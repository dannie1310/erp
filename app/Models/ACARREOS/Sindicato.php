<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Sindicato extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'sindicatos';
    public $primaryKey = 'IdSindicato';

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
