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

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function existe($usuario)
    {
        return $this->repository->where([["usuario","=",$usuario]])->first();
    }

    public function buscaUsuarioEmpresaPorCorreo($correo)
    {
        $this->repository->where([["correo","=",$correo]]);
        $this->repository->where([["usuario_estado","!=","2"]]);
        $this->repository->where([["usuario","!=",$correo]]);
        return $this->repository->all();
    }
}
