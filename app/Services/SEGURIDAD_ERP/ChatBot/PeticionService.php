<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 24/10/2019
 * Time: 06:15 PM
 */


namespace App\Services\SEGURIDAD_ERP\ChatBot;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\ChatBot\Peticion;
use App\Repositories\SEGURIDAD_ERP\ChatBot\PeticionRepository;

class PeticionService
{
    /**
     * @var PeticionRepository
     */
    protected $repository;

    /**
     * @param Peticion $model
     */
    public function __construct(Peticion $model)
    {
        $this->repository = new PeticionRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function getRespuesta($parametros)
    {
        $from = $parametros['From'];
        $body = $parametros['Body'];
        $numero_celular = str_replace("whatsapp:","", $from);
        $usuario_from = Usuario::where("numero_celular","=",$numero_celular)->first();


        if(!$usuario_from){
            $mensaje = "Este servicio es para uso del personal de Hermes Infraestructura; el número celular: ".$numero_celular." no está asociado a ningún usuario.
			\nPor favor solicite al área de Soporte a Aplicaciones que le asigne el número.";
            return $mensaje;
        }
        $H = date_format(now(), "H");
        if($H>11){
            $saludo = "Buenas tardes";
        }else if($H>20){
            $saludo = "Buenas noches";
        }else  {
            $saludo = "Buenos días";
        }

        $nombre_usuario = ucwords(strtolower($usuario_from->nombre), " ");

        $tokenobj = $usuario_from->createToken('token-chat-bot');
        $token = $tokenobj->accessToken;

        $peticion = $this->store(
            [
                "id_usuario"=>$usuario_from->idusuario,
                "peticion"=>$body,
            ]
        );
        $mensaje = $peticion->getRespuesta(["saludo"=>$saludo,"nombre"=>$nombre_usuario],$tokenobj);

        return $mensaje;

    }

    public function store($data)
    {
        return $this->repository->create($data);
    }
}
