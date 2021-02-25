<?php


namespace App\Services\CTPQ;


use App\Models\CTPQ\Cuenta;
use App\Models\CTPQ\Empresa;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class CuentaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaService constructor.
     * @param Cuenta $model
     */
    public function __construct(Cuenta $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $empresa = Empresa::find($empresaLocal->IdEmpresaContpaq);
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        return $this->repository->all($data);
    }
}
