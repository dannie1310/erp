<?php


namespace App\Repositories\MODULOSSAO\Remesa\RemesaFolio;


use App\Models\MODULOSSAO\ControlRemesas\RemesaFolio;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(RemesaFolio $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findFolio($data)
    {
        return $this->model->where('IDProyecto', $data['id_proyecto'])
            ->where('Anio', $data['anio'])
            ->where('NumeroSemana', $data['numero_semana'])->first();
    }
}
