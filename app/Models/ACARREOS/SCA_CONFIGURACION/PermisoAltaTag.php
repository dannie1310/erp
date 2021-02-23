<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use Illuminate\Database\Eloquent\Model;

class PermisoAltaTag extends Model
{
    protected $connection = 'scaconf';
    protected $table = 'sca_configuracion.permisos_alta_tag';
    protected $primaryKey = 'idusuario';
    public $timestamps = false;
}
