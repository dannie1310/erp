<?php


namespace App\Services\CADECO;


use App\Models\CADECO\Destajista;
use App\Repositories\Repository;

class DestajistaService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Destajista $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $destajista = $this->repository;

        if(isset($data['rfc']))
        {
            $destajista = $destajista->where([['rfc', 'LIKE', '%' . request('rfc') . '%']]);
        }
        if(isset($data['razon_social']))
        {
            $destajista = $destajista->where([['razon_social', 'LIKE', '%' . request('razon_social') . '%']]);
        }

        return $destajista->paginate($data);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function delete($data, $id)
    {
        return $this->repository->delete($data, $id);
    }
}
