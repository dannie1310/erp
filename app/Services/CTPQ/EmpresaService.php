<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:42 AM
 */

namespace App\Services\CTPQ;


use App\Models\CTPQ\Empresa;
use App\Repositories\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
class EmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AlmacenService constructor.
     *
     * @param Almacen $model
     */
    public function __construct(ConfigRepository $config,Empresa $model)
    {
        $this->config = $config;
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function conectar($id)
    {
        $empresa = $this->repository->show($id);
        $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($id);
        $this->config->set('database.connections.cntpq.database', $empresa->AliasBDD);
        \Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        return $this->config->get("database.connections.cntpq.database");
    }

}