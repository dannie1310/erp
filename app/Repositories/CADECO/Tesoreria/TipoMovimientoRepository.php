<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:25 PM
 */

namespace App\Repositories\CADECO\Tesoreria;


use App\Models\CADECO\Tesoreria\TipoMovimiento;
use App\Traits\RepositoryTrait;

class TipoMovimientoRepository
{
    use RepositoryTrait;

    /**
     * @var TipoMovimiento
     */
    protected $model;

    /**
     * TipoMovimientoRepository constructor.
     * @param TipoMovimiento $model
     */
    public function __construct(TipoMovimiento $model)
    {
        $this->model = $model;
    }
}