<?php


namespace App\Services\MODULOSSAO;


use App\Models\MODULOSSAO\ControlRemesas\RemesaFolio;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use App\Repositories\Repository;

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
            $this->repository->where([['Anio', 'LIKE', '%'.$data['anio'].'%']]);
        }

        if (isset($data['numeroSemana']))
        {
            $this->repository->where([['NumeroSemana', 'LIKE', '%'.$data['numeroSemana'].'%']]);
        }

        if (isset($data['CantidadExtraordinariasPermitidas']))
        {
            $this->repository->where([['CantidadExtraordinariasPermitidas', 'LIKE', '%'.$data['CantidadExtraordinariasPermitidas'].'%']]);
        }

        if (isset($data['MontoLimiteExtraordinarias']))
        {
            $this->repository->where([['MontoLimiteExtraordinarias', 'LIKE', '%'.$data['MontoLimiteExtraordinarias'].'%']]);
        }

        return  $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}
