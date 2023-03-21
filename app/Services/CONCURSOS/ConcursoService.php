<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 22/02/23
 * Time: 04:45 PM
 */

namespace App\Services\CONCURSOS;

use App\Events\Concursos\FinalizacionDeAperturaConcurso;
use App\Events\Concursos\InicioDeAperturaConcurso;
use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use App\PDF\Concurso\InformeCierre;
use App\Repositories\SEGURIDAD_ERP\Concursos\ConcursoRepository;
use DateTime;
use DateTimeZone;

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
        $fecha = new DateTime($data['fecha']);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data["fecha"] = $fecha->format("Y/m/d");
        $data["nombre"] = $data["concurso"];
        $concurso = $this->repository->create($data);
        event(new InicioDeAperturaConcurso($concurso));
        return $concurso;
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
            abort(400, "El concurso se encuentra con estado cerrado, por lo tanto, no se puede editar \nFavor de comunicarse con Soporte a Aplicaciones y/o Coordinación SAO en caso de tener alguna duda.");
        }

        $fecha = new DateTime($data['fecha']);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data["fecha"] = $fecha->format("Y/m/d");

        return $concurso->editar($data);
    }

    public function cerrar($id)
    {
        $concurso = $this->repository->show($id);
        if($concurso->estatus == 1)
        {
            $concurso->cerrar();
            event(new FinalizacionDeAperturaConcurso($concurso));
        }
        return $concurso;
    }

    public function pdf($id = null)
    {
        if($id){
            $concurso = $this->repository->show($id);

        }else{
            $concurso = $this->repository->ultimo();
        }
        $pdf = new InformeCierre($concurso);
        $pdf->create()->Output('I', 'AperturaConcurso-' . $concurso->nombre_archivo.".pdf", 1);
    }

    /*public function agregaParticipante(array $data, $id)
    {
        $concurso = $this->repository->show($id);

        if($concurso->estatus != 1)
        {
            abort(400, "El concurso se encuentra con estado cerrado, por lo tanto, no se puede editar \nFavor de comunicarse con Soporte a Aplicaciones y/o Coordinación SAO en caso de tener alguna duda.");
        }
        return $concurso->agregaParticipante($data);
    }

    public function eliminaParticipante($id, $id_participante)
    {
        $concurso = $this->repository->show($id);

        if($concurso->estatus != 1)
        {
            abort(400, "El concurso se encuentra con estado cerrado, por lo tanto, no se puede editar \nFavor de comunicarse con Soporte a Aplicaciones y/o Coordinación SAO en caso de tener alguna duda.");
        }
        return $concurso->eliminaParticipante($id_participante);
    }*/
}
