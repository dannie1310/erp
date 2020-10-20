<?php


namespace App\Repositories\CADECO\Documentacion;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\CADECO\Documentacion\Archivo as Model;
use App\Models\CADECO\Documentacion\Archivo;


class ArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getRepetido($hashfile, $id_transaccion){
        return Archivo::where("hashfile", $hashfile)->where("id_transaccion","=",$id_transaccion)->first();
    }

    public function registrarArchivo($data)
    {
        return $this->model->create($data);
    }

}
