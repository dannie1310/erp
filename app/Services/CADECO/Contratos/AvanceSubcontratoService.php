<?php


namespace App\Services\CADECO\Contratos;


use App\Models\CADECO\AvanceSubcontrato;
use App\Models\CADECO\Subcontrato;
use App\Repositories\CADECO\Contratos\AvanceSubcontratoRepository as Repository;

class AvanceSubcontratoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(AvanceSubcontrato $model)
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

        if(isset($data['id_empresa'])){
            $empresa = Empresa::where([['descripcion', 'LIKE', '%'.$data['id_empresa'].'%']])->pluck("id_empresa");
            $this->repository->whereIn(['id_empresa',  $empresa]);
        }

        if(isset($data['subcontrato__numero_folio'])){
            $subcontrato = Subcontrato::where([['descripcion', 'LIKE', '%'.$data['subcontrato__numero_folio'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente',  $subcontrato]);
        }

        if (isset($data['monto'])) {
            $this->repository->where([['monto', 'LIKE', '%'.$data['monto'].'%']]);
        }
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function obtenerPartida($id)
    {
        return $this->repository->show($id)->partidasSubcontrato();
    }

    public function update($data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }
}
