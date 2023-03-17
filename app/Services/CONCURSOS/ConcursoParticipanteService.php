<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 22/02/23
 * Time: 04:45 PM
 */

namespace App\Services\CONCURSOS;

use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use App\Models\SEGURIDAD_ERP\Concursos\ConcursoParticipante;
use App\PDF\Concurso\InformeCierre;
use App\Repositories\SEGURIDAD_ERP\Concursos\ConcursoParticipanteRepository;
use App\Repositories\SEGURIDAD_ERP\Concursos\ConcursoRepository;

class ConcursoParticipanteService
{
    /**
     * @var ConcursoRepository
     */
    protected $repository;

    /**
     * @param ConcursoParticipante $model
     */
    public function __construct(ConcursoParticipante $model)
    {
        $this->repository = new ConcursoParticipanteRepository($model);
    }

    public function store(array $data)
    {
       return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        $participante = $this->repository->show($id);

        if($participante->concurso->estatus != 1)
        {
            abort(400, "El concurso se encuentra con estado cerrado, por lo tanto, no se puede editar \nFavor de comunicarse con Soporte a Aplicaciones y/o Coordinación SAO en caso de tener alguna duda.");
        }
        return $participante->editar($data);
    }

    public function destroy($id)
    {
        return $this->repository->show($id)->eliminar();
    }

}
