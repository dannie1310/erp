<?php


namespace App\Services\SEGURIDAD_ERP\IndicadoresFinanzas;

use App\Exports\FinanzasGlobal\SolicitudesPagoAplicadasExport;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Repositories\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaRepository;
use Maatwebsite\Excel\Facades\Excel;

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
        if (isset($data['base_datos'])) {
            $this->repository->where([["base_datos","like","%".$data["base_datos"]."%"]]);
        }
        if (isset($data['nombre_obra'])) {
            $this->repository->where([["nombre_obra","like","%".$data["nombre_obra"]."%"]]);
        }

        return $this->repository->paginate();
    }

    public function procesaSolicitudesPagoParaIndicador()
    {
        //esto se traslada a ETL en pentaho
        //return $this->repository->procesaSolicitudesPagoParaIndicador();
    }

    public function getIndicadorAplicadas()
    {
        return $this->repository->getIndicadorAplicadas();
    }

    public function descargarExcel()
    {

        $solicitudes_pago_pendientes_aplicar = SolicitudPagoAplicada::pendientes()
            ->registrosActivos()
            ->select("base_datos","nombre_obra","fecha_solicitud", "numero_folio","razon_social"
                ,"monto","usuario_registro","observaciones","remesa_relacionada","monto_autorizado_remesa","monto_pagado"
            ,"monto_aplicado","pendiente")->get()->toArray();
        return Excel::download(new SolicitudesPagoAplicadasExport($solicitudes_pago_pendientes_aplicar), 'solicitudes_pago_pendientes_aplicar'."_".date('dmY_His').'.xlsx');
    }

}
