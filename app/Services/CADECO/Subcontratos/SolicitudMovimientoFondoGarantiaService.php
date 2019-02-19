<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 06:16 PM
 */

namespace App\Services\CADECO\Subcontratos;


use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;
use App\Repositories\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia\Repository;

class SolicitudMovimientoFondoGarantiaService
{
    /**
     * @var Repository
     */
    protected $repository;
    private $id_usuario;
    private $usuario;
    private $id_obra;

    /**
     * SolicitudMovimientoFondoGarantiaService constructor.
     * @param SolicitudMovimientoFondoGarantia $model
     */
    public function __construct(SolicitudMovimientoFondoGarantia $model)
    {
        $this->repository = new Repository($model);
        /*$this->id_usuario = auth()->id();
        $this->usuario = auth()->user()->usuario;
        $this->id_obra = Context::getIdObra();*/
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function create($data)
    {
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function cancelar(array $data, $id)
    {
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->cancelar($data, $id);
    }

    public function rechazar(array $data, $id)
    {
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->rechazar($data, $id);
    }

    public function autorizar(array $data, $id)
    {
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->autorizar($data, $id);
    }

    public function revertirAutorizacion(array $data, $id)
    {
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->revertirAutorizacion($data, $id);
    }

}