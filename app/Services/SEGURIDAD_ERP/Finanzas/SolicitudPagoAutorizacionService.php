<?php

namespace App\Services\SEGURIDAD_ERP\Finanzas;


use App\Models\CADECO\Empresa;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion;
use App\Repositories\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionRepository;


class SolicitudPagoAutorizacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param SolicitudPagoAutorizacion $model
     */
    public function __construct(SolicitudPagoAutorizacion $model)
    {
        $this->repository = new SolicitudPagoAutorizacionRepository($model);
    }

    public function paginate($data)
    {
        $solicitudes = $this->repository;

        if(isset($data['numero_folio'])){
            $solicitudes = $solicitudes->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['razon_social'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();
            foreach ($empresa as $e){
                $solicitudes = $solicitudes->whereOr([['razon_social', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['observaciones'])){
            $solicitudes = $solicitudes->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }

        return $solicitudes->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->withoutGlobalScopes()->show($id);
    }

    public function index()
    {
        return $this->repository->all();
    }

    public function porAutorizar()
    {
        return $this->repository->porAutorizar();
    }
}
