<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\ACARREOS\Marca;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\IGH\Usuario;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\CSV\Acarreos\Catalogos\MarcaLayout;

class MarcaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MarcaService constructor.
     * @param Marca $model
     */
    public function __construct(Marca $model)
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

        if (isset($data['descripcion']))
        {
            $this->repository->where([['Descripcion', 'LIKE', '%'.$data['descripcion'].'%']]);
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
        $this->conexionAcarreos();
        return $this->repository->create($data);
    }

    public function activar($id)
    {
        $this->conexionAcarreos();
        $marca = $this->show($id);
        if ($marca->Estatus == 1) {
            abort(400, "La marca se encuentra " . $marca->estado_format . " previamente.");
        }
        return $marca->activar();
    }

    public function desactivar(array  $data, $id)
    {
        $this->conexionAcarreos();
        $marca = $this->show($id);
        if ($marca->Estatus == 0) {
            abort(400, "La marca se encuentra " . $marca->estado_format . " previamente.");
        }
        return $marca->desactivar($data['motivo']);
    }

    public function update(array $data, $id)
    {
        $this->conexionAcarreos();
        return $this->repository->show($id)->editar($data);
    }

    public function excel()
    {
        $this->conexionAcarreos();
        return Excel::download(new MarcaLayout(), 'marca.csv');
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
