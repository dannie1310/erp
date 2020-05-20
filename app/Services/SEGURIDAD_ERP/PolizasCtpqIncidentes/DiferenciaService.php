<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 12/05/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\PolizasCtpqIncidentes;

use App\Jobs\ProcessBusquedaDiferenciasPolizas;
use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia as Model;
use App\Repositories\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaRepository as Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DiferenciaService
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
        $parametros = collect((object)["tipo_busqueda"=>1]) ;
        ini_set('max_execution_time', '900000');
        $polizas = $this->obtienePolizasAValidar($parametros);
        $this->detectarDiferencias($polizas, $parametros);
        return [count($polizas)];
        /*ProcessBusquedaDiferenciasPolizas::dispatch($this)
        ->delay(now()->addMinutes(1));*/
    }

    public function procesarBusquedaDiferencias()
    {
        $parametros = collect((object)["tipo_busqueda"=>1]) ;
        ini_set('max_execution_time', '900000');
        $polizas = $this->obtienePolizasAValidar($parametros);
        $this->detectarDiferencias($polizas, $parametros);
        return [count($polizas)];
    }

    private function detectarDiferencias($arreglo_polizas, $parametros)
    {
        foreach ($arreglo_polizas as $arreglo_poliza) {
            if (trim($arreglo_poliza["concepto_a"]) != trim($arreglo_poliza["concepto_b"])) {
                $incidente_arr = [
                    "id_poliza" => $arreglo_poliza["id_poliza_a"],
                    "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                    "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                    "id_tipo" => 2,
                    "tipo_busqueda" => $parametros["tipo_busqueda"],
                    "observaciones" => 'a: ' . $arreglo_poliza["concepto_a"] . ' b: ' . $arreglo_poliza["concepto_b"],
                ];
                $this->repository->create($incidente_arr);
            } else {
                $correccion_arr = [
                    "id_poliza" => $arreglo_poliza["id_poliza_a"],
                    "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                    "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                    "id_tipo" => 2,
                    "tipo_busqueda" => $parametros["tipo_busqueda"],
                ];
                $this->repository->corrige($correccion_arr);
            }

            if ($arreglo_poliza["suma_cargos_a"] != $arreglo_poliza["suma_abonos_b"]) {
                $incidente_arr = [
                    "id_poliza" => $arreglo_poliza["id_poliza_a"],
                    "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                    "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                    "id_tipo" => 3,
                    "tipo_busqueda" => $parametros["tipo_busqueda"],
                    "observaciones" => 'a: ' . $arreglo_poliza["suma_cargos_a"] . ' b: ' . $arreglo_poliza["suma_abonos_b"],
                ];
                $this->repository->create($incidente_arr);
            } else {
                $correccion_arr = [
                    "id_poliza" => $arreglo_poliza["id_poliza_a"],
                    "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                    "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                    "id_tipo" => 3,
                    "tipo_busqueda" => $parametros["tipo_busqueda"],
                ];
                $this->repository->corrige($correccion_arr);
            }

            if ($arreglo_poliza["no_movtos_a"] != $arreglo_poliza["no_movtos_b"]) {
                $incidente_arr = [
                    "id_poliza" => $arreglo_poliza["id_poliza_a"],
                    "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                    "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                    "id_tipo" => 4,
                    "tipo_busqueda" => $parametros["tipo_busqueda"],
                    "observaciones" => 'a: ' . $arreglo_poliza["no_movtos_a"] . ' b: ' . $arreglo_poliza["no_movtos_b"],
                ];
                $this->repository->create($incidente_arr);
            } else {
                $correccion_arr = [
                    "id_poliza" => $arreglo_poliza["id_poliza_a"],
                    "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                    "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                    "id_tipo" => 4,
                    "tipo_busqueda" => $parametros["tipo_busqueda"],
                ];
                $this->repository->corrige($correccion_arr);
                if (key_exists("movimientos", $arreglo_poliza)) {

                    foreach ($arreglo_poliza["movimientos"] as $arreglo_movimiento) {
                        $relacion_arr = [
                            "id_movimiento_a" => $arreglo_movimiento["id_a"],
                            "base_datos_a" => $arreglo_poliza["base_datos_a"],
                            "id_movimiento_b" => $arreglo_movimiento["id_b"],
                            "base_datos_b" => $arreglo_poliza["base_datos_b"],
                            "tipo_relacion" => $parametros["tipo_busqueda"],
                            "tipo_busqueda" => $parametros["tipo_busqueda"],
                        ];
                        $this->repository->guardaRelacionMovimientos($relacion_arr);
                        if ($arreglo_movimiento["codigo_cuenta_a"] != $arreglo_movimiento["codigo_cuenta_b"]) {
                            $incidente_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 6,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                                "observaciones" => 'a: ' . $arreglo_movimiento["codigo_cuenta_a"] . ' b: ' . $arreglo_movimiento["codigo_cuenta_b"],
                            ];
                            $this->repository->create($incidente_arr);
                        } else {
                            $correccion_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 6,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                            ];
                            $this->repository->corrige($correccion_arr);
                        }

                        if (trim($arreglo_movimiento["nombre_cuenta_a"]) != trim($arreglo_movimiento["nombre_cuenta_b"])) {
                            $incidente_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 7,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                                "observaciones" => 'a: ' . $arreglo_movimiento["nombre_cuenta_a"] . ' b: ' . $arreglo_movimiento["nombre_cuenta_b"],
                            ];
                            $this->repository->create($incidente_arr);
                        } else {
                            $correccion_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 7,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                            ];
                            $this->repository->corrige($correccion_arr);
                        }

                        if (trim($arreglo_movimiento["referencia_a"]) != trim($arreglo_movimiento["referencia_b"])) {
                            $incidente_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 8,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                                "observaciones" => 'a: ' . $arreglo_movimiento["referencia_a"] . ' b: ' . $arreglo_movimiento["referencia_b"],
                            ];
                            $this->repository->create($incidente_arr);
                        } else {
                            $correccion_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 8,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                            ];
                            $this->repository->corrige($correccion_arr);
                        }

                        if (trim($arreglo_movimiento["concepto_a"]) != trim($arreglo_movimiento["concepto_b"])) {
                            $incidente_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 9,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                                "observaciones" => 'a: ' . $arreglo_movimiento["concepto_a"] . ' b: ' . $arreglo_movimiento["concepto_b"],
                            ];
                            $this->repository->create($incidente_arr);
                        } else {
                            $correccion_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 9,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                            ];
                            $this->repository->corrige($correccion_arr);
                        }

                        if ($arreglo_movimiento["tipo_a"] != $arreglo_movimiento["tipo_b"]) {
                            $incidente_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 10,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                                "observaciones" => 'a: ' . $arreglo_movimiento["tipo_a"] . ' b: ' . $arreglo_movimiento["tipo_b"],
                            ];
                            $this->repository->create($incidente_arr);
                        } else {
                            $correccion_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 10,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                            ];
                            $this->repository->corrige($correccion_arr);
                        }

                        if ($arreglo_movimiento["importe_a"] != $arreglo_movimiento["importe_b"]) {
                            $incidente_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 11,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                                "observaciones" => 'a: ' . $arreglo_movimiento["importe_a"] . ' b: ' . $arreglo_movimiento["importe_b"],
                            ];
                            $this->repository->create($incidente_arr);
                        } else {
                            $correccion_arr = [
                                "id_poliza" => $arreglo_poliza["id_poliza_a"],
                                "id_movimiento" => $arreglo_movimiento["id_a"],
                                "base_datos_revisada" => $arreglo_poliza["base_datos_a"],
                                "base_datos_referencia" => $arreglo_poliza["base_datos_b"],
                                "id_tipo" => 11,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                            ];
                            $this->repository->corrige($correccion_arr);
                        }
                    }
                }
            }
        }
    }

    private function obtienePolizasAValidar($parametros)
    {
        $arreglo_para_validar = [];
        $empresas_consolidadoras = $this->repository->getListaEmpresasConsolidadoras();
        if ($parametros["tipo_busqueda"] == 1) {
            foreach ($empresas_consolidadoras as $empresa_consolidadora) {
                foreach ($empresa_consolidadora->empresas_consolidantes as $empresa_consolidante) {
                    $empresas_consolidantes[] = $empresa_consolidante;
                }
            }
            $contador = 0;
            foreach ($empresas_consolidantes as $empresa_consolidante) {
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $empresa_consolidante->AliasBDD);
                try {
                    $polizas = Poliza::where("Ejercicio", 2008)->where("Periodo", 12)->get();
                } catch (\Exception $e) {

                }

                foreach ($polizas as $poliza) {
                    #CONECTARSE A BASE CORRESPONDIENTE A LA PÓLIZA
                    DB::purge('cntpq');
                    Config::set('database.connections.cntpq.database', $empresa_consolidante->AliasBDD);
                    $movimientos = $poliza->movimientos()->orderBy("NumMovto")->get();
                    //Se cargan movimientos de poliza para consultar la cantidad correcta posteriormente a cambiar la conexión a la base de referencia
                    $poliza->load("movimientos");
                    #CONECTARSE A BASE DE REFERENCIA PARA BUSCAR LA PÓLIZA
                    DB::purge('cntpq');
                    Config::set('database.connections.cntpq.database', $empresa_consolidante->empresa_consolidadora->AliasBDD);
                    #BUSCAR PÓLIZA EN BASE DE REFERENCIA
                    try {
                        $poliza_referencia = Poliza::where("Ejercicio", $poliza->Ejercicio)->where("Periodo", $poliza->Periodo)
                            ->where("TipoPol", $poliza->TipoPol)->where("Folio", $poliza->Folio)->first();
                    } catch (\Exception $e) {

                    }

                    if ($poliza_referencia) {
                        $movimientos_referencia = $poliza_referencia->movimientos()->orderBy("NumMovto")->get();
                        $relacion_arr = [
                            "id_poliza_a" => $poliza->Id,
                            "base_datos_a" => $empresa_consolidante->AliasBDD,
                            "id_poliza_b" => $poliza_referencia->Id,
                            "base_datos_b" => $empresa_consolidante->empresa_consolidadora->AliasBDD,
                            "tipo_relacion" => $parametros["tipo_busqueda"],
                            "tipo_busqueda" => $parametros["tipo_busqueda"],
                        ];
                        $this->repository->guardaRelacionPolizas($relacion_arr);
                        $arreglo_para_validar[$contador] = [
                            "id_poliza_a" => $poliza->Id,
                            "base_datos_a" => $empresa_consolidante->AliasBDD,
                            "concepto_a" => $poliza->Concepto,
                            "suma_cargos_a" => $poliza->Cargos,
                            "suma_abonos_a" => $poliza->Abonos,
                            "no_movtos_a" => count($poliza->movimientos),
                            "id_poliza_b" => $poliza_referencia->Id,
                            "base_datos_b" => $empresa_consolidante->empresa_consolidadora->AliasBDD,
                            "concepto_b" => $poliza_referencia->Concepto,
                            "suma_cargos_b" => $poliza_referencia->Cargos,
                            "suma_abonos_b" => $poliza_referencia->Abonos,
                            "no_movtos_b" => count($poliza_referencia->movimientos),
                        ];
                        if ($arreglo_para_validar[$contador]["no_movtos_a"] == $arreglo_para_validar[$contador]["no_movtos_b"]) {
                            $contador_movimientos = 0;
                            foreach ($movimientos as $movimiento) {
                                DB::purge('cntpq');
                                Config::set('database.connections.cntpq.database', $empresa_consolidante->AliasBDD);
                                $movimiento->load("cuenta");

                                DB::purge('cntpq');
                                Config::set('database.connections.cntpq.database', $empresa_consolidante->empresa_consolidadora->AliasBDD);
                                $movimientos_referencia[$contador_movimientos]->load("cuenta");
                                $arreglo_para_validar[$contador]["movimientos"][$contador_movimientos] = [
                                    "id_a" => $movimiento->Id,
                                    "tipo_a" => $movimiento->TipoMovto,
                                    "importe_a" => $movimiento->Importe,
                                    "referencia_a" => $movimiento->Referencia,
                                    "concepto_a" => $movimiento->Concepto,
                                    "codigo_cuenta_a" => $movimiento->cuenta->Codigo,
                                    "nombre_cuenta_a" => $movimiento->cuenta->Nombre,

                                    "id_b" => $movimientos_referencia[$contador_movimientos]->Id,
                                    "tipo_b" => $movimientos_referencia[$contador_movimientos]->TipoMovto,
                                    "importe_b" => $movimientos_referencia[$contador_movimientos]->Importe,
                                    "referencia_b" => $movimientos_referencia[$contador_movimientos]->Referencia,
                                    "concepto_b" => $movimientos_referencia[$contador_movimientos]->Concepto,
                                    "codigo_cuenta_b" => $movimientos_referencia[$contador_movimientos]->cuenta->Codigo,
                                    "nombre_cuenta_b" => $movimientos_referencia[$contador_movimientos]->cuenta->Nombre,
                                ];
                                $contador_movimientos++;
                            }

                        } else {
                            $incidente_arr = [
                                "id_poliza" => $poliza->Id,
                                "base_datos_revisada" => $empresa_consolidante->AliasBDD,
                                "base_datos_referencia" => $empresa_consolidante->empresa_consolidadora->AliasBDD,
                                "id_tipo" => 4,
                                "tipo_busqueda" => $parametros["tipo_busqueda"],
                                "observaciones" => $arreglo_para_validar[$contador]["no_movtos_a"] . '-' . $arreglo_para_validar[$contador]["no_movtos_b"] . ' ' . $poliza->Id . ' ' . $poliza_referencia->Id
                            ];
                            $this->repository->create($incidente_arr);
                        }

                    } else {
                        $incidente_arr = [
                            "id_poliza" => $poliza->Id,
                            "base_datos_revisada" => $empresa_consolidante->AliasBDD,
                            "base_datos_referencia" => $empresa_consolidante->empresa_consolidadora->AliasBDD,
                            "id_tipo" => 1,
                            "tipo_busqueda" => $parametros["tipo_busqueda"]
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