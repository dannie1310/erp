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

class Opcion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.ChatBot.opciones';
    public $timestamps = false;
    protected $fillable =[
        "numero_clave"
        , "palabra_clave"
        , "orden"
        , "texto_devolver"
        , "ruta_api"
        , "estado"
    ];


    /**
     * Relaciones Eloquent
     */
    public function opcionesHijas()
    {
        return $this->hasMany(Opcion::class, 'id_padre', 'id');
    }

    public function opcionPadre()
    {
        return $this->belongsTo(Opcion::class, 'id_padre', 'id');
    }

}
