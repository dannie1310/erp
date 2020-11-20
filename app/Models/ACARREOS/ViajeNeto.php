<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class ViajeNeto extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'viajesnetos';
    public $primaryKey = 'IdViajeNeto';
    protected $fillable = [

    ];

    /**
     * Relaciones Eloquent
     */
    public function camion()
    {
        return $this->belongsTo(Camion::class, 'IdCamion', 'IdCamion');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'IdMaterial', 'IdMaterial');
    }

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
