<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 12/05/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\IncidentesPolizas;

use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\IncidentesPolizas\IncidenteIndividualConsolidada as Model;
use App\Repositories\SEGURIDAD_ERP\IncidentesPolizas\IncidenteIndividualConsolidadaRepository as Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class IncidenteIndividualConsolidadaService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    private function store($datos)
    {
        $incidente = $this->repository->store($datos);
        return $incidente;
    }

    public function buscarDiferencias($parametros)
    {
        $polizas = $this->obtienePolizasAValidar($parametros);


        return [count($polizas)];
    }

    private function obtienePolizasAValidar($parametros)
    {
        $arreglo_para_validar =  [];
        $empresas_consolidadoras = $this->repository->getListaEmpresasConsolidadoras();
        if($parametros->tipo_busqueda == 1){
            foreach($empresas_consolidadoras as $empresa_consolidadora){
                foreach($empresa_consolidadora->empresas_consolidantes as $empresa_consolidante)
                {
                    $empresas_consolidantes[] = $empresa_consolidante;
                }
            }
            $contador =0;
            foreach($empresas_consolidantes as $empresa_consolidante)
            {
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database',$empresa_consolidante->AliasBDD);
                try{
                    $polizas = Poliza::where("Ejercicio",2019)->where("Periodo",1)->get();
                } catch (\Exception $e){

                }

                foreach($polizas as $poliza)
                {
                    #CONECTARSE A BASE CORRESPONDIENTE A LA PÓLIZA
                    DB::purge('cntpq');
                    Config::set('database.connections.cntpq.database',$empresa_consolidante->AliasBDD);
                    $movimientos = $poliza->movimientos()->orderBy("NumMovto")->get();
                    //Se cargan movimientos de poliza para consultar la cantidad correcta posteriormente a cambiar la conexión a la base de referencia
                    $poliza->load("movimientos");
                    #CONECTARSE A BASE DE REFERENCIA PARA BUSCAR LA PÓLIZA
                    DB::purge('cntpq');
                    Config::set('database.connections.cntpq.database',$empresa_consolidante->empresa_consolidadora->AliasBDD);
                    #BUSCAR PÓLIZA EN BASE DE REFERENCIA
                    try{
                        $poliza_referencia = Poliza::where("Ejercicio",$poliza->Ejercicio)->where("Periodo", $poliza->Periodo)
                            ->where("TipoPol",$poliza->TipoPol)->where("Folio",$poliza->Folio)->first();
                    } catch (\Exception $e){

                    }

                    if($poliza_referencia){
                        $movimientos_referencia = $poliza_referencia->movimientos()->orderBy("NumMovto")->get();
                        $relacion_arr = [
                            "id_poliza_a"=>$poliza->Id,
                            "base_datos_a"=>$empresa_consolidante->AliasBDD,
                            "id_poliza_b"=>$poliza_referencia->Id,
                            "base_datos_b"=>$empresa_consolidante->empresa_consolidadora->AliasBDD,
                            "tipo_relacion"=>$parametros->tipo_busqueda,
                            "tipo_busqueda"=>$parametros->tipo_busqueda,
                        ];
                        $this->repository->guardaRelacionPolizas($relacion_arr);
                        $arreglo_para_validar[$contador] = [
                            "id_poliza_a"=>$poliza->Id,
                            "base_datos_a"=>$empresa_consolidante->AliasBDD,
                            "concepto_a"=>$poliza->Concepto,
                            "suma_cargos_a"=>$poliza->Cargos,
                            "suma_abonos_a"=>$poliza->Abonos,
                            "no_movtos_a"=>count($poliza->movimientos),
                            "id_poliza_b"=>$poliza_referencia->Id,
                            "base_datos_b"=>$empresa_consolidante->empresa_consolidadora->AliasBDD,
                            "concepto_b"=>$poliza_referencia->Concepto,
                            "suma_cargos_b"=>$poliza_referencia->Cargos,
                            "suma_abonos_b"=>$poliza_referencia->Abonos,
                            "no_movtos_b"=>count($poliza_referencia->movimientos),
                        ];
                        if($arreglo_para_validar[$contador]["no_movtos_a"] == $arreglo_para_validar[$contador]["no_movtos_b"] )
                        {
                            $contador_movimientos = 0;
                            foreach($movimientos as $movimiento){
                                DB::purge('cntpq');
                                Config::set('database.connections.cntpq.database',$empresa_consolidante->AliasBDD);
                                $movimiento->load("cuenta");

                                DB::purge('cntpq');
                                Config::set('database.connections.cntpq.database',$empresa_consolidante->empresa_consolidadora->AliasBDD);
                                $movimientos_referencia[$contador_movimientos]->load("cuenta");
                                $arreglo_para_validar[$contador]["movimientos"][$contador_movimientos] = [
                                    "id_a"=>$movimiento->Id,
                                    "tipo_a"=>$movimiento->TipoMovto,
                                    "importe_a"=>$movimiento->Importe,
                                    "referencia_a"=>$movimiento->Referencia,
                                    "concepto_a"=>$movimiento->Concepto,
                                    "codigo_cuenta_a"=>$movimiento->cuenta->Codigo,
                                    "nombre_cuenta_a"=>$movimiento->cuenta->Nombre,

                                    "id_b"=>$movimientos_referencia[$contador_movimientos]->Id,
                                    "tipo_b"=>$movimientos_referencia[$contador_movimientos]->TipoMovto,
                                    "importe_b"=>$movimientos_referencia[$contador_movimientos]->Importe,
                                    "referencia_b"=>$movimientos_referencia[$contador_movimientos]->Referencia,
                                    "concepto_b"=>$movimientos_referencia[$contador_movimientos]->Concepto,
                                    "codigo_cuenta_b"=>$movimientos_referencia[$contador_movimientos]->cuenta->Codigo,
                                    "nombre_cuenta_b"=>$movimientos_referencia[$contador_movimientos]->cuenta->Nombre,
                                ];
                                $contador_movimientos++;
                            }

                        } else {
                            $incidente_arr = [
                                "id_poliza"=>$poliza->Id,
                                "base_datos"=>$empresa_consolidante->AliasBDD,
                                "base_datos_referencia"=>$empresa_consolidante->empresa_consolidadora->AliasBDD,
                                "id_tipo_incidente"=>4,
                                "tipo_busqueda"=>$parametros->tipo_busqueda,
                                "observaciones"=>$arreglo_para_validar[$contador]["no_movtos_a"] .'-'. $arreglo_para_validar[$contador]["no_movtos_b"].' '.$poliza->Id.' '.$poliza_referencia->Id
                            ];
                            $this->repository->create($incidente_arr);
                        }

                    } else {
                        $incidente_arr = [
                            "id_poliza"=>$poliza->Id,
                            "base_datos"=>$empresa_consolidante->AliasBDD,
                            "base_datos_referencia"=>$empresa_consolidante->empresa_consolidadora->AliasBDD,
                            "id_tipo_incidente"=>1,
                            "tipo_busqueda"=>$parametros->tipo_busqueda
                        ];
                        $this->repository->create($incidente_arr);
                    }
                    $contador++;
                }
            }
        }
        return $arreglo_para_validar;
    }

}