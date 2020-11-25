<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class InicioCamion extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'inicio_camion';
    public $primaryKey = 'id';
    protected $fillable = [
        'idcamion',
        'idmaterial',
        'idorigen',
        'fecha_origen',
        'idusuario',
        'uidTAG',
        'IMEI',
        'idperfil',
        'folioMina',
        'folioSeguimiento',
        'volumen',
        'code',
        'numImpresion',
        'tipo',
        'estatus',
        'Version',
        'deductiva',
        'idMotivo_deductiva',
        'FechaCarga',
        'deductiva_entrada',
        'latitud_origen',
        'longitud_origen'
    ];

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
