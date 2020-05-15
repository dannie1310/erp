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


        return [$polizas];
    }

    private function obtienePolizasAValidar($parametros)
    {
        $polizas =  [];
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
                    $polizas = Poliza::where("Ejercicio",2019)->get();
                } catch (\Exception $e){

                }

                foreach($polizas as $poliza)
                {
                    $contador++;
                    $movimientos = $poliza->movimientos;
                    #CONECTARSE A BASE DE REFERENCIA
                    DB::purge('cntpq');
                    Config::set('database.connections.cntpq.database',$empresa_consolidante->empresa_consolidadora->AliasBDD);
                    #BUSCAR PÓLIZA EN BASE DE REFERENCIA
                    try{
                        $poliza_referencia = Poliza::where("Ejercicio",$poliza->Ejercicio)->where("Periodo", $poliza->Periodo)
                            ->where("TipoPol",$poliza->TipoPol)->where("Folio",$poliza->Folio)->first();
                    } catch (\Exception $e){

                    }

                    if($poliza_referencia){
                        $relacion_arr = [
                            "id_poliza_a"=>$poliza->Id,
                            "base_datos_a"=>$empresa_consolidante->AliasBDD,
                            "id_poliza_b"=>$poliza_referencia->Id,
                            "base_datos_b"=>$empresa_consolidante->empresa_consolidadora->AliasBDD,
                            "tipo_relacion"=>$parametros->tipo_busqueda,
                            "tipo_busqueda"=>$parametros->tipo_busqueda,
                        ];
                        $this->repository->guardaRelacionPolizas($relacion_arr);

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

                }
            }


        }
        return $contador;
    }

}