<?php

namespace App\Services\ACTIVO_FIJO;

use App\Models\SCI\VwListaUsuario;
use App\Repositories\Repository;

class ListaUsuarioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param VwListaUsuario $model
     */
    public function __construct(VwListaUsuario $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function indexOrdenado($data)
    {
        $usuarios = $this->repository->all($data);
        $datos = array();
        $tipo =  null;
        $i = 0;

        foreach ($usuarios as $usuario)
        {
            if($tipo == null || $tipo != $usuario->Ubicacion)
            {
                $tipo =  $usuario->Ubicacion;
                $datos[$i] = [
                    'id' => 0,
                    'nombre' => $usuario->Ubicacion
                ];
                $i++;
            }
            $datos[$i] = [
                'id' => $usuario->idUsuario,
                'nombre' => "-  ".$usuario->Usuario
            ];

            $i++;
        }
        return $datos;
    }
}
