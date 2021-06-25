<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $connection = 'scaconf';
    protected $table = 'sca_configuracion.configuracion';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
