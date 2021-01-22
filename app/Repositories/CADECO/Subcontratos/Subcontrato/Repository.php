<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 06:27 PM
 */

namespace App\Repositories\CADECO\Subcontratos\Subcontrato;


use App\Models\CADECO\Subcontrato;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Subcontrato $model)
    {
        $this->model = $model;
    }
}
