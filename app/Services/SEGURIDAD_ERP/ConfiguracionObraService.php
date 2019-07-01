<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Repositories\Repository;

class ConfiguracionObraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ConfiguracionObraService constructor.
     * @param ConfiguracionObra $model
     */
    public function __construct(ConfiguracionObra $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        $configuracionObra = $this->repository;

        $configuracionObra = $configuracionObra->withoutGlobalScopes();
        return $configuracionObra->all();
   }
}