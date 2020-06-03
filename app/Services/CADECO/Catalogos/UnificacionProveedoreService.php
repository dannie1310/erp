<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 02/06/2020
 * Time: 06:00 PM
 */

namespace App\Services\CADECO\Catalogos;

use App\Repositories\Repository;
use App\Models\CADECO\Catalogos\UnificacionProveedores;

class UnificacionProveedoreService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * UnificacionProveedoreService constructor
     *
     * @param UnificacionProveedores $model
     */

    public function __construct(UnificacionProveedores $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
}
