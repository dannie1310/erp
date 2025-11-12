<?php

namespace App\Models\SEGURIDAD_ERP\Finanzas;

use Illuminate\Database\Eloquent\Model;

class CodigoImpuesto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.codigos_impuestos';
    public $timestamps = false;
}
