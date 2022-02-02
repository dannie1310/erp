<?php


namespace App\Services\SEGURIDAD_ERP\IndicadoresFinanzas;

use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Repositories\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaRepository;

class SolicitudPagoAplicadaService
{
    protected $repository;

    public function __construct(SolicitudPagoAplicada $model)
    {
        $this->repository = new SolicitudPagoAplicadaRepository($model);
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

        return $this->repository->paginate();
    }

    public function procesaSolicitudesPagoParaIndicador()
    {
        return $this->repository->procesaSolicitudesPagoParaIndicador();

    }

    public function getIndicadorAplicadas()
    {
        return $this->repository->getIndicadorAplicadas();
    }

}
