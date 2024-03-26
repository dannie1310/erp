<?php

namespace App\Repositories\CTPQ;

use App\Models\CTPQ\Cuenta;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCuentaProveedor;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCuentaProveedorPartida;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CuentaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Cuenta $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getCuentasPasivo($id_empresa)
    {
       return Cuenta::cuentasPasivo($id_empresa)->get();
    }

    public function getCuentas($data)
    {
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $empresa = \App\Models\CTPQ\Empresa::find($empresaLocal->IdEmpresaContpaq);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
        return Cuenta::cuentasPasivo($data["id_empresa"])->get();
    }

    public function generaSolicitudAsociacion($data)
    {
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        //$data['id_empresa'] = $empresaLocal->IdEmpresaContpaq;
        $empresa = \App\Models\CTPQ\Empresa::find($empresaLocal->IdEmpresaContpaq);

        if (!SolicitudAsociacionCuentaProveedor::getSolicitudActiva($data["id_empresa"])) {
            return SolicitudAsociacionCuentaProveedor::create([
                "usuario_inicio" => auth()->id()
                , "id_empresa_contpaq" => $empresaLocal->IdEmpresaContpaq
                , "base_datos" => $empresa->AliasBDD
                , "fecha_hora_inicio" => date('Y-m-d H:i:s')]);
        } else {
            return null;
        }
    }

    public function generaPartidasAsociacion($data)
    {
        return SolicitudAsociacionCuentaProveedorPartida::create($data);

    }
}
