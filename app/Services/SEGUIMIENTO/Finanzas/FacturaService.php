<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\FinDimIngresoCliente;
use App\Models\REPSEG\FinDimIngresoEmpresa;
use App\Models\REPSEG\FinFacIngresoFactura;
use App\Models\REPSEG\GrlMoneda;
use App\Models\REPSEG\GrlProyecto;
use App\Repositories\Repository;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FacturaService constructor.
     * @param FinFacIngresoFactura $model
     */
    public function __construct(FinFacIngresoFactura $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['idproyecto']))
        {
            $proyectos = GrlProyecto::where([['proyecto', 'LIKE', '%'.$data['idproyecto'].'%']])->pluck("idproyecto");
            $this->repository->whereIn(['idproyecto',  $proyectos]);
        }

        if(isset($data['numero']))
        {
            $this->repository->where([['numero', 'LIKE', '%' . $data['numero'] . '%']]);
        }

        if (isset($data['fecha']))
        {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
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

        if (isset($data['fecha_cobro']))
        {
            $this->repository->whereBetween( ['fecha_cobro', [ request( 'fecha_cobro' )." 00:00:00",request( 'fecha_cobro' )." 23:59:59"]] );
        }

        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}
