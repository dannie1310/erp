<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Repositories\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIRepository as Repository;

class SolicitudRecepcionCFDIService
{
    protected $repository;

    public function __construct(SolicitudRecepcionCFDI $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha_hora_registro', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        if (isset($data['folio'])) {
            $this->repository->where([["numero_folio","=",$data["folio"]]]);
        }
        if (isset($data['uuid'])) {
            $cfdi = CFDSAT::porProveedor(auth()->user()->id_empresa)->enSolicitud()
                ->where([['uuid', 'LIKE', '%' . $data['uuid'] . '%']])->pluck("id");
            $this->repository->whereIn(['id_cfdi', $cfdi]);
        }
        return $this->repository->paginate();
    }

    public function store(array $data)
    {
        return $this->repository->registrar($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

}
