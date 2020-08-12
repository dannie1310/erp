<?php


namespace App\Services\CTPQ;


use App\Models\CTPQ\Empresa;
use App\Models\CTPQ\TipoPoliza;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class TipoPolizaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoPolizaService constructor.
     * @param TipoPoliza $model
     */
    public function __construct(TipoPoliza $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        $empresa = Empresa::find($data["id_empresa"]);
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        return $this->repository->all($data);
    }
}
