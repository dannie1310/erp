<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'empresas';
    public $primaryKey = 'IdEmpresa';

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
