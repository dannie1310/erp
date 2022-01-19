<?php


namespace App\Services\CADECO\PortalProveedor;


use App\Models\CADECO\SolicitudAutorizacionAvance;

use App\PDF\PortalProveedores\SolicitudAvanceFormato;
use App\Repositories\CADECO\SolicitudAutorizacionAvanceRepository as Repository;

class SolicitudAutorizacionAvanceService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudAutorizacionAvanceService constructor.
     * @param SolicitudAutorizacionAvance $model
     */
    public function __construct(SolicitudAutorizacionAvance $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->solicitudes();
    }

    public function store($data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function proveedorConceptos($id, $base)
    {
        return $this->repository->subcontratoAEstimar($id, $base);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function delete($data, $id)
    {
        return $this->repository->eliminar($id, $data['data']);
    }

    public function pdfSolicitudAvanceFormato($id, $data)
    {
        $pdf = new SolicitudAvanceFormato($id, $data['db']);
        return $pdf;
    }

    public function registrarRetencionIva($data, $id)
    {
        return $this->repository->registrarIVARetenido($id, $data);
    }

    public function descargaLayout($id, $base)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '7200');
        return $this->repository->descargaLayout($id, $base);
    }
}
