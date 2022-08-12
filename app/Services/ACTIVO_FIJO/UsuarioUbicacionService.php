<?php

namespace App\Services\ACTIVO_FIJO;

use App\Repositories\Repository;
use App\Models\ACTIVO_FIJO\UsuarioUbicacion;
use App\Models\IGH\Ubicacion;

class UsuarioUbicacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Resguardo constructor.
     * @param UsuarioUbicacion $model
     */
    public function __construct(UsuarioUbicacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function listaResguardos($data){
        $ubicaciones = $this->repository->all()->pluck('idUbicacion')->toArray();
        array_push($ubicaciones, auth()->user()->idubicacion);
        return Ubicacion::whereIn('idubicacion', $ubicaciones)->select('idubicacion', 'Ubicacion')->get();
    }
}