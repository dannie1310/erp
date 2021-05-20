<?php


namespace App\Services\MODULOSSAO;


use App\Models\MODULOSSAO\ControlRemesas\RemesaFolio;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use App\Repositories\MODULOSSAO\Remesa\RemesaFolio\Repository;

class RemesaFolioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * RemesaFolioService constructor.
     * @param RemesaFolio $model
     */
    public function __construct(RemesaFolio $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['proyecto']))
        {
            $proyecto = Proyecto::where([['Nombre', 'LIKE', '%' . $data['proyecto'] . '%']])->pluck('IDProyecto');
            $this->repository->whereIn(['IDProyecto', $proyecto]);
        }

        if (isset($data['anio']))
        {
            $this->repository->where([['Anio', '=', $data['anio']]]);
        }

        if (isset($data['numeroSemana']))
        {
            $this->repository->where([['NumeroSemana', '=', $data['numeroSemana']]]);
        }

        if (isset($data['CantidadExtraordinariasPermitidas']))
        {
            $this->repository->where([['CantidadExtraordinariasPermitidas', '=', $data['CantidadExtraordinariasPermitidas']]]);
        }

        if (isset($data['MontoLimiteExtraordinarias']))
        {
            $this->repository->where([['MontoLimiteExtraordinarias', '=', $data['MontoLimiteExtraordinarias']]]);
        }

        return  $this->repository->paginate($data);
    }

    public function show($data)
    {
        return  $this->repository->findFolio($data);
    }

    public function update(array $data)
    {
        return $this->repository->findFolio($data)->editar($data);
    }
}
