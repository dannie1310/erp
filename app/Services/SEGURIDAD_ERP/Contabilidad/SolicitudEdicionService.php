<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use DateTime;
use Chumper\Zipper\Zipper;
use App\Models\CTPQ\Poliza;
use App\Models\IGH\Usuario;
use App\PDF\CTPQ\PolizaFormatoT1;
use App\PDF\CTPQ\PolizaFormatoT2;
use App\PDF\CTPQ\PolizaFormatoT3;
use App\PDF\CTPQ\PolizaFormatoTB1;
use App\PDF\CTPQ\PolizaFormatoTB2;
use App\PDF\CTPQ\PolizaFormatoTB3;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SolicitudEdicionExport;
use App\Imports\SolicitudEdicionImport;
use Illuminate\Support\Facades\Storage;
use App\Repositories\CTPQ\PolizaRepository;
use App\Http\Transformers\CTPQ\PolizaTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\PDF\ContabilidadGeneral\PolizaFormatoOriginalT1;
use App\PDF\ContabilidadGeneral\PolizaFormatoOriginalT2;
use App\PDF\ContabilidadGeneral\PolizaFormatoOriginalT3;
use App\PDF\ContabilidadGeneral\PolizaFormatoOriginalT1B;
use App\PDF\ContabilidadGeneral\PolizaFormatoOriginalT2B;
use App\PDF\ContabilidadGeneral\PolizaFormatoOriginalT3B;
use App\PDF\ContabilidadGeneral\PolizaFormatoPropuestaT1;
use App\PDF\ContabilidadGeneral\PolizaFormatoPropuestaT2;
use App\PDF\ContabilidadGeneral\PolizaFormatoPropuestaT3;
use App\PDF\ContabilidadGeneral\PolizaFormatoPropuestaT1B;
use App\PDF\ContabilidadGeneral\PolizaFormatoPropuestaT2B;
use App\PDF\ContabilidadGeneral\PolizaFormatoPropuestaT3B;
use App\Http\Transformers\CTPQ\PolizaMovimientoTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion as Model;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionPartidaPoliza;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CtgTipoSolicitudEdicion;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionRepository as Repository;

class SolicitudEdicionService
{
    /**
     * @var Repository
     */
    protected $repository;
    protected $arreglo_solicitud;
    protected $log;
    protected $carga;
    protected $resumen;

