<?php


namespace App\Services\ACARREOS\Catalogos;


use App\CSV\Acarreos\Catalogos\EmpresaLayout;
use App\Models\ACARREOS\Empresa;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\IGH\Usuario;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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

    public function update(array $data, $id)
    {
        $this->conexionAcarreos();
        return $this->repository->show($id)->editar($data);
    }

    public function store($data)
    {
        $this->conexionAcarreos();
        return $this->repository->create($data);
    }

    public function activar($id)
    {
        $this->conexionAcarreos();
        $empresa = $this->show($id);
        if ($empresa->Estatus == 1) {
            abort(400, "La empresa se encuentra " . $empresa->estado_format . " previamente.");
        }
        return $empresa->activar();
    }

    public function desactivar(array  $data, $id)
    {
        $this->conexionAcarreos();
        $empresa = $this->show($id);
        if ($empresa->Estatus == 0) {
            abort(400, "La empresa se encuentra " . $empresa->estado_format . " previamente.");
        }
        return $empresa->desactivar($data['motivo']);
    }

    public function excel()
    {
        $this->conexionAcarreos();
        return Excel::download(new EmpresaLayout(), 'empresas.csv');
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
