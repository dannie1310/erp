<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class DeductivaMotivo extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'deductivas_motivos';
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
