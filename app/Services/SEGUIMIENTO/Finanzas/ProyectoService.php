<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\GrlProyecto;
use App\Repositories\Repository;

class ProyectoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(GrlProyecto $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        $proyectos = $this->repository->all($data);
        $datos_proyecto = array();
        $tipo =  0;
        $i = 0;

        foreach ($proyectos as $proyecto)
        {
            if($tipo == 0 || $tipo != $proyecto->idproyecto_tipo)
            {
                $tipo =  $proyecto->idproyecto_tipo;
                $datos_proyecto[$i] = [
                    'id' => 0,
                    'nombre' => $proyecto->tipo->proyecto_tipo
                ];
                $i++;
            }
            $datos_proyecto[$i] = [
                'id' => $proyecto->idproyecto,
                'nombre' => "-  ".$proyecto->proyecto
            ];
            $i++;
        }
        return $datos_proyecto;
    }
}
