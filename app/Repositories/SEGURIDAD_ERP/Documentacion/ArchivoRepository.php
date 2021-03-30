<?php


namespace App\Repositories\SEGURIDAD_ERP\Documentacion;



use App\Models\SEGURIDAD_ERP\Documentacion\Archivo;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Documentacion\Archivo as Model;


class ArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrarArchivo($data)
    {
        return $this->model->create($data);
    }

    public function getRepetido($hashfile){
        $repetido_f = Archivo::where("hashfile", $hashfile)->first();
        return $repetido_f;
    }

}
