<?php

namespace App\Models\SEGURIDAD_ERP\EsquemaAutorizacion;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'EsquemaAutorizacion.tokens';
    public $timestamps = false;

    protected $fillable = [
        'id_firmante',
        'id_transaccion',
        'token',
        'estado',
    ];



    /**
     * Relaciones
     */



    /**
     * Scopes
     */


}
