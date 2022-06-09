<?php

namespace App\Models\SEGURIDAD_ERP\ChatBot;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDAutocorreccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgEstadoCFD;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Peticion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.ChatBot.peticiones';
    public $timestamps = false;
    protected $fillable =[
        "id_opcion"
        , "id_usuario"
        , "token_acceso"
        , "texto_devolver"
        , "numero_clave"
        , "palabra_clave"
    ];

    /**
     * Relaciones Eloquent
     */
    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'id_opcion', 'id');
    }



    public function getRespuesta()
    {

    }

}
