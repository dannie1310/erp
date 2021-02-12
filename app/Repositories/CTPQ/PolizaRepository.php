<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/02/2020
 * Time: 03:52 PM
 */

namespace App\Repositories\CTPQ;

use App\Models\CTPQ\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa as EmpresaERP;
use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDI;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDIPartida;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class PolizaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Poliza $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function update(array $datos, $id)
    {
        return $this->show($id)->actualiza($datos);
    }

    public function find(array $datos){
        return $this->model->where("Folio","=",$datos["folio"])
            ->where("Fecha","=",$datos["fecha"])
            ->where("TipoPol","=",$datos["tipo"])
            ->get();
    }

    public function generaSolicitudAsociacion()
    {
        if (!SolicitudAsociacionCFDI::getSolicitudActiva()) {
            return SolicitudAsociacionCFDI::create(["usuario_inicio" => auth()->id(), "fecha_hora_inicio" => date('Y-m-d H:i:s')]);
        } else {
            return null;
        }
    }

    public function getListaEmpresas()
    {
        //$bases = Empresa::all()->pluck("AliasBDD")->take(20);
        //$bases = Empresa::where("AliasBDD","like","ctPCO811231EI4_014")->pluck("AliasBDD")->take(20);
        /*$bases = Empresa::where("AliasBDD","like","ctPCO811231EI4_01%")
            ->where("AliasBDD","not like","%HST%")
            ->pluck("AliasBDD","Nombre")->take(20);*/
        $bases = EmpresaERP::paraSincronizacionCFDIPoliza()
            ->pluck("AliasBDD","Nombre");
        return $bases;
    }

    public function generaPeticionesAsociacion($data)
    {
        return SolicitudAsociacionCFDIPartida::create($data);
    }
}
