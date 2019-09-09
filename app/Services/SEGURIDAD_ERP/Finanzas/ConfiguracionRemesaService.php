<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Finanzas\ConfiguracionRemesa;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Repositories\Repository;

class ConfiguracionRemesaService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ConfiguracionRemesaService constructor.
     * @param ConfiguracionRemesa $model
     */
    public function __construct(ConfiguracionRemesa $model)
    {
        $this->repository = new Repository($model);
    }

    public function show()
    {
        $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();

        $tipo_obra = ConfiguracionObra::query()->where('id_proyecto','=',$proyectos->id)
            ->where('id_obra','=',Context::getIdObra())->first();

        return $this->repository->show($tipo_obra->id);
    }

    public function actualizar($data)
    {
        $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();

        $tipo_obra = ConfiguracionObra::query()->where('id_proyecto','=',$proyectos->id)
            ->where('id_obra','=',Context::getIdObra())->first();

        if ($this->repository->show($tipo_obra->id)){
            $remesa = $this->repository->show($tipo_obra->id)->update($data);
        }else{
            $remesa = $this->repository->create([
                'id_configuracion_obra' => $tipo_obra->id,
                'documentos_manuales' => $data['documentos_manuales']
            ]);
        }
        return $this->repository->show($tipo_obra->id);
    }

}