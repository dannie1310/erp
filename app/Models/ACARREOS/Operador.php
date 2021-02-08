<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Operador extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'operadores';
    public $primaryKey = 'IdOperador';

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
