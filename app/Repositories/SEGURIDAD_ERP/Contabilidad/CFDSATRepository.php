<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 04:18 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class CFDSATRepository extends Repository implements RepositoryInterface
{
    public function __construct(CFDSAT $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}