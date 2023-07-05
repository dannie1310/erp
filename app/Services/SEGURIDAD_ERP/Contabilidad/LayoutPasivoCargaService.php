<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Exports\Contabilidad\ListaEmpresasExport;
use App\Exports\FinanzasGlobal\SolicitudesPagoAplicadasExport;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCarga;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCargaRepository;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ListaEmpresaRepository as Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Maatwebsite\Excel\Facades\Excel;

class LayoutPasivoCargaService{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param LayoutPasivoCarga $model
     */
    public function __construct(LayoutPasivoCarga $model)
    {
        $this->repository = new LayoutPasivoCargaRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}
