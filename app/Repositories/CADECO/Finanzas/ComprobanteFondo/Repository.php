<?php
/**
 * Created by PhpStorm.
 * User: DBenitezC
 * Date: 24/02/2021
 * Time: 08:20 PM
 */

namespace App\Repositories\CADECO\Finanzas\ComprobanteFondo;



use App\Models\CADECO\ComprobanteFondo;
use App\Models\CADECO\Fondo;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(ComprobanteFondo $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findFondo($descripcion)
    {
        $fondos = Fondo::where('descripcion', 'LIKE', '%'.$descripcion.'%')->pluck('id_fondo');
        return $fondos;
    }

    public function orderByFondo($campo, $ordena)
    {
        $fondos = Fondo::orderBy($campo, $ordena)->pluck('id_fondo');
        return $fondos;
    }
}
