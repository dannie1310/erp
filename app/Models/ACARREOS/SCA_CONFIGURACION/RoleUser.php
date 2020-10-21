<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'sca_configuracion.role_user';
    public $timestamps = false;
}
