<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 22/02/23
 * Time: 04:45 PM
 */

namespace App\Services\CONCURSOS;

use App\Events\Concursos\ActualizacionDatosAperturaConcurso;
use App\Events\Concursos\InicioDeAperturaConcurso;
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
        $participante = $this->repository->create($data);
        if($participante && $participante->concurso)
        {
            event(new ActualizacionDatosAperturaConcurso($participante->concurso));
        }
       return $participante;
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function setGanador($id)
    {
        $participante = $this->repository->show($id);
        if($participante->concurso->estatus == 1)
        {
            abort(400, "El concurso aún no se encuentra cerrado, por lo tanto no se puede establecer un ganador. \n\nFavor de comunicarse con Soporte a Aplicaciones y/o Coordinación SAO en caso de tener alguna duda.");
        }
        if($participante->concurso->estatus == 3)
        {
            abort(400, "El concurso ya tiene fallo registrado, por lo tanto no se puede modificar al ganador. \n\nFavor de comunicarse con Soporte a Aplicaciones y/o Coordinación SAO en caso de tener alguna duda.");
        }
        $participante->setGanador();
        return $participante;
    }

    public function update(array $data, $id)
    {
        $participante = $this->repository->show($id);

        if($participante->concurso->estatus != 1)
        {
            abort(400, "El concurso se encuentra con estado cerrado, por lo tanto, no se puede editar \nFavor de comunicarse con Soporte a Aplicaciones y/o Coordinación SAO en caso de tener alguna duda.");
        }

        $participante = $participante->editar($data);

        if($participante && $participante->concurso)
        {
            event(new ActualizacionDatosAperturaConcurso($participante->concurso));
        }

        return $participante;
    }

    public function destroy($id)
    {
        $participante = $this->repository->show($id);
        $concurso = $participante->concurso;
        $participante->eliminar();

        if($concurso)
        {
            event(new ActualizacionDatosAperturaConcurso($concurso));
        }

        return $participante;
    }

}
