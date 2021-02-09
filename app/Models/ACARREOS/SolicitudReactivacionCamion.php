<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class SolicitudReactivacionCamion extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'solicitud_reactivacion_camion';
    public $primaryKey = 'IdSolicitudReactivacion';

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
        'Version'
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
