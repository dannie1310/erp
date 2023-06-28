<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/02/2020
 * Time: 03:52 PM
 */

namespace App\Repositories\CTPQ;

use App\Models\CADECO\Movimiento;
use App\Models\CTPQ\Cuenta;
use App\Models\CTPQ\Empresa;
use App\Models\CTPQ\Parametro;
use App\Models\CTPQ\PolizaMovimiento;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaContpaqProvedorSat;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa as EmpresaERP;
use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDI;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDIPartida;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

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
        //$bases = Empresa::where("AliasBDD","like","ctPCO811231EI4_014%")->pluck("AliasBDD","Nombre")->take(20);
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

    public function listarPosiblesCFDI($id_empresa, $id_poliza)
    {
        $empresa = EmpresaERP::find($id_empresa);
        $id_empresa_contpaq = $empresa->IdEmpresaContpaq;

        if($empresa)
        {
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
        }

        $poliza = Poliza::find($id_poliza);

        $id_cuentas = PolizaMovimiento::where("IdPoliza","=",$poliza->Id)
            ->get()
            ->pluck("IdCuenta")
            ->toArray();

        $id_proveedor_sat = CuentaContpaqProvedorSat::whereIn("id_cuenta_contpaq", $id_cuentas)
            ->where("id_empresa_contpaq","=",$id_empresa_contpaq)
            ->get()
            ->pluck("id_proveedor_sat")
            ->toArray();

        $importes = PolizaMovimiento::where("IdPoliza","=",$poliza->Id)
            ->get()
            ->pluck("Importe")->toArray();

        $cfdis = CFDSAT::
        join("Contabilidad.proveedores_sat","proveedores_sat.id","cfd_sat.id_proveedor_sat")
            ->where("cancelado","=",0)
            ->whereIn("id_proveedor_sat",$id_proveedor_sat)
            ->orWhereIn("total",$importes)
            ->orWhereIn("importe_iva",$importes)
            ->selectRaw("cfd_sat.id_proveedor_sat, cfd_sat.id, cfd_sat.uuid, cfd_sat.importe_iva, cfd_sat.total,cfd_sat.conceptos_txt
            ,cfd_sat.serie, cfd_sat.folio, proveedores_sat.rfc, proveedores_sat.razon_social
            , FORMAT(cfd_sat.fecha,'dd-MM-yyyy') as fecha_cfdi, 1 as grado_coincidencia, 0 as seleccionado, cfd_sat.tipo_comprobante")
            ->orderBy("cfd_sat.total")
        ->get();

        foreach ($cfdis as $cfdi)
        {
            $cfdi->seleccionado = false;
            if(in_array($cfdi->total, $importes) || in_array($cfdi->importe_iva, $importes))
            {
                $cfdi->grado_coincidencia += 1;
            }
            if(in_array($cfdi->id_proveedor_sat, $id_proveedor_sat))
            {
                $cfdi->grado_coincidencia += 1;
            }
        }

       $cfdis->sortByDesc("grado_coincidencia");

        return $cfdis;

    }

    public function asociarCFDI($data)
    {
        return $this->model->asociarCFDI($data);
    }
}
