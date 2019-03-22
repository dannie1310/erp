<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/21/19
 * Time: 6:59 PM
 */

namespace App\Services\IGH;


use App\Models\IGH\Usuario;
use App\Repositories\IGH\UsuarioRepository as Repository;

class UsuarioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * UsuarioService constructor.
     * @param Usuario $model
     */
    public function __construct(Usuario $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}