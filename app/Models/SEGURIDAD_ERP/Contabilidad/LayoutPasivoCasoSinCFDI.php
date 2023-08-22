<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class LayoutPasivoCasoSinCFDI extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.layout_pasivos_casos_sin_cfdi';
    protected $fillable =[
        "descripcion",
    ];
    public $timestamps = false;
    /**
     * Relaciones
     */


    /**
     * Scopes
     */

    /**
     * Atributos
     */


    /**
     * Métodos
     */
}
