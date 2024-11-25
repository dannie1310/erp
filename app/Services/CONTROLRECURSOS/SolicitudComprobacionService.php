<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\Empresa;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\CONTROL_RECURSOS\SolChequeComprobacion;
use App\Repositories\Repository;

class SolicitudComprobacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param SolChequeComprobacion $model
     */
    public function __construct(SolChequeComprobacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['idserie']))
        {
            $serie = Serie::where([['Descripcion', 'LIKE', '%' . $data['idserie'] . '%']])->pluck('idseries');
            $this->repository->whereIn(['IdSerie', $serie]);
        }

        if (isset($data['idproveedor']))
        {
            $proveedor = Proveedor::where([['RazonSocial', 'LIKE', '%' . $data['idproveedor'] . '%']])->empleados()->pluck('IdProveedor');
            $this->repository->whereIn(['IdProveedor', $proveedor]);
        }

        if (isset($data['idempresa']))
        {
            $empresa = Empresa::where([['RazonSocial', 'LIKE', '%' . $data['idempresa'] . '%']])->pluck('IdEmpresa');
            $this->repository->whereIn(['IdEmpresa', $empresa]);
        }

        if (isset($data['Fecha']))
        {
            $this->repository->whereBetween( ['Fecha', [ request( 'Fecha' )." 00:00:00",request( 'Fecha' )." 23:59:59"]] );
        }

        if (isset($data['folio']))
        {
            $folio = explode("-", $data['folio']);
            if(count($folio) == 2)
            {
                $this->repository->where([['Serie', '=', $folio[0]]])->where([['Folio', 'LIKE', '%'.$folio[1].'%']]);

            }else {
                $this->repository->where([['Folio', 'LIKE', '%' . $folio[0] . '%']]);
            }
        }

        if (isset($data['importe']))
        {
            $this->repository->where([['Importe', 'LIKE', '%'.$data['total'].'%']]);
        }

        if (isset($data['idmoneda']))
        {
            $tipo = CtgMoneda::where([['moneda', 'LIKE', '%' . $data['idmoneda'] . '%']])->pluck('id');
            $this->repository->whereIn(['IdMoneda', $tipo]);
        }

        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}
