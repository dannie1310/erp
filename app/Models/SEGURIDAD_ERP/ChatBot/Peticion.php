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

        if(!$opcion && strpos($peticion, ".")){
            $opcion = Opcion::where("cadena_clave","=",$peticion)
                ->first();
        }

        if(!$opcion)
        {
            $opcion = Opcion::where("palabra_clave","=","SALUDO")
                ->first();
        }

        return $opcion;
    }

    public function getRespuesta($parametros, $tokenObj)
    {
        if(!is_null($this->opcion->cadena_clave))
        {
            $respuesta = "*_".$this->opcion->cadena_clave."_* ";
        }else {
            $respuesta = "";
        }

        if(strlen($this->opcion->texto_devolver)<=50)
        {
            $respuesta .=  "*_".$this->opcion->texto_devolver."_*";
        } else
        {
            $respuesta .= $this->opcion->texto_devolver;
        }

        foreach ($parametros as $label=>$value)
        {
            $respuesta = str_replace("[$label]",$value,$respuesta);
        }

        $respuesta .= "\n";

        $opcionesHijas = $this->opcion->opcionesHijas()->orderBy("orden")->get();

        if(count($opcionesHijas)>0)
        {
            foreach($opcionesHijas as $opcionHija)
            {
                $respuesta.="\n".$opcionHija->numero_clave.".-".$opcionHija->texto_devolver;
            }
        } else if($this->opcion->ruta_api != "")
        {
            try{
                $respuesta.="\n".$this->getRespuestaAPI($this->opcion->ruta_api, $tokenObj->accessToken);
            }catch (\Exception $e) {
                if($e->getCode() == 403)
                {
                    $respuesta .= "\n🚫 No cuenta con los privilegios para realizar esta consulta. 🚫";

                }
            }

        }


        if($this->opcion->id_padre>0)
        {
            $respuesta.="\n\n < Regresar";
        }

        return $respuesta;

    }

    public function getRespuestaAPI($ruta_api, $token)
    {

        $respuesta = "";

        $client = new \GuzzleHttp\Client();
        $ruta_peticion = "https://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/".$ruta_api."?access_token=".$token;

        try {
            $response = $client->request('GET', $ruta_peticion);
            $responseJSON = json_decode($response->getBody());
            $respuesta = $response->getBody();


        } catch (RequestException $th) {
            $responseJSON = json_decode($th->getResponse()->getBody());
            $respuesta = $responseJSON->message;
        }

        return $respuesta;
    }

}
