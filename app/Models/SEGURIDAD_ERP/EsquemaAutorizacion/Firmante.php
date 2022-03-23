<?php

namespace App\Models\SEGURIDAD_ERP\EsquemaAutorizacion;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class Firmante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'EsquemaAutorizacion.firmantes';
    public $timestamps = false;



    /**
     * Relaciones
     */

    public function nivelAutorizacion()
    {
        return $this->hasOne(NivelAutorizacion::class,"id", "id_nivel_autorizacion");
    }



    /**
     * Scopes
     */


}
