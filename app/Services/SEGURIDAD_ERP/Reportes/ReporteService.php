<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/04/2020
 * Time: 01:10 PM
 */

namespace App\Services\SEGURIDAD_ERP\Reportes;
use App\Models\SEGURIDAD_ERP\Reportes\Reporte as Model;
use App\Repositories\SEGURIDAD_ERP\Reportes\ReporteRepository as Repository;

class ReporteService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        if($this->repository->show($id)){
            return $this->repository->show($id);
        } else {
            abort(500,"Reporte no encontrado");
        }
    }

    public function paginate($data)
    {
        $reportes = $this->repository;

        if (isset($data['nombre'])) {
            $reportes->where([['nombre', 'LIKE', '%' . $data['nombre'] . '%']]);
        }

        if (isset($data['descripcion'])) {
            $reportes->where([['descripcion', 'LIKE', '%' . $data['descripcion'] . '%']]);
        }
        return $reportes->paginate($data);
    }
}