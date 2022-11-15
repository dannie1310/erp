<?php

namespace App\Models\SEGURIDAD_ERP\Reportes;


use App\Models\SEGURIDAD_ERP\Permiso;
use Illuminate\Database\Eloquent\Model;

class CatalogoMeses extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Reportes.CatalogoMeses';
    protected $primaryKey = 'MesID';

}
