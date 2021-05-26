<?php


namespace App\Services\ACARREOS\Catalogos;

use DateTime;
use DateTimeZone;
use App\Models\IGH\Usuario;
use App\Repositories\Repository;
use App\Models\ACARREOS\Operador;
use Illuminate\Support\Facades\DB;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;

class OperadorService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * OperadorService constructor.
     * @param Operador $model
     */
    public function __construct(Operador $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        $this->conexionAcarreos();
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        $this->conexionAcarreos();

        if (isset($data['Nombre']))
        {
            $this->repository->where([['Nombre', 'LIKE', '%'.$data['Nombre'].'%']]);
        }

        if (isset($data['Direccion']))
        {
            $this->repository->where([['Direccion', 'LIKE', '%'.$data['Direccion'].'%']]);
        }

        if (isset($data['NoLicencia']))
        {
            $this->repository->where([['NoLicencia', 'LIKE', '%'.$data['NoLicencia'].'%']]);
        }

        if (isset($data['Estatus']))
        {
            $this->repository->where([['Estatus', 'LIKE', '%'.$data['Estatus'].'%']]);
        }

        if (isset($data['VigenciaLicencia']))
        {
            $this->repository->whereBetween( ['VigenciaLicencia', [ request( 'VigenciaLicencia' ),request( 'VigenciaLicencia' )]] );
        }

        if (isset($data['created_at']))
        {
            $this->repository->whereBetween( ['created_at', [ request( 'created_at' )." 00:00:00",request( 'created_at' )." 23:59:59"]] );
        }

        if (isset($data['usuario_registro']))
        {
            $usuario = Usuario::where([['nombre', 'LIKE', '%' . $data['usuario_registro'] . '%']])->pluck('idusuario');
            $this->repository->whereIn(['usuario_registro', $usuario]);
        }
        return  $this->repository->paginate($data);
    }

    public function show($id)
    {
        $this->conexionAcarreos();
        return $this->repository->show($id);
    }

    public function store($data)
    {
        $fecha = New DateTime($data['VigenciaLicencia']);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['VigenciaLicencia'] = $fecha->format("Y-m-d");
        $this->conexionAcarreos();
        return $this->repository->create($data);
    }

    public function update(array $data, $id)
    {
        $fecha = New DateTime($data['VigenciaLicencia']);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['VigenciaLicencia'] = $fecha->format("Y-m-d");
        $this->conexionAcarreos();
        return $this->repository->update($data,$id);
    }

    public function activar($id)
    {
        $this->conexionAcarreos();
        $operador = $this->show($id);
        if ($operador->Estatus == 1) {
            abort(400, "El origen se encuentra " . $operador->estado_format . " previamente.");
        }
        return $operador->activar();
    }

    public function desactivar(array  $data, $id)
    {
        $this->conexionAcarreos();
        $operador = $this->show($id);
        if ($operador->Estatus == 0) {
            abort(400, "El origen se encuentra " . $operador->estado_format . " previamente.");
        }
        return $operador->desactivar($data['motivo']);
    }

    private function conexionAcarreos()
    {
        $base_datos = Proyecto::pluck('base_datos')->first();
        if(!is_null($base_datos))
        {
            try {
                DB::purge('acarreos');
                \Config::set('database.connections.acarreos.database', $base_datos);
            } catch (\Exception $e) {
                abort(500, "El proyecto no se encuentra activo en acarreos.");
                throw $e;
            }
        }else{
            abort(500, "El proyecto no se encuentra activo en acarreos.");
        }
    }
}
