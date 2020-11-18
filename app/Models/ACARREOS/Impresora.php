<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'impresoras';
    public $primaryKey = 'id';

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
