<?php


namespace App\Services\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\NoDeducido;
use App\Repositories\SEGURIDAD_ERP\Fiscal\NoDeducido\Repository;

class NoDeducidoService
{
    /**
     * @var Repository
     */
    protected $repository;


    public function __construct(NoDeducido $model)
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
        $this->validarPartidas($data['cfd']);
        return $this->repository->create($data);
    }

    private function validarPartidas($data)
    {
        foreach ($data as $partida) {
            if (array_key_exists('selected', $partida) == false || ($partida['selected'] == true)) {
                return "OK";
                break;
            }
        }
        throw new \Exception('Se debe seleccionar al menos un CFD');
    }
}
