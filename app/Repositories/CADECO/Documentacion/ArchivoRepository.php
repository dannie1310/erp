<?php


namespace App\Repositories\CADECO\Documentacion;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\CADECO\Documentacion\Archivo as Model;


class ArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getRepetido($hashfile){
        return Archivo::where("hashfile", $hashfile)->first();
    }

}
