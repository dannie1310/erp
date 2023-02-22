<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 22/02/23
 * Time: 04:45 PM
 */

namespace App\Services\CONCURSOS;

use App\Models\SEGURIDAD_ERP\Concurso;
use App\Repositories\Repository;

class ConcursoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ConcursoService constructor.
     *
     * @param Concurso $model
     */
    public function __construct(Concurso $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data)
    {dd($data);
       return $this->repository->create($data);
        
    }
}