<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:42 AM
 */

namespace App\Services\CTPQ_NOM;


use App\Models\CTPQ\NomGenerales\Nom10000;
use App\Repositories\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class EmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param ConfigRepository $config
     * @param Nom10000 $model
     */
    public function __construct(ConfigRepository $config,Nom10000 $model)
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
        $empresa = $this->show($id);
        $this->config->set('database.connections.cntpq_nom.database', $empresa->RutaEmpresa);
        \Config::set('database.connections.cntpq_nom.database',$empresa->RutaEmpresa);
        return $empresa;
    }
}
