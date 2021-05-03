<?php

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Informes\ContabilidadGeneral\SaldosNegativos;
use App\Informes\EFOSEmpresaInforme;
use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativo;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CuentaSaldoNegativoRepository extends Repository implements RepositoryInterface
{
    public function __construct(CuentaSaldoNegativo $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function sincronizar()
    {
        DB::connection('seguridad')->beginTransaction();

        DB::connection("seguridad")->update("update Contabilidad.cuentas_saldos_negativos set estado = 0");

        $empresas = Empresa::all();
        $insertados = 0;

        $values = [];

        foreach ($empresas as $empresa){
            $values = [];
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            try{
                $values = DB::connection("cntpq")->select("
                select IdCuenta as id_cuenta, Cuentas.Nombre as nombre_cuenta, Cuentas.Codigo as codigo_cuenta, Cuentas.Tipo as tipo, sum (
                    CASE MovimientosPoliza.TipoMovto
                        WHEN 1 THEN MovimientosPoliza.Importe * -1
                        WHEN 0 THEN MovimientosPoliza.Importe
                    END) as saldo,
                       '".$empresa->AliasBDD."' as base_datos,
                       '".$empresa->Nombre."' as nombre_empresa
                from dbo.MovimientosPoliza join dbo.Cuentas on(Cuentas.Id = MovimientosPoliza.IdCuenta)
                group by IdCuenta, Nombre, Codigo, Tipo
                having sum (
          CASE MovimientosPoliza.TipoMovto
             WHEN 1 THEN MovimientosPoliza.Importe * -1
             WHEN 0 THEN MovimientosPoliza.Importe
          END)<-0.99
                ");
                $values = array_map(function ($value) {
                    return (array)$value;
                }, $values);


            } catch (\Exception $e)
            {

            }
            if(count($values)>0){
                try{
                    CuentaSaldoNegativo::insert($values);
                    $insertados ++;
                }catch (\Exception $e)
                {
                    abort(500, $e->getMessage());
                }
            }
        }

        DB::connection('seguridad')->commit();
        return [
            "insertados"=>$insertados
        ];
    }

    public function obtenerInforme($id)
    {
        $cuenta = $this->show($id);
        $informe["informe"]["encabezado"] = [
            "base_datos"=>$cuenta->base_datos
            , "empresa"=>$cuenta->nombre_empresa
            , "codigo_cuenta" =>$cuenta->codigo_cuenta
            , "nombre_cuenta" =>$cuenta->nombre_cuenta
            , "tipo_cuenta" =>$cuenta->tipo
            , "saldo_cuenta" =>$cuenta->saldo_real
            , "saldo_cuenta_format" =>$cuenta->saldo_real_format
        ];
        $informe["informe"]["data"] = SaldosNegativos::get($cuenta);
        return $informe;
    }

    public function obtenerInformeMovimientos($data)
    {
        $informe = SaldosNegativos::getMovimientos($data);
        return $informe;
    }
}
