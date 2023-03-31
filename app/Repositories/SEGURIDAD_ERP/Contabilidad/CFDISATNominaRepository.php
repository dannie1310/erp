<?php

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Facades\Context;
use App\Informes\CFDEmpresaMes;
use App\Informes\CFDICompleto;
use App\Informes\Fiscal\InformeCostosCFDIvsCostosBalanza;
use App\Informes\Fiscal\InformeSATLP;
use App\Informes\Fiscal\PendientesREPProveedor;
use App\Informes\Fiscal\PendientesREPEmpresa;
use App\Informes\Fiscal\PendientesREPEmpresaProveedor;
use App\Informes\Fiscal\PendientesREPProveedorEmpresa;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\catCFDI\TipoComprobante;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNomina;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNominaReceptor;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Repositories\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoRepository;
use Illuminate\Support\Facades\DB;

class CFDISATNominaRepository extends Repository implements RepositoryInterface
{
    public function __construct(CFDISATNomina $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar(array $datos)
    {
        return $this->model->registrar($datos);

    }

    public function getArchivoSQL($archivo)
    {
        return DB::raw("CONVERT(VARBINARY(MAX), '" . $archivo . "')");
    }

    public function getRFCReceptoras()
    {
        $empresas = EmpresaSAT::all()->pluck("rfc")->toArray();
        return $empresas;
    }

    public function getIdEmisor($datos_emisor)
    {
        $empresa = EmpresaSAT::where("rfc", "=", $datos_emisor["rfc"])
            ->first();

        if ($empresa) {
            return $empresa->id;
        } else {
            return -1;
        }
    }

    public function getIdReceptor($datos_receptor)
    {
        $receptor = CFDISATNominaReceptor::where("rfc", "=", $datos_receptor["rfc"])
            ->first();
        if (!$receptor) {
            $receptor = CFDISATNominaReceptor::create(
                ["rfc" => $datos_receptor["rfc"], "nombre" => $datos_receptor["nombre"]]
            );
        }
        return $receptor->id;
    }

    public function iniciaCarga($nombre_archivo)
    {
        return $this->model->carga()->create(["nombre_archivo_zip" => $nombre_archivo]);
    }

    public function finalizaCarga($carga)
    {
        EFOS::actualizaEFOS(null, $carga);
    }

    public function getIdProveedorSAT($datos, $id_empresa)
    {

        $proveedor = ProveedorSAT::where("rfc", "=", $datos["rfc"])
            ->first();
        if ($proveedor) {
            return $proveedor->id;
        } else {
            if ($id_empresa > 0) {
                $proveedor = ProveedorSAT::create(
                    $datos
                );
                return $proveedor->id;
            } else {
                return null;
            }

        }
    }

    public function getProveedorSAT($datos, $id_empresa)
    {

        $proveedor = ProveedorSAT::where("rfc", "=", $datos["rfc"])
            ->first();
        if ($proveedor) {
            return ["id_proveedor" => $proveedor->id, "nuevo" => 0];
        } else {
            if ($id_empresa > 0) {
                $proveedor = ProveedorSAT::create(
                    $datos
                );
                return ["id_proveedor" => $proveedor->id, "nuevo" => 1];
            } else {
                return null;
            }

        }
    }


    public function getEstadoEFO($rfc)
    {
        $efo = DB::connection("seguridad")->table("Finanzas.ctg_efos")
            ->where("rfc", "=", $rfc)
            ->first();
        if ($efo) {
            return $efo->estado;
        } else {
            return null;
        }
    }

    public function validaExistencia($uuid)
    {
        $cfdi_nomina = CFDISATNomina::where("uuid", "=", $uuid)->first();
        return $cfdi_nomina;
    }

    public function obtenerInformeCostosCFDIvsCostosBalanza($data)
    {
        $informe["informe"] = InformeCostosCFDIvsCostosBalanza::get($data);
        return $informe;
    }

    public function obtenerMovimientosCuentasInformeSATLP2020($data)
    {
        $informe["informe"] = InformeSATLP::getMovimientos($data);
        return $informe;
    }

    public function obtenerListaCFDICostosCFDIBalanza($data)
    {
        if ($data["tipo"] == 9) {
            $cfdi = InformeCostosCFDIvsCostosBalanza::getListaCFDIEjercicioPosterior($data);
        } else {
            $cfdi = InformeCostosCFDIvsCostosBalanza::getListaCFDI($data);
        }

        return $cfdi;
    }

    public function obtenerNumeroEmpresa()
    {
        $id_obra = Context::getIdObra();
        $base_datos = Context::getDatabase();

        $proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first();

        $configuracion = ConfiguracionObra::where('id_proyecto', '=', $proyecto->id)
            ->where('id_obra', '=', $id_obra)->first();

        if ($configuracion->numero_obra_contpaq) {
            return $configuracion->numero_obra_contpaq;
        } else {
            abort(500, "No se ha configurado el número de empresa contpaq para este proyecto en SAO. \n \n Por favor comuniquese con soporte a aplicaciones enviando un correo a la dirección: soporte_aplicaciones@desarrollo-hi.atlassian.net");
        }


    }

    public function getRFCObra()
    {
        $obra = Obra::find(Context::getIdObra());
        if ($obra) {
            return $obra->rfc;
        }
    }

}
