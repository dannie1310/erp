<?php


namespace App\Services\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\Autocorreccion;
use App\Repositories\SEGURIDAD_ERP\Fiscal\Autocorreccion\Repository;

class AutocorreccionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AutocorreccionService constructor.
     * @param Autocorreccion $model
     */
    public function __construct(Autocorreccion $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        $this->validarPartidas($data['cfds']);
        return $this->repository->create($data);
    }

    private function validarPartidas($data)
    {
        foreach ($data as $partida)
        {
            if(array_key_exists('selected', $partida) == false || ($partida['selected'] == true))
            {
                return "OK";
                break;
            }
        }
        throw new \Exception('Se debe seleccionar al menos un CFD');
    }

    public function aplicar($id, array $data)
    {
        return $this->repository->show($id)->aplicarAutocorreccion($data);
    }
}
