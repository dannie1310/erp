<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 12/05/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\PolizasCtpqIncidentes;

use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use stdClass;
use App\Models\CTPQ\Poliza;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use App\Jobs\ProcessBusquedaDiferenciasPolizas;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\PDF\ContabilidadGeneral\PolizaFormatoOriginal;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\PDF\ContabilidadGeneral\InformeDiferenciasPolizas;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\CtgTipo;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Busqueda;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia as Model;
use App\Repositories\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaRepository as Repository;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionMovimientos;
use App\Utils\BusquedaDiferenciasPolizas;

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

        if (isset($data['id_empresa'])) {
            $empresa = Empresa::find($data['id_empresa']);

            $this->repository->where([['base_datos_revisada', '=', $empresa->AliasBDD]]);
        }
        if (isset($data['id_tipo_diferencia'])) {
            $this->repository->where([['id_tipo', '=', $data['id_tipo_diferencia']]]);
        }
        if (isset($data['ejercicio'])) {
            $this->repository->where([['ejercicio', '=', $data['ejercicio'] ]]);
        }
        if (isset($data['periodo'])) {
            $this->repository->where([['periodo', '=', $data['periodo'] ]]);
        }
        if (isset($data['folio_poliza'])) {
            $this->repository->where([['folio_poliza', '=', $data['folio_poliza'] ]]);
        }
        if (isset($data['observaciones'])) {
            $this->repository->where([['observaciones', 'like', '%'.$data['observaciones'].'%' ]]);
        }
        if (isset($data['tipo_poliza'])) {
            $this->repository->where([['tipo_poliza', 'like', '%'.$data['tipo_poliza'].'%' ]]);
        }

        return $this->repository->paginate($data);
    }

    private function store($datos)
    {
        $incidente = $this->repository->store($datos);
        return $incidente;
    }

    public function buscarDiferencias($parametros)
    {
        /*$busqueda = Busqueda::find(3845);
        $busqueda->procesarBusquedaDiferencias();*/
        $datos_lote = [];
        /*$lote = LoteBusqueda::find(1);
        $lote->generaSolicitudesCambio();*/
        /*29097 ok*/
        /*error 19195*/
        /*$relaciones["relacion_poliza"] = RelacionPolizas::where("id_poliza_a","=",19195)
            ->where("base_datos_a","=", 'ctPCO811231EI4_014')->first();
        $relaciones["relaciones_movimientos"] = RelacionMovimientos::where("id_poliza_a","=",19195)
            ->where("base_datos_a","=", 'ctPCO811231EI4_014')->get();

        $busqueda = New BusquedaDiferenciasPolizas($relaciones);
        $impedir_busqueda = $busqueda->buscarDiferenciasPolizas();*/

        $lote =LoteBusqueda::getLoteActivo();
        if(!$lote){
            $lote = $this->generaPeticionesBusquedas($parametros);
            $datos_lote = [
                "folio" =>$lote->id,
                "tipo_busqueda" => $lote->tipo_str,
                "usuario_inicio" =>$lote->usuario->nombre_completo,
                "fecha_hora_inicio"=>$lote->fecha_hora_inicio_format,
                "mensaje" =>"Proceso de búsqueda generado éxitosamente, se le enviará un correo con los resultados al finalizar"
            ];
        } else {
            $datos_lote = [
                "folio" =>$lote->id,
                "tipo_busqueda" => $lote->tipo_str,
                "usuario_inicio" =>$lote->usuario->nombre_completo,
                "fecha_hora_inicio"=>$lote->fecha_hora_inicio_format,
                "mensaje" =>"Existe un proceso de búsqueda activo, favor de esperar"
            ];
        }
        return $datos_lote;
    }

    public function actualizaDiferencias()
    {
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);
        $cantidad = Diferencia::count();
        $take = 1000;

        for ($i = 0; $i <= ($cantidad); $i += $take) {
            //dd($i, $cantidad, $take);
            $cfd = Diferencia::skip($i)->take($take)->get();
            //dd(count($cfd));
            foreach ($cfd as $rcfd) {

                try{
                    $rcfd->ejercicio = $rcfd->poliza->Ejercicio;
                    $rcfd->periodo = $rcfd->poliza->Periodo;
                    $rcfd->tipo_poliza = $rcfd->poliza->tipo_poliza->Nombre;
                    $rcfd->folio_poliza = $rcfd->poliza->Folio;
                    $rcfd->fecha_poliza = $rcfd->poliza->Fecha;
                    $rcfd->save();

                }catch (\Exception $e) {

                }
            }
        }
    }

    private function generaPeticionesBusquedas($parametros)
    {
        $lote = $this->repository->generaLoteBusqueda($parametros->tipo_busqueda);
        if($parametros->tipo_busqueda == 1) {
            $empresas_consolidantes = $this->repository->getListaEmpresasConsolidantes();
            foreach ($empresas_consolidantes as $empresa_consolidante) {
                $ejercicios = $empresa_consolidante->ejercicios;
                foreach ($ejercicios as $ejercicio) {
                    for ($periodo = 1; $periodo <= 12; $periodo++) {
                        $data = [
                            "id_tipo_busqueda" => 1,
                            "id_lote" => $lote->id,
                            "ejercicio" => $ejercicio,
                            "periodo" => $periodo,
                            "base_datos_busqueda" => $empresa_consolidante->AliasBDD,
                            "base_datos_referencia" => $empresa_consolidante->empresa_consolidadora->AliasBDD
                        ];
                        $busqueda = $this->repository->generaPeticionesBusquedas($data);
                        //$busqueda->procesarBusquedaDiferencias();
                        //if($periodo)
                        ProcessBusquedaDiferenciasPolizas::dispatch($busqueda)->onQueue($this->getNombreCola($ejercicio, $periodo));
                        //ProcessBusquedaDiferenciasPolizas::dispatch($busqueda);
                    }

                }
            }
            if(!count($lote->busquedas)>0){
                $lote->finaliza();
            }
        } else if($parametros->tipo_busqueda == 2) {
            $empresas_individuales = $this->repository->getListaEmpresasIndividuales();
            foreach ($empresas_individuales as $empresas_individual) {
                $ejercicios = $empresas_individual->ejercicios;
                foreach ($ejercicios as $ejercicio) {
                    for ($periodo = 1; $periodo <= 12; $periodo++) {
                        $data = [
                            "id_tipo_busqueda" => 2,
                            "id_lote" => $lote->id,
                            "ejercicio" => $ejercicio,
                            "periodo" => $periodo,
                            "base_datos_busqueda" => $empresas_individual->AliasBDD,
                            "base_datos_referencia" => $empresas_individual->empresa_historica->AliasBDD
                        ];
                        $busqueda = $this->repository->generaPeticionesBusquedas($data);
                        $busqueda->procesarBusquedaDiferencias();
                        //if($periodo)
                        ProcessBusquedaDiferenciasPolizas::dispatch($busqueda)->onQueue($this->getNombreCola($ejercicio, $periodo));
                        //ProcessBusquedaDiferenciasPolizas::dispatch($busqueda);
                    }
                }
            }
            if(!count($lote->busquedas)>0){
                $lote->finaliza();
            }
        } else if($parametros->tipo_busqueda == 3) {
            $empresas_consolidadoras = $this->repository->getListaEmpresasConsolidadorasConHistorica();
            foreach ($empresas_consolidadoras as $empresa_consolidadora) {
                $ejercicios = $empresa_consolidadora->ejercicios;
                foreach ($ejercicios as $ejercicio) {
                    for ($periodo = 1; $periodo <= 12; $periodo++) {
                        $data = [
                            "id_tipo_busqueda" => 3,
                            "id_lote" => $lote->id,
                            "ejercicio" => $ejercicio,
                            "periodo" => $periodo,
                            "base_datos_busqueda" => $empresa_consolidadora->AliasBDD,
                            "base_datos_referencia" => $empresa_consolidadora->empresa_historica->AliasBDD
                        ];
                        $busqueda = $this->repository->generaPeticionesBusquedas($data);
                        //$busqueda->procesarBusquedaDiferencias();
                        //if($periodo)
                        ProcessBusquedaDiferenciasPolizas::dispatch($busqueda)->onQueue($this->getNombreCola($ejercicio, $periodo));
                        //ProcessBusquedaDiferenciasPolizas::dispatch($busqueda);
                    }
                }
            }
            if(!count($lote->busquedas)>0){
                $lote->finaliza();
            }
        } else if($parametros->tipo_busqueda == 4) {
            $empresas_consolidantes = $this->repository->getListaEmpresasConsolidantesHistoricas();
            foreach ($empresas_consolidantes as $empresa_consolidante) {
                $ejercicios = $empresa_consolidante->ejercicios;
                foreach ($ejercicios as $ejercicio) {
                    for ($periodo = 1; $periodo <= 12; $periodo++) {
                        $data = [
                            "id_tipo_busqueda" => 4,
                            "id_lote" => $lote->id,
                            "ejercicio" => $ejercicio,
                            "periodo" => $periodo,
                            "base_datos_busqueda" => $empresa_consolidante->AliasBDD,
                            "base_datos_referencia" => $empresa_consolidante->empresa_consolidadora->AliasBDD
                        ];
                        $busqueda = $this->repository->generaPeticionesBusquedas($data);
                        //$busqueda->procesarBusquedaDiferencias();
                        //if($periodo)
                        ProcessBusquedaDiferenciasPolizas::dispatch($busqueda)->onQueue($this->getNombreCola($ejercicio, $periodo));
                        //ProcessBusquedaDiferenciasPolizas::dispatch($busqueda);
                    }
                }
            }
            if(!count($lote->busquedas)>0){
                $lote->finaliza();
            }
        }
        return $lote;
    }

    private function getNombreCola($ejercicio, $periodo)
    {
        return "q".$ejercicio;
        /*if($ejercicio<2010)
        {
            return "q0709";
        } else if($ejercicio>=2010 && $ejercicio <=2012)
        {
            return "q1012";
        } else if($ejercicio>=2013 && $ejercicio <=2015)
        {
            return "q1315";
        } else if($ejercicio>=2016 && $ejercicio <=2018)
        {
            return "q1618";
        } else if($ejercicio>=2019)
        {
            return "q1920";
        }*/
    }

    public function obtenerInforme($parametros){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $solicitud = 1;
        $diferencias = 1;

        if($parametros->sin_solicitud_relacionada == 1 && $parametros->con_solicitud_relacionada == 1){
            $solicitud = 2;
        } else if($parametros->sin_solicitud_relacionada == 1 && $parametros->con_solicitud_relacionada === false){
            $solicitud = 1;
        } else if($parametros->sin_solicitud_relacionada === false && $parametros->con_solicitud_relacionada == 1){
            $solicitud = 0;
        } else {
            $solicitud = 2;
        }

        if($parametros->solo_diferencias_activas == 1 && $parametros->no_solo_diferencias_activas == 1){
            $diferencias = 2;
        } else if($parametros->solo_diferencias_activas == 1 && $parametros->no_solo_diferencias_activas === false){
            $diferencias = 1;
        } else if($parametros->solo_diferencias_activas === false && $parametros->no_solo_diferencias_activas == 1){
            $diferencias = 0;
        } else {
            $diferencias = 2;
        }
        return $this->repository->getInforme($parametros->id_empresa, $solicitud, $diferencias, $parametros->tipo_agrupacion);
    }

    public function impresionPolizas($id_relacion){
        $relacion = $this->repository->getRelacion($id_relacion);
        if($relacion){
            $polizas  = $relacion->polizas;
            $pdf = new PolizaFormatoOriginal($polizas);
            return $pdf->create();
        } else {
            dd("Relación no encontrada");
        }


    }

    public function pdfDiferencias($data){
        $solicitud = 2;
        if($data['sin_solicitud_relacionada'] == 'true'  && $data['con_solicitud_relacionada'] == 'false' ) $solicitud = 1;
        if($data['sin_solicitud_relacionada'] == 'false'  && $data['con_solicitud_relacionada'] == 'true' ) $solicitud = 0;

        $diferencias = 2;
        if($data['solo_diferencias_activas'] == 'true'  && $data['no_solo_diferencias_activas'] == 'false' ) $diferencias = 1;
        if($data['solo_diferencias_activas'] == 'false'  && $data['no_solo_diferencias_activas'] == 'true' ) $diferencias = 0;

        $info = $this->repository->getInforme($data['id_empresa'], $solicitud, $diferencias, $data['tipo_agrupacion']);
        $pdf = new InformeDiferenciasPolizas($data, $info);
        return $pdf->create();

    }
}
