<?php

namespace App\Services\CADECO;



use App\Models\CADECO\Transaccion as Model;
use App\Repositories\CADECO\TransaccionRepository as Repository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class TransaccionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TransaccionService constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        return $this->repository->paginate();
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function showSC($id, $base_datos)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base_datos);
        return $this->repository->withoutGlobalScopes()->show($id);
    }

    public function store(array $data)
    {

    }
}
