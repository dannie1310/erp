<?php

namespace App\Services\ACTIVO_FIJO;

use App\Models\ACTIVO_FIJO\Resguardo;
use App\PDF\ActivoFijo\ResguardoFormato;
use App\Models\ACTIVO_FIJO\UsuarioUbicacion;
use App\Repositories\ACTIVO_FIJO\ResguardoRepository as Repository;

class ResguardoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Resguardo constructor.
     * @param Resguardo $model
     */
    public function __construct(Resguardo $model)
    {
        $this->repository = new Repository($model);
    }

    public function listaResguardos($data){
        return $this->repository->getListaResguardos($data);
    }

    public function getResguardos($data){
        // return $this->repository->getResguardos($data);
        $query = $this->repository;
        if(!isset($data['ubicacion'])){
            $ubicaciones = UsuarioUbicacion::where('idUsuario', '=', auth()->id())->pluck('idUbicacion')->toArray();
            array_push($ubicaciones,auth()->user()->idubicacion );
            $query->whereIn(['IdProyecto', $ubicaciones]);  
        }else{
            $this->repository->where([['IdProyecto', '=', $data['ubicacion']]]);
        }
        if(isset($data['empleado'])){
            $this->repository->where([['IdEmpleado', '=', $data['empleado']]]);
        }
        if(isset($data['tipo'])){
            $this->repository->where([['GrupoEquipo', '=', $data['tipo']]]);
        }
        $this->repository->where([['Estatus', '!=', 0]]);
        return $query->paginate();
    }

    public function pdfResguardo($id){
        $resguardo = $this->repository->show($id);
        $pdf = new ResguardoFormato($resguardo);
        return $pdf->create();
    }
}