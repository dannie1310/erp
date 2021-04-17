<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\ACARREOS\Tiro;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class TiroService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TiroService constructor.
     * @param Repository $repository
     */
    public function __construct(Tiro $model)
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

    public function paginate($data)
    {
        $this->conexionAcarreos();
        return  $this->repository->paginate($data);
    }

    public function asignarConcepto(array $data, $id)
    {
        $this->conexionAcarreos();
        return  $this->show($id)->asignarConcepto($data[0]);;
    }

    public function store($data)
    {
        $this->conexionAcarreos();
        return $this->repository->create($data);
    }

    public function activar($id)
    {
        $this->conexionAcarreos();
        try {
            DB::connection('acarreos')->beginTransaction();
            $tiro = $this->show($id);
            if ($tiro->Estatus == 1) {
                abort(400, "El tiro se encuentra " . $tiro->estado_format . " previamente.");
            }
            $tiro->Estatus = 1;
            $tiro->usuario_desactivo = NULL;
            $tiro->motivo = NULL;
            $tiro->save();
            DB::connection('acarreos')->commit();
            return $tiro;
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
            $tiro = $this->show($id);
            if ($tiro->Estatus == 0) {
                abort(400, "El tiro se encuentra " . $tiro->estado_format . " previamente.");
            }
            $tiro->Estatus = 0;
            $tiro->usuario_desactivo = auth()->id();
            $tiro->motivo = $data['motivo'];
            $tiro->save();
            DB::connection('acarreos')->commit();
            return $tiro;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
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

    
    public function excel()
    {
        $this->conexionAcarreos();
        return Excel::download(new TiroLayout(), 'tiros.csv');
    }
}
