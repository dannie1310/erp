<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class SolicitudReactivacionCamionImagen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'solicitud_reactivacion_camion_imagenes';
    public $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'IdSolicitudReactivacion',
        'IdCamion',
        'TipoC',
        'Imagen',
        'Tipo'
    ];

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
