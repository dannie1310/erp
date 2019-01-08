<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 06:13 PM
 */

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\EstatusPrepoliza;
use App\Traits\RepositoryTrait;

class EstatusPrepolizaRepository
{
    use RepositoryTrait;

    /**
     * @var EstatusPrepoliza
     */
    protected $model;

    /**
     * EstatusPrepolizaRepository constructor.
     * @param EstatusPrepoliza $model
     */
    public function __construct(EstatusPrepoliza $model)
    {
        $this->model = $model;
    }
}