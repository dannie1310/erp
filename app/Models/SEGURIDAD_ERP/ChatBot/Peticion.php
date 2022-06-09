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
        , "numero_clave"
        , "palabra_clave"
        , "peticion"
    ];

    /**
     * Relaciones Eloquent
     */
    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'id_opcion', 'id');
    }

    public function registrar(array $data)
    {
        $opcion = $this->obtieneOpcion($data["peticion"],$data["id_usuario"]);

        $peticion = Peticion::create(
            [
                "id_usuario"=>$data["id_usuario"]
                ,"peticion"=>$data["peticion"]
                ,"id_opcion"=>$opcion->id
            ]
        );
        return $peticion;

    }

    public function obtieneOpcion($peticion,$id_usuario)
    {
        if($peticion == "<")
        {
            $ultimaPeticion = Peticion::where("id_usuario","=",$id_usuario)
            ->orderBy("id","desc")
            ->first();

            return $ultimaPeticion->opcion->opcionPadre;

        }

        $opcion = Opcion::where("palabra_clave","=",strtoupper($peticion))
            ->first();

        if(!$opcion && is_numeric($peticion))
        {
            $ultima_peticion = Peticion::where("id_usuario","=", $id_usuario)->orderBy("id","desc")
                ->first();

            if($ultima_peticion)
            {
                $opcion = Opcion::where("numero_clave","=",$peticion)
                    ->where("id_padre","=",$ultima_peticion->id_opcion)
                    ->first();

            }else{
                $opcion = Opcion::where("numero_clave","=",$peticion)
                    ->first();
            }

        }

        if(!$opcion)
        {
            $opcion = Opcion::where("palabra_clave","=","SALUDO")
                ->first();
        }

        return $opcion;
    }

    public function getRespuesta($parametros)
    {
        $respuesta =  $this->opcion->texto_devolver;
        foreach ($parametros as $label=>$value)
        {
            $respuesta = str_replace("[$label]",$value,$respuesta);
        }

        foreach($this->opcion->opcionesHijas()->orderBy("orden")->get() as $opcionHija)
        {
            $respuesta.="\n".$opcionHija->numero_clave.".-".$opcionHija->texto_devolver;
        }
        if($this->opcion->id_padre>0)
        {
            $respuesta.="\n< Regresar";
        }

        return $respuesta;

    }

}
