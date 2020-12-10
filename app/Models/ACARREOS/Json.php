<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Json extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'json';
    public $primaryKey = 'id';
    protected $fillable = [
      'json'
    ];
    public $timestamps = false;

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
