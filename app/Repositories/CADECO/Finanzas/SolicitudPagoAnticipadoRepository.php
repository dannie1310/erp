<?php


namespace App\Repositories\CADECO\Finanzas;



use App\Informes\Finanzas\PagosAnticipados;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\SEGURIDAD_ERP\Compras\Configuracion;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativo;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\DB;


class SolicitudPagoAnticipadoRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudPagoAnticipado $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getIndicadorAplicadas($base_datos, $id_obra)
    {
        $informe = PagosAnticipados::getIndicadorAplicadas($base_datos, $id_obra);
        return $informe;
    }

    public function solicitarAutorizacion($id){
        return $this->model->solicitarAutorizacion($id);
    }

}
