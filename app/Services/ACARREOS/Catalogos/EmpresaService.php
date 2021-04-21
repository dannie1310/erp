<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\ACARREOS\Empresa;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\IGH\Usuario;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class EmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EmpresaService constructor.
     * @param Empresa $model
     */
    public function __construct(Empresa $model)
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

        if (isset($data['razonSocial'])) {
            $this->repository->where([['razonSocial', 'LIKE', '%' . $data['razonSocial'] . '%']]);
        }

        if (isset($data['rfc'])) {
            $this->repository->where([['rfc', 'LIKE', '%' . $data['rfc'] . '%']]);
        }

        if (isset($data['created_at'])) {
            $this->repository->whereBetween(['created_at', [request('created_at') . " 00:00:00", request('created_at') . " 23:59:59"]]);
        }

        if (isset($data['razonSocial'])) {
            $this->repository->where([['razonSocial', 'LIKE', '%' . $data['razonSocial'] . '%']]);
        }

        if (isset($data['usuario_registro'])) {
            $usuario = Usuario::where([['nombre', 'LIKE', '%' . $data['usuario_registro'] . '%']])->pluck('idusuario');
            $this->repository->whereIn(['usuario_registro', $usuario]);
        }

        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        $this->conexionAcarreos();
        return $this->repository->show($id);
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
