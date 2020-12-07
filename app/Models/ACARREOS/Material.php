<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'materiales';
    public $primaryKey = 'IdMaterial';

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
