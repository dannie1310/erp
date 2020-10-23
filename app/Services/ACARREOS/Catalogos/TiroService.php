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

    private function conexionAcarreos()
    {
        try{
            DB::purge('acarreos');
            \Config::set('database.connections.acarreos.database',Proyecto::pluck('base_datos')->first());
        }catch (\Exception $e){
            abort(500, "El proyecto no se encuentra activo en acarreos.");
            throw $e;
        }
    }
}