    public function __construct(Model $model)
    {
        $this->resumen["cantidad_polizas_involucradas"] = 0;
        $this->resumen["cantidad_partidas"] = 0;
        $this->resumen["cantidad_movimientos"] = 0;
        $this->resumen["cantidad_bases"] = 0;
        $this->resumen["bases"] = [];
        $this->bases = [];
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
        if($data['sort'] == 'tipo'){
            $tipos = CtgTipoSolicitudEdicion::query()->orderBy('descripcion',$data['order'])->get();
            foreach ($tipos as $tipo){
                $this->repository->whereOr([['id_tipo', '=', $tipo->id]]);
            }
            request()->request->remove("sort");
            request()->query->remove("sort");
        }

        if($data['sort'] == 'usuario_registro'){
            $usuarios = Usuario::query()->solicitudEdicion(SolicitudEdicion::all())->orderBy('nombre',$data['order'])->get();

            foreach ($usuarios as $usuario){
                $this->repository->whereOr([['id_usuario_registro', '=', $usuario->idusuario]]);
            }
            request()->request->remove("sort");
            request()->query->remove("sort");
        }

        if (isset($data['numero_folio'])) {
            $this->repository->where([['numero_folio', 'LIKE', '%' . $data['numero_folio'] . '%']]);
        }

        if (isset($data['fecha_hora_registro'])) {
            $this->repository->whereBetween( ['fecha_hora_registro', [ request( 'fecha_hora_registro' )." 00:00:00",request( 'fecha_hora_registro' )." 23:59:59"]] );
        }

        if (isset($data['tipo'])) {
            $tipos = CtgTipoSolicitudEdicion::query()->where([['descripcion', 'LIKE', '%'.$data['tipo'].'%']])->get();
            foreach ($tipos as $tipo){
                $this->repository->whereOr([['id_tipo', '=', $tipo->id]]);
            }
        }

        if (isset($data['id_empresa'])) {
            $empresa = Empresa::find($data['id_empresa']);
            $ids_solicitud = [];
            $diferencias = Diferencia::where("base_datos_revisada",$empresa->AliasBDD)->get();
            $partidas = SolicitudEdicionPartidaPoliza::where("bd_contpaq",$empresa->AliasBDD)->get();
            foreach($diferencias as $diferencia){
                if( $diferencia->partida_solicitud){
                    $ids_solicitud[] = $diferencia->partida_solicitud->solicitud->id;
                }
            }
            foreach($partidas as $partida){
                $ids_solicitud[] = $partida->partida_solicitud->solicitud->id;
            }

            $ids_solicitud = array_values(array_unique($ids_solicitud));

            foreach ($ids_solicitud as $id_solicitud){
                $this->repository->whereOr([['id', '=', $id_solicitud]]);
            }
        }

        if (isset($data['id_tipo_solicitud'])) {
            $this->repository->where([['id_tipo', '=', $data['id_tipo_solicitud']]]);
        }

        if (isset($data['id_estado'])) {
            $this->repository->where([['estado', '=', $data['id_estado']]]);
        }

        if (isset($data['estado'])) {
            if (strpos('REGISTRADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 0]]);
            }

            if (strpos('AUTORIZADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 1]]);
            }

            if (strpos('APLICADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 2]]);
            }

            if (strpos('RECHAZADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', -1]]);
            }
        }

        if (isset($data['startDate'])) {
            $this->repository->where([['fecha_hora_registro', '>=', $data['startDate'] ." 00:00:00"]]);
        }

        if (isset($data['endDate'])) {
            $this->repository->where([['fecha_hora_registro', '<=', $data['endDate'] ." 23:59:59"]]);
        }

        return $this->repository->paginate($data);
    }

    public function store(array $data)
    {
        $solicitud = $this->repository->registrar($data);
        return $solicitud;
    }

    private function generaDirectorios($nombre_archivo)
    {
        $nombre = $nombre_archivo . "_" . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/contabilidad/solicitud_edicion/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }

    private function getDatosPartidas($file_xls)
    {
        $rows = Excel::toArray(new SolicitudEdicionImport, $file_xls);
        $partidas_solicitud = [];
        $i = 0;
        foreach ($rows[0] as $key => $row) {
            if ($key > 0) {
                $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]);
                $fecha_obj = DateTime::createFromFormat("Y/m/d", $fecha->format("Y/m/d"));
                $rows[0][$key][0] = $fecha_obj->format("Y/m/d");
                $partidas_solicitud[$i]["fecha"] = $fecha_obj->format("Y/m/d");
                $partidas_solicitud[$i]["fecha_format"] = $fecha_obj->format("d/m/Y");
                $partidas_solicitud[$i]["tipo"] = (int)$row[1];

                $partidas_solicitud[$i]["folio"] = (int)$row[2];
                /*$partidas_solicitud[$i]["importe"] = (float)$row[3];
                $partidas_solicitud[$i]["importe_format"] = "$ " . number_format($row[3], "2", ".", ",");*/
                $partidas_solicitud[$i]["concepto"] = (string)$row[3];
                $partidas_solicitud[$i]["referencia"] = (string)$row[4];
                switch ($row[1]) {
                    case 1:
                        $partidas_solicitud[$i]["tipo_txt"] = "Ingresos";
                        break;
                    case 2:
                        $partidas_solicitud[$i]["tipo_txt"] = "Egresos";
                        break;
                    case 3:
                        $partidas_solicitud[$i]["tipo_txt"] = "Diario";
                        break;
                }
                $i++;
            }
        }
        return $partidas_solicitud;
    }

    private function getFileXLS($nombre_archivo, $archivo_xls)
    {
        $paths = $this->generaDirectorios($nombre_archivo);
        $exp = explode("base64,", $archivo_xls);
        $data = base64_decode($exp[1]);
        $file_xls = public_path($paths["path_xls"]);
        file_put_contents($file_xls, $data);
        return $file_xls;
    }

    private function getDatosPolizas($partidas)
    {
        $poliza_transformer = new PolizaTransformer();
        $movimiento_transformer = new PolizaMovimientoTransformer();
        $repositorio_poliza = new PolizaRepository(new Poliza());
        $lista = $this->repository->getListaBDContpaq();
        $i_partida = 0;
        foreach ($partidas as $partida) {
            $contador_movimientos = 0;
            $contador_bases = 0;
            $polizas = [];
            $i = 0;
            foreach ($lista as $empresa) {
                $polizas_encontradas = [];
                try {
                    DB::purge('cntpq');
                    \Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
                    $polizas_encontradas = $repositorio_poliza->find($partida);
                    if (count($polizas_encontradas) > 0) {
                        $this->bases[] = $empresa->AliasBDD;
                        $contador_bases++;
                    }
                } catch (\Exception $e) {
                    abort(500, "No tiene acceso a la BD: " . $empresa->AliasBDD . " favor de ponerse en contacto con el área de soporte a aplicaciones.".$e->getMessage());
                }

                foreach ($polizas_encontradas as $poliza_encontrada) {
                    $movimientos = [];
                    $im = 0;
                    foreach ($poliza_encontrada->movimientos as $movimiento) {
                        $movimiento_transform = $movimiento_transformer->transform($movimiento);
                        $movimientos[$im] = $movimiento_transform;
                        $movimientos[$im]["id_movimiento"] = $movimiento_transform["id"];
                        $movimientos[$im]["concepto_original"] = $movimiento_transform["concepto"];
                        $movimientos[$im]["referencia_original"] = $movimiento_transform["referencia"];
                        $this->resumen["cantidad_movimientos"]++;
                        $im++;
                    }
                    $poliza_encontrada_transform = $poliza_transformer->transform($poliza_encontrada);
                    $polizas[$i] = $poliza_encontrada_transform;
                    $polizas[$i]["bd_contpaq"] = $empresa->AliasBDD;
                    $polizas[$i]["movimientos"] = $movimientos;
                    $polizas[$i]["concepto_original"] = $poliza_encontrada_transform["concepto"];
                    $polizas[$i]["id_poliza"] = $poliza_encontrada_transform["id"];
                    $polizas[$i]["id_empresa_contpaq"] = $empresa->IdEmpresaContpaq;
                    $contador_movimientos += count($movimientos);
                    $i++;
                    $this->resumen["cantidad_polizas_involucradas"]++;
                }
            }
            $partidas[$i_partida]["cantidad_movimientos"] = $contador_movimientos;
            $partidas[$i_partida]["cantidad_bases"] = $contador_bases;
            $partidas[$i_partida]["polizas"] = $polizas;
            $i_partida++;
        }
        return $partidas;
    }

    public function impresionPolizas($id, $caida){
        $tipo =  $this->repository->show($id)->id_tipo;
        // dd($tipo);
        switch ($tipo) {
            case 1:
                return $this->impresionPolizasTipo1($id, $caida);
                break;
            case 2:
                return $this->impresionPolizasTipo2($id, $caida);
                break;
            case 3:
                return $this->impresionPolizasTipo3($id, $caida);
                break;
        }
    }

    private function impresionPolizasTipo1($id, $caida){
        if($caida == 1){
            $folios  = $this->repository->show($id)->polizas;
            $pdf = new PolizaFormatoT1($folios);
            return $pdf->create();
        }
        if($caida == 2){
            $folios  = $this->repository->show($id)->polizas;
            $pdf = new PolizaFormatoTB1($folios);
            return $pdf->create();
        }

    }

    private function impresionPolizasTipo2($id, $caida)
    {
        if($caida == 1){
            $solicitud = $this->repository->show($id);
            $diferencias  = $solicitud->diferencias;
            $polizas = [];
            foreach($diferencias as $diferencia){
                $polizas[] = $diferencia->poliza;
            }
            $polizas  = array_values(array_unique($polizas));
            $pdf = new PolizaFormatoT2($polizas, $diferencias[0]->empresa);
            return $pdf->create();
        }
        if($caida == 2){
            $solicitud = $this->repository->show($id);
            $diferencias  = $solicitud->diferencias;
            $polizas = [];
            foreach($diferencias as $diferencia){
                $polizas[] = $diferencia->poliza;
            }
            $polizas  = array_values(array_unique($polizas));
            $pdf = new PolizaFormatoTB2($polizas, $diferencias[0]->empresa);
            return $pdf->create();
        }

    }

    private function impresionPolizasTipo3($id, $caida)
    {
        if($caida == 1){
            $solicitud = $this->repository->show($id);
            $diferencias  = $solicitud->diferencias;
            $polizas = [];
            foreach($diferencias as $diferencia){
                $polizas[] = $diferencia->poliza;
            }
            $polizas  = array_values(array_unique($polizas));
            $pdf = new PolizaFormatoT3($polizas, $diferencias[0]->empresa);
            return $pdf->create();
        }
        if($caida == 2){
            $solicitud = $this->repository->show($id);
            $diferencias  = $solicitud->diferencias;
            $polizas = [];
            foreach($diferencias as $diferencia){
                $polizas[] = $diferencia->poliza;
            }
            $polizas  = array_values(array_unique($polizas));
            $pdf = new PolizaFormatoTB3($polizas, $diferencias[0]->empresa);
            return $pdf->create();
        }
    }

    public function impresionPolizasPropuesta($id, $caida){
        $tipo =  $this->repository->show($id)->id_tipo;
        switch ($tipo) {
            case 1:
                return $this->impresionPolizasPropuestaTipo1($id, $caida);
                break;
            case 2:
                return $this->impresionPolizasPropuestaTipo2($id, $caida);
                break;
            case 3:
                return $this->impresionPolizasPropuestaTipo3($id, $caida);
                break;
        }
    }

    private function impresionPolizasPropuestaTipo1($id, $caida)
    {
        $folios  = $this->repository->show($id)->polizas;
        if($caida == 1){
            $pdf = new PolizaFormatoPropuestaT1($folios);
        }
        if($caida == 2){
            $pdf = new PolizaFormatoPropuestaT1B($folios);
        }

        return $pdf->create();
    }

    private function impresionPolizasPropuestaTipo2($id, $caida)
    {
        $solicitud = $this->repository->show($id);
        $diferencias  = $solicitud->diferencias;
        $polizas = [];
        foreach($diferencias as $diferencia){
            $polizas[] = $diferencia->poliza;
        }
        $polizas  = array_values(array_unique($polizas));
        if($caida == 1){
            $pdf = new PolizaFormatoPropuestaT2($polizas, $solicitud, $diferencias[0]->empresa);
        }
        if($caida == 2){
            $pdf = new PolizaFormatoPropuestaT2B($polizas, $solicitud, $diferencias[0]->empresa);
        }

        return $pdf->create();
    }

    private function impresionPolizasPropuestaTipo3($id, $caida)
    {
        $solicitud = $this->repository->show($id);
        $diferencias  = $solicitud->diferencias;
        $polizas = [];
        foreach($diferencias as $diferencia){
            $polizas[] = $diferencia->poliza;
        }
        $polizas  = array_values(array_unique($polizas));
        if($caida == 1){
            $pdf = new PolizaFormatoPropuestaT3($polizas, $solicitud, $diferencias[0]->empresa);
        }
        if($caida == 2){
            $pdf = new PolizaFormatoPropuestaT3B($polizas, $solicitud, $diferencias[0]->empresa);
        }
        return $pdf->create();
    }

    public function impresionPolizasOriginal($id, $caida){
        $tipo =  $this->repository->show($id)->id_tipo;
        switch ($tipo) {
            case 1:
                return $this->impresionPolizasOriginalTipo1($id, $caida);
                break;
            case 2:
                return $this->impresionPolizasOriginalTipo2($id, $caida);
                break;
            case 3:
                return $this->impresionPolizasOriginalTipo3($id, $caida);
                break;
        }
    }

    private function impresionPolizasOriginalTipo1($id, $caida)
    {
        $folios  = $this->repository->show($id)->polizas;
        if($caida == 1){
        $pdf = new PolizaFormatoOriginalT1($folios);
        }
        if($caida == 2){
        $pdf = new PolizaFormatoOriginalT1B($folios);

        }
        return $pdf->create();
    }

    private function impresionPolizasOriginalTipo2($id, $caida)
    {
        $solicitud = $this->repository->show($id);
        $diferencias  = $solicitud->diferencias;
        $polizas = [];
        foreach($diferencias as $diferencia){
            $polizas[] = $diferencia->poliza;
        }
        $polizas  = array_values(array_unique($polizas));
        if($caida == 1){
            $pdf = new PolizaFormatoOriginalT2($polizas, $solicitud, $diferencias[0]->empresa);
        }
        if($caida == 2){
            $pdf = new PolizaFormatoOriginalT2B($polizas, $solicitud, $diferencias[0]->empresa);

        }
        return $pdf->create();
    }

    private function impresionPolizasOriginalTipo3($id, $caida)
    {
        $solicitud = $this->repository->show($id);
        $diferencias  = $solicitud->diferencias;
        $polizas = [];
        foreach($diferencias as $diferencia){
            $polizas[] = $diferencia->poliza;
        }
        $polizas  = array_values(array_unique($polizas));
        if($caida == 1){
            $pdf = new PolizaFormatoOriginalT3($polizas, $solicitud, $diferencias[0]->empresa);
        }
        if($caida == 2){
            $pdf = new PolizaFormatoOriginalT3B($polizas, $solicitud, $diferencias[0]->empresa);
        }
        return $pdf->create();

    }

    public function procesaSolicitudXLS($nombre_archivo, $archivo_xls)
    {
        $file_xls = $this->getFileXLS($nombre_archivo, $archivo_xls);
        $partidas = $this->getDatosPartidas($file_xls);
        $this->resumen["cantidad_partidas"] = count($partidas);


        $partidas_con_polizas = $this->getDatosPolizas($partidas);
        $this->resumen["cantidad_bases"] = count(array_unique($this->bases));
        $this->resumen["bases"] = array_unique($this->bases);
        return ["partidas" => $partidas_con_polizas, "resumen" => $this->resumen];
    }

    public function autorizar($id, $datos)
    {
        if($datos->id_tipo == 1)
        {
            $polizas = [];
            $contador_aprobadas = 0;
            foreach ($datos->partidas as $partida){
                foreach($partida["polizas"]["data"] as $poliza )
                {
                    $polizas[] = ["id"=>$poliza["id"], "estado"=>$poliza["estado"]] ;
                    if($poliza["estado"])
                    {
                        $contador_aprobadas++;
                    }
                }
            }
            if(!$contador_aprobadas > 0)
            {
                abort(500,"No puede autorizar una solicitud sin aprobar el cambio de al menos una póliza");
            }
            return $this->repository->autorizarPorPolizas($polizas, $id);
        } else {
            $partidas = [];
            $contador_aprobadas = 0;
            foreach ($datos->partidas as $partida){
                $partidas[] = ["id"=>$partida["id"], "estado"=>$partida["estado"]] ;
                if($partida["estado"])
                {
                    $contador_aprobadas++;
                }
            }
            if(!$contador_aprobadas > 0)
            {
                abort(500,"No puede autorizar una solicitud sin aprobar el cambio de al menos una partida");
            }
            return $this->repository->autorizarPorPartidas($partidas, $id);
        }

    }

    public function rechazar($id)
    {
        $this->repository->rechazar($id);

    }

    public function aplicar($id)
    {
        return $this->repository->aplicar($id);
    }

    public function descargarXLS($id)
    {
        $folio = $this->show($id)->numero_folio;
        $solicitud_polizas = array(
            array('a','b'),
            array('c','d'),
        );

        $polizas_solicitud = $this->repository->getPolizasSolicitud($id);

        $export = new SolicitudEdicionExport($polizas_solicitud);

        return Excel::download($export, "SolEdPol_#".$folio."_".date('dmY_His').".xlsx");
    }
}
