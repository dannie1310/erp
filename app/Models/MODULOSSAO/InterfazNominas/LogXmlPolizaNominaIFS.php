<?php

namespace App\Models\MODULOSSAO\InterfazNominas;

use Illuminate\Database\Eloquent\Model;

class LogXmlPolizaNominaIFS extends Model
{
    protected $connection = 'interfaz_nominas';
    protected $table = 'InterfazNominas.LogXmlPolizasNominasIFS';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'empresa',
        'actividad',
        'id_poliza_contpaq',
        'usuario_carga',
        'estatus'
    ];
}
