<?php

namespace App\Services\SEGUIMIENTO\Finanzas;

use App\Models\REPSEG\VwFinIngresoRegistrado;
use App\Repositories\Repository;

class VwFinIngresoRegistradoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FacturaService constructor.
     * @param VwFinIngresoRegistrado $model
     */
    public function __construct(VwFinIngresoRegistrado $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
       /* if (isset($data['idproyecto']))
        {
            $proyectos = GrlProyecto::where([['proyecto', 'LIKE', '%'.$data['idproyecto'].'%']])->pluck("idproyecto");
            $this->repository->whereIn(['idproyecto',  $proyectos]);
        }

        if(isset($data['numero']))
        {
            $this->repository->where([['numero', 'LIKE', '%' . $data['numero'] . '%']]);
        }

        if (isset($data['fecha_emision']))
        {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha_emision' )." 00:00:00",request( 'fecha_emision' )." 23:59:59"]] );
        }

        if (isset($data['idempresa']))
        {
            $empresas = FinDimIngresoEmpresa::where([['empresa', 'LIKE', '%'.$data['idempresa'].'%']])->pluck("idempresa");
            $this->repository->whereIn(['idempresa',  $empresas]);
        }

        if (isset($data['idcliente']))
        {
            $clientes = FinDimIngresoCliente::where([['cliente', 'LIKE', '%'.$data['idcliente'].'%']])->pluck("idcliente");
            $this->repository->whereIn(['idcliente',  $clientes]);
        }

        if(isset($data['descripcion']))
        {
            $this->repository->where([['descripcion', 'LIKE', '%'.$data['descripcion'].'%']]);
        }

        if (isset($data['idmoneda']))
        {
            $monedas = GrlMoneda::where([['moneda', 'LIKE', '%'.$data['idmoneda'].'%']])->pluck("idmoneda");
            $this->repository->whereIn(['idmoneda',  $monedas]);
        }

        if(isset($data['importe']))
        {
            $this->repository->where([['importe', 'LIKE', '%'.$data['importe'].'%']]);
        }

        if (isset($data['fecha']))
        {
            $this->repository->whereBetween( ['timestamp', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        */
        return $this->repository->paginate($data);
    }

}
