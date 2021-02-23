<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;

class AvisoSATOmitir extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.avisos_sat_omitir';

    public $timestamps = false;

}
