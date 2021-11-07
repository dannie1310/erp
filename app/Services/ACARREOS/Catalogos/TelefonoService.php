<?php


namespace App\Services\ACARREOS\Catalogos;

use App\Repositories\Repository;
use App\Models\ACARREOS\Telefono;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\CSV\Acarreos\Catalogos\TelefonoLayout;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;

class TelefonoService{
    
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CamionService constructor.
     * @param Camion $model
     */
    public function __construct(Telefono $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
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

    public function update(array $data, $id)
    {
        $this->conexionAcarreos();
        return $this->repository->update($data, $id);
    }

    public function paginate($data)
    {
        $this->conexionAcarreos();
        return  $this->repository->paginate($data);
    }

    public function activar($id)
    {
        $this->conexionAcarreos();
        try {
            DB::connection('acarreos')->beginTransaction();
            $telefono = $this->show($id);
            if ($telefono->estatus == 1) {
                abort(400, "El telefono se encuentra " . $telefono->estado_format . " previamente.");
            }
            $telefono->estatus = 1;
            $telefono->elimino = NULL;
            $telefono->motivo = NULL;
            $telefono->save();
            DB::connection('acarreos')->commit();
            return $telefono;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function desactivar(array  $data, $id)
    {
        $this->conexionAcarreos();
        try {
            DB::connection('acarreos')->beginTransaction();
            $telefono = $this->show($id);
            if ($telefono->estatus == 0) {
                abort(400, "El telefono se encuentra " . $telefono->estado_format . " previamente.");
            }
            $telefono->estatus = 0;
            $telefono->elimino = auth()->id();
            $telefono->motivo = $data['motivo'];
            $telefono->save();
            DB::connection('acarreos')->commit();
            return $telefono;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function excel()
    {
        $this->conexionAcarreos();
        return Excel::download(new TelefonoLayout(), 'telefonos.csv');
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