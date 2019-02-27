<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 06:58 PM
 */

namespace App\Services\CADECO\Contratos;


use App\Facades\Context;
use App\Models\CADECO\Subcontrato;
use App\Repositories\CADECO\Subcontratos\Subcontrato\Repository;

class SubcontratoService
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
     * @param Subcontrato $model
     */
    public function __construct(Subcontrato $model)
    {

        $this->repository = new Repository($model);
        $this->id_usuario = auth()->id();
        $this->usuario = auth()->user()->usuario;
        $this->id_obra = Context::getIdObra();
    }

    public function all($data)
    {
        return $this->repository->all($data);
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
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario_registra'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->create($data);
    }
}