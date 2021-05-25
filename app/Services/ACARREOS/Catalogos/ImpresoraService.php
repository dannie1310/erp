<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\IGH\Usuario;
use App\Repositories\Repository;
use App\Models\ACARREOS\Impresora;
use Illuminate\Support\Facades\DB;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;

class ImpresoraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ImpresoraService constructor.
     * @param Impresora $model
     */
    public function __construct(Impresora $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $this->conexionAcarreos();

        if (isset($data['mac']))
        {
            $this->repository->where([['mac', 'LIKE', '%'.$data['mac'].'%']]);
        }

        if (isset($data['marca']))
        {
            $this->repository->where([['marca', 'LIKE', '%'.$data['marca'].'%']]);
        }

        if (isset($data['modelo']))
        {
            $this->repository->where([['modelo', 'LIKE', '%'.$data['modelo'].'%']]);
        }

        if (isset($data['created_at']))
        {
            $this->repository->whereBetween( ['created_at', [ request( 'created_at' )." 00:00:00",request( 'created_at' )." 23:59:59"]] );
        }

        if (isset($data['registro']))
        {
            $usuario = Usuario::where([['nombre', 'LIKE', '%' . $data['registro'] . '%']])->pluck('idusuario');
            $this->repository->whereIn(['registro', $usuario]);
        }
        return  $this->repository->paginate($data);
    }

    public function store($data)
    {
        $this->conexionAcarreos();
        return $this->repository->create($data);
    }

    public function show($id)
    {
        $this->conexionAcarreos();
        return $this->repository->show($id);
    }

    public function activar($id)
    {
        $this->conexionAcarreos();
        $origen = $this->show($id);
        if ($origen->Estatus == 1) {
            abort(400, "La impresora se encuentra " . $origen->estado_format . " previamente.");
        }
        return $origen->activar();
    }

    public function desactivar(array  $data, $id)
    {
        $this->conexionAcarreos();
        $origen = $this->show($id);
        if ($origen->Estatus == 0) {
            abort(400, "La impresora se encuentra " . $origen->estado_format . " previamente.");
        }
        return $origen->desactivar($data['motivo']);
    }

    public function update(array $data, $id)
    {
        $this->conexionAcarreos();
        return $this->repository->show($id)->editar($data);
    }

   /* public function excel()
    {
        $this->conexionAcarreos();
        return Excel::download(new OrigenLayout(), 'origenes.csv');
    }*/

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
