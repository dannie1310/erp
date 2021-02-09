<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class SolicitudActualizacionCamion extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'solicitud_actualizacion_camion';
    public $primaryKey = 'IdSolicitudActualizacion';

    protected $fillable = [
        'IdCamion',
        'IdSindicato',
        'IdEmpresa',
        'Propietario',
        'IdOperador',
        'Placas',
        'PlacasCaja',
        'IdMarca',
        'Modelo',
        'Ancho',
        'Largo',
        'Alto',
        'Gato',
        'Extension',
        'Disminucion',
        'CubicacionReal',
        'CubicacionParaPago',
        'Economico',
        'IMEI',
        'Registro',
        'Version',
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
