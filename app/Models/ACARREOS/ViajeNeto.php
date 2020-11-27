<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class ViajeNeto extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'viajesnetos';
    public $primaryKey = 'IdViajeNeto';
    protected $fillable = [
        'IdArchivoCargado',
        'FechaCarga',
        'HoraCarga',
        'IdProyecto',
        'IdCamion',
        'IdOrigen',
        'FechaSalida',
        'HoraSalida',
        'IdTiro',
        'FechaLlegada',
        'HoraLlegada',
        'IdMaterial',
        'Observaciones',
        'Creo',
        'Estatus',
        'Code',
        'uidTAG',
        'Imagen01',
        'imei',
        'Version',
        'CodeImagen',
        'IdEmpresa',
        'IdSindicato',
        'CodeRandom',
        'CreoPrimerToque',
        'CubicacionCamion',
        'IdPerfil',
        'folioMina',
        'folioSeguimiento',
        'numImpresion',
        'tipoViaje',
        'latitud_origen',
        'longitud_origen',
        'latitud_tiro',
        'longitud_tiro'
    ];
    public $timestamps = false;

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
