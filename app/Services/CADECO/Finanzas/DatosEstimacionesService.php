<?php


namespace App\Services\CADECO\Finanzas;
use App\Models\CADECO\Finanzas\DatosEstimaciones;
use App\Repositories\Repository;


class DatosEstimacionesService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DatosEstimacionesService constructor.
     * @param DatosEstimaciones $model
     */
    public function __construct(DatosEstimaciones $model)
    {
        $this->repository = new Repository($model);
    }

    public function store($data){
        $datos = [
          "penalizacion_antes_iva" => $data['data']['penalizacion_antes_iva'],
          "retenciones_antes_iva" => $data['data']['retenciones_antes_iva'],
          "prest_mat_antes_iva" => $data['data']['prest_mat_antes_iva'],
          "ret_fon_gar_antes_iva" => $data['data']['ret_fon_gar_antes_iva'],
          "desc_pres_mat_antes_iva" => $data['data']['desc_pres_mat_antes_iva'],
          "desc_otros_prest_antes_iva" => $data['data']['desc_otros_prest_antes_iva']
        ];
        return $this->repository->create($datos);
    }

}