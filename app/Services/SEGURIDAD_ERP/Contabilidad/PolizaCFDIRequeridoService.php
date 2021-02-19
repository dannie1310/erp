<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\PolizaCFDIRequerido as Model;
use App\Repositories\Repository;

class PolizaCFDIRequeridoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        if (isset($data['solo_pendientes'])) {
            if($data['solo_pendientes']==="true"){
                $this->repository->whereDoesntHave("cfdi");
            }
        }

        if (isset($data['solo_asociados'])) {
            if($data['solo_asociados']==="true"){
                $this->repository->whereHas("cfdi");
            }
        }
        if (isset($data['base_datos_ctpq'])) {
            $this->repository->where([['base_datos_contpaq', 'like', '%' .$data['base_datos_ctpq']. '%' ]]);
        }
        if (isset($data['empresa_ctpq'])) {
            $this->repository->join("Contabilidad.ListaEmpresas as pol_empresa", "pol_empresa.AliasBDD","=","polizas_cfdi_requerido.base_datos_contpaq")
                ->where([['pol_empresa.Nombre', 'like', '%' .$data['empresa_ctpq']. '%' ]]);
        }
        if (isset($data['ejercicio'])) {
            $this->repository->where([['ejercicio', '=', $data['ejercicio'] ]]);
        }
        if (isset($data['periodo'])) {
            $this->repository->where([['periodo', '=', $data['periodo'] ]]);
        }
        if (isset($data['tipo_poliza'])) {
            $this->repository->where([['tipo', 'like', '%' .$data['tipo_poliza']. '%' ]]);
        }
        if (isset($data['folio_poliza'])) {
            $this->repository->where([['folio', 'like', '%' .$data['folio_poliza']. '%' ]]);
        }
        if (isset($data['fecha_poliza'])) {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha_poliza' )." 00:00:00",request( 'fecha_poliza' )." 23:59:59"]] );
        }
        return $this->repository->paginate($data);
    }

}
