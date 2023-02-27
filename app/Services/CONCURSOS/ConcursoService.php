<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 22/02/23
 * Time: 04:45 PM
 */

namespace App\Services\CONCURSOS;

use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use App\Repositories\SEGURIDAD_ERP\Concursos\ConcursoRepository;

class ConcursoService
{
    /**
     * @var ConcursoRepository
     */
    protected $repository;

    /**
     * ConcursoService constructor.
     *
     * @param Concurso $model
     */
    public function __construct(Concurso $model)
    {
        $this->repository = new ConcursoRepository($model);
    }

    public function store(array $data)
    {
       return $this->repository->create($data);
    }

    public function paginate($data)
    {
        if(isset($data['nombre']))
        {
            $this->repository->where([['nombre', 'LIKE', '%' . $data['nombre'] . '%']]);
        }

        if (isset($data['fecha_hora_inicio_apertura']))
        {
            $this->repository->whereBetween( ['fecha_hora_inicio_apertura', [ request( 'fecha_hora_inicio_apertura' )." 00:00:00",request( 'fecha_hora_inicio_apertura' )." 23:59:59"]] );
        }
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        $concurso = $this->repository->show($id);

        if($concurso->estatus != 1)
        {
            abort(400, "El concurso se encuentra con estado cerrado, por lo tanto, no se puede editar \nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }
        return $concurso->editar($data);
    }
}