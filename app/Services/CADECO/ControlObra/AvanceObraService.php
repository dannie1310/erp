<?php


namespace App\Services\CADECO\ControlObra;


use App\Models\CADECO\AvanceObra;
use App\Repositories\CADECO\ControlObra\AvanceObraRepository as Repository;

class AvanceObraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AvanceObraService constructor.
     * @param AvanceObra $model
     */
    public function __construct(AvanceObra $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if(isset($data['numero_folio'])){
            $this->repository->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['observaciones'])){
            $this->repository->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }
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

    public function store($data)
    {
        dd($data);
        return $this->repository->create($data);
    }
}
