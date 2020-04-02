<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Proyecto;
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

    public function contexto()
    {

        $proyecto = Proyecto::query()->get()->where('base_datos','=',Context::getDatabase());

        $tipo_obra = ConfiguracionObra::query()->get()->where('id_proyecto','=',$proyecto[1]->id)
                                                ->where('id_obra','=',Context::getIdObra());
        return $tipo_obra;

    }
}
