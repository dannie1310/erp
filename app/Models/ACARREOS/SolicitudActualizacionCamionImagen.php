<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class SolicitudActualizacionCamionImagen extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'solicitud_actualizacion_camion_imagenes';
    public $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'IdSolicitudActualizacion',
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
