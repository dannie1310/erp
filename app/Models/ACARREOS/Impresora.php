<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'impresoras';
    public $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('estatus',  1);;
        });
    }

    /**
     * Relaciones Eloquent
     */

    /**
     * Scopes
     */


    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
