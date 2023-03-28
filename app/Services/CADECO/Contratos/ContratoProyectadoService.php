<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO\Contratos;


use App\Facades\Context;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contrato;
use App\Repositories\Repository;
use App\Utils\ValidacionSistema;
use App\Imports\CotizacionImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SolicitudEdicionImport;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\PresupuestoContratista;
use App\CSV\Contratos\AsignacionContratistaLayout;
use App\Models\CADECO\Contratos\AreaSubcontratante;
use App\Models\CADECO\PresupuestoContratistaPartida;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use App\PDF\Contratos\PresupuestoContratistaTablaComparativaFormato;

class ContratoProyectadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    protected $verifica;

    /**
     * ContratoProyectadoService constructor.
     * @param ContratoProyectado $model
     */
    public function __construct(ContratoProyectado $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function find($id)
    {
        return $this->repository->where('id_transaccion', '=', $id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data){
        try {
            ini_set('memory_limit', -1) ;
            ini_set('max_execution_time', '7200') ;
            DB::connection('cadeco')->beginTransaction();
            $fecha_cp = strtotime($data['fecha']);
            $fecha_cump = strtotime($data['cumplimineto']);
            $fecha_vencim = strtotime($data['vencimiento']);

            $contrato_proyectado = $this->repository->create([
                'fecha' => date('Y-m-d',$fecha_cp),
                'cumplimiento' => date('Y-m-d',$fecha_cump) ,
                'vencimiento' => date('Y-m-d',$fecha_vencim) ,
                'referencia' => $data['referencia'],
            ]);
            $contrato_proyectado = $this->repository->show($contrato_proyectado->id_transaccion);
            $contrato_proyectado->areaSubcontratante()->create([
                'id_transaccion' => $contrato_proyectado->id_transaccion,
                'id_area_subcontratante' => $data['id_area_subcontratante'],
            ]);

            $nivel_anterior = 0;
            $nivel_contrato_anterior = '';
            foreach($data['contratos'] as $key => $contrato){
                $nivel = '';
                if($nivel_contrato_anterior == ''){
                    $nivel = '000.';
                    $nivel_contrato_anterior = $nivel;
                    $nivel_anterior = $contrato['nivel'];
                }else{
                    if($nivel_anterior + 1 == $contrato['nivel']){
                        $cant = Contrato::where('nivel', 'LIKE', $nivel_contrato_anterior.'___.')->where('id_transaccion', '=', $contrato_proyectado->id_transaccion)->count();
                        $nivel = $nivel_contrato_anterior . str_pad($cant, 3, 0, 0) . '.';
                        $nivel_contrato_anterior = $nivel;
                        $nivel_anterior = $contrato['nivel'];
                    }else{
                        $cant = Contrato::where('nivel', 'LIKE', mb_substr($nivel_contrato_anterior, 0, (($contrato['nivel'] - 1) * 4)) . '___.')->where('id_transaccion', '=', $contrato_proyectado->id_transaccion)->count();
                        $nivel = mb_substr($nivel_contrato_anterior, 0, (($contrato['nivel'] - 1) * 4)) . str_pad($cant, 3, 0, 0) . '.';
                        $nivel_contrato_anterior = $nivel;
                        $nivel_anterior = $contrato['nivel'];
                    }

                }
                $datos = array();
                $datos['id_transaccion'] = $contrato_proyectado->id_transaccion;
                $datos['nivel'] = $nivel;
                $datos['descripcion'] = $contrato['descripcion_sin_formato'];
                $datos['clave'] = $contrato['clave'];

                if($contrato['es_hoja']){
                    $datos['id_destino'] = $contrato['destino'];
                    $datos['unidad'] = $contrato['unidad'];
                    $datos['cantidad_original'] = $contrato['cantidad'];
                    $datos['cantidad_presupuestada'] = $contrato['cantidad'];
                }

                $contrato_proyectado->conceptos()->create($datos);
            }

            DB::connection('cadeco')->commit();

            return $contrato_proyectado;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }

    public function paginate($data)
    {
       if(isset($data['id_area_subcontratante'])){
            $areas = TipoAreaSubcontratante::where([['descripcion', 'LIKE', '%'.request('id_area_subcontratante').'%']])->pluck("id");
            $cp_areas = AreaSubcontratante::whereIn('id_area_subcontratante',  $areas)->pluck("id_transaccion");
            $this->repository->whereIn(['id_transaccion', $cp_areas]);

        }
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio'])){
            $this->repository->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['referencia'])){
            $this->repository->where([['referencia', 'LIKE', '%'.$data['referencia'].'%']]);
        }
        return $this->repository->paginate();
    }

    public function getLayoutData($data){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $file_xls = $this->getFileXLS($data->nombre_archivo, $data->pagos);
        $partidas = $this->getDatosPartidas($file_xls);

        $index_padre = 0;
        $nivel_anterior = 0;
        $contratos = array();
        $partidas_error = false;
        $partidas_errores = [];
        foreach($partidas as $key => $partida){
            if(!$partida['descripcion'] || !$partida['nivel']){continue;}

            $destino = '';
            $destino_path = '';
            $cantidad = 0;
            $tipo_error = [];

            if(is_numeric($partida["destino"])){
                if($partida['destino'] && $concepto = Concepto::where('clave_concepto', '=', "'" . $partida['destino'] . "'")->orWhere("id_concepto","=",$partida['destino'])->first()){
                    if($concepto->es_agrupador){
                        $path = explode('->', $concepto->path);
                        $destino = $concepto->id_concepto;
                        $destino_path =  $concepto->path_corta;
                    }
                }
            }else{
                if($partida['destino'] && $concepto = Concepto::where('clave_concepto', '=', $partida['destino'])->first()){
                    if($concepto->es_agrupador){
                        $path = explode('->', $concepto->path);
                        $destino = $concepto->id_concepto;
                        $destino_path =  $concepto->path_corta;
                    }
                }
            }

            if($partida['cantidad'] && !is_numeric($partida['cantidad'])){
                $cantidad = 'N/V';
                $partidas_errores[1] = 'Cantidad no válida';
                $tipo_error['cantidad'] = true;
            }else{
                $cantidad = $partida['cantidad'];
            }
            if(strlen($partida['descripcion']) > 255){
                $partidas_errores[0] = 'Descripción mayor a 255 caracteres';
                $tipo_error['descripcion'] = true;
            }

            $dsc_format = str_pad($partida['descripcion'], strlen($partida['descripcion']) + ($partida['nivel'] * 2), '_', STR_PAD_LEFT);
            $contratos[$key] = [
                    'clave' => $partida['clave'],
                    'descripcion' => $dsc_format,
                    'descripcion_sin_formato' => $partida['descripcion'],
                    'unidad' => $partida['unidad'],
                    'cantidad' => $cantidad,
                    'destino' => $destino,
                    'destino_path' => $destino_path,
                    'nivel' => (int) $partida['nivel'],
                    'es_hoja' => $partida['cantidad']?true:false,
                    'cantidad_hijos' => 0,
                    'partida_valida' => true,
                    'tipo_error' => [],
                ];
            if($contratos[$key]['es_hoja']){
                if($destino == ''){
                    $contratos[$key]['destino_path'] = 'N/V';
                    $tipo_error['destino'] = true;
                    $partidas_errores[2] = 'Destino no válido';
                }
                $contratos[$key]['partida_valida'] = count($tipo_error) > 0?false:true;
                $contratos[$key]['tipo_error'] = $tipo_error;
            }
            if($key == 0){

                $index_padre = $key;
                $nivel_anterior = $partida['nivel'];
                continue;
            }
            if($nivel_anterior + 1 == $partida['nivel']){
                $contratos[$key - 1]['es_hoja'] = false;
                $contratos[$key - 1]['cantidad'] = '';
                $contratos[$key - 1]['unidad'] = '';
                $contratos[$key - 1]['destino'] = '';
                $contratos[$key - 1]['destino_path'] = '';
                $contratos[$key - 1]['cantidad_hijos'] = $contratos[$key - 1]['cantidad_hijos'] + 1;

                $index_padre = $key - 1;
                $nivel_anterior = $partida['nivel'];
                continue;
            }

            if($nivel_anterior == $partida['nivel']){
                $contratos[$index_padre]['cantidad_hijos'] = $contratos[$index_padre]['cantidad_hijos'] + 1;
                continue;
            }

            if($nivel_anterior < $partida['nivel']){
                $index_base = $key - 1;
                while($contratos[$index_base]['nivel'] >= $partida['nivel']){$index_base--;}
                $contratos[$index_base]['cantidad_hijos'] = $contratos[$index_base]['cantidad_hijos'] + 1;
            }


        }

        if(count($partidas_errores) > 0){
            $partidas_error = true;
        }
        return ['partidas_con_error' => $partidas_error, 'errores_partidas' => implode(', ', $partidas_errores), 'contratos' =>$contratos];
    }

    private function getDatosPartidas($file_xls)
    {
        $rows = Excel::toArray(new SolicitudEdicionImport, $file_xls);
        $partidas = [];
        if(count($rows[0][0]) != 6){
            abort(403, 'El archivo XLS no es compatible.');
        }
        foreach ($rows[0] as $key => $row) {
            $partidas[$key] = [
                'clave' => $row[0],
                'descripcion' => $row[1],
                'nivel' => $row[2],
                'unidad' => $row[3],
                'cantidad' => $row[4],
                'destino' => array_key_exists(5, $row)?$row[5]:null,
            ];
        }
        return $partidas;
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

    private function generaDirectorios($nombre_archivo)
    {
        $nombre = $nombre_archivo . "_" . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/contratos/contrato_proyectado/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }


    public function getContratos(){
        $contratos = DB::connection('cadeco')->select(' SELECT DISTINCT(id_transaccion), id_transaccion_b64, folio, referencia FROM (

            SELECT
                cp.id_transaccion as id_transaccion_b64
                , cp.numero_folio as folio
                , cp.referencia
                , cp.id_transaccion
				, con.id_concepto
				, con.cantidad_original
				, sum(pa.cantidad_asignada) as total_asignado
				, con.cantidad_original - sum(pa.cantidad_asignada) as restante,
				 (
				 SELECT SUM(pa.cantidad_asignada) as asignada
				   FROM Subcontratos.partidas_asignacion pa
				     WHERE id_transaccion in(
                     SELECT id_transaccion
					 FROM dbo.transacciones
					   WHERE tipo_transaccion=50 and id_antecedente =cp.id_transaccion
                     )
				 )as asignada,
				(
				      SELECT SUM (cantidad_original) from contratos where id_transaccion=cp.id_transaccion
				)as proyectada


            FROM
                transacciones as cp

				JOIN dbo.transacciones as pc on(cp.id_transaccion = pc.id_antecedente)

				JOIN dbo.contratos as con ON(con.id_transaccion = cp.id_transaccion)

				JOIN dbo.presupuestos AS pre ON(pre.id_concepto = con.id_concepto AND pre.id_transaccion = pc.id_transaccion AND pre.no_cotizado != 1)
                                LEFT JOIN Subcontratos.partidas_asignacion AS pa ON(pa.id_transaccion = pc.id_transaccion AND pa.id_concepto = con.id_concepto)

              INNER JOIN Contratos.cp_areas_subcontratantes m ON
                         cp.id_transaccion=m.id_transaccion  AND m.id_area_subcontratante IN (1,2)
            WHERE
                cp.tipo_transaccion = 49
                AND cp.id_obra = '.Context::getIdObra().'

            GROUP BY
                cp.numero_folio
                , cp.referencia
                , cp.id_transaccion
				, con.id_concepto
				, con.cantidad_original

             HAVING con.cantidad_original - sum(pa.cantidad_asignada) > 0 OR sum(pa.cantidad_asignada) is null
             ) AS TA  where (TA.proyectada-Ta.asignada )>0 or TA.asignada is null  ORDER BY folio desc ');


             $data = [];
            foreach($contratos as $contrato){
                $data[] = [
                    'id_transaccion' => $contrato->id_transaccion,
                    'folio' => '#' . str_pad($contrato->folio, 5,0,0),
                    'referencia' => $contrato->referencia,
                ];
            }

        return $data;
    }

    public function getCotizaciones($id){
        try{
            $items = array();
            $presupuestos = array();
            $precios = [];
            $contrato_p = $this->repository->show($id);
            $contratos = $contrato_p->conceptos;
            $presupuesto_contratistas = $contrato_p->presupuestos;

            foreach($contratos as $i => $contrato){
                $cantidad_pendiente = 0;
                if(($contrato->cantidad_original - $contrato->asignados->sum('cantidad_asignada')) > 0){
                    $items[$i] = [
                        'id_concepto' => $contrato->id_concepto,
                        'descripcion' => $contrato->descripcion,
                        'descripcion_corta' => mb_substr($contrato->descripcion, 0, 20),
                        'destino' => $contrato->destino->concepto->getAncestrosAttribute($contrato->destino->concepto->nivel),
                        'destino_corto' => mb_substr($contrato->destino->concepto->getAncestrosAttribute($contrato->destino->concepto->nivel), 0, 20),
                        'unidad' => $contrato->unidad,
                        'cantidad_solicitada' => number_format($contrato->cantidad_original, 4, '.', ''),
                        'cantidad_aprobada' => number_format($contrato->cantidad_original, 4, '.', ''),
                        'cantidad_disponible' => number_format($contrato->cantidad_original - $contrato->asignados->sum('cantidad_asignada'), 4, '.', ''),
                        'cantidad_base' => number_format($contrato->cantidad_original - $contrato->asignados->sum('cantidad_asignada'), 4, '.', ''),
                        'item_pendiente' => $contrato->cantidad_original - $contrato->asignados->sum('cantidad_asignada') > 0?true:false,
                    ];
                    $cantidad_pendiente = $contrato->cantidad_original - $contrato->asignados->sum('cantidad_asignada');
                    foreach($presupuesto_contratistas as $presupuesto){
                        if(!array_key_exists($presupuesto->id_transaccion, $presupuestos)){
                            $presupuestos[$presupuesto->id_transaccion] = [
                                'id_transaccion' => $presupuesto->id_transaccion,
                                'rfc' => $presupuesto->empresa->rfc,
                                'razon_social' => $presupuesto->empresa->razon_social,
                                'sucursal' => $presupuesto->sucursal?$presupuesto->sucursal->descripcion:'',
                                'direccion' => $presupuesto->sucursal?$presupuesto->sucursal->direccion:'',
                                'numero_folio_format' => $presupuesto->numero_folio_format,
                            ];
                            $presupuestos[$presupuesto->id_transaccion]['partidas'] = array();
                        }
                        array_key_exists($presupuesto->id_transaccion, $presupuestos)?'': $presupuestos[$presupuesto->id_transaccion] = array();
                        $partida_presupuestada = PresupuestoContratistaPartida::where('id_transaccion', '=',$presupuesto->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();

                        if($partida_presupuestada && $partida_presupuestada->precio_unitario_despues_descuento > 0){
                            if (key_exists($partida_presupuestada->id_concepto, $precios)) {
                                if ($partida_presupuestada->precio_unitario_despues_descuento > 0 && $precios[$partida_presupuestada->id_concepto] > $partida_presupuestada->precio_unitario_despues_descuento)
                                    $precios[$partida_presupuestada->id_concepto] = (float)$partida_presupuestada->precio_unitario_despues_descuento;
                            } else {
                                if ($partida_presupuestada->precio_unitario_despues_descuento > 0) {
                                    $precios[$partida_presupuestada->id_concepto] = (float)$partida_presupuestada->precio_unitario_despues_descuento;
                                }
                            }
                            $presupuestos[$presupuesto->id_transaccion]['partidas'][$i] = [
                                'id_concepto' => $contrato->id_concepto,
                                'precio_unitario' => $partida_presupuestada->precio_unitario_antes_descuento_format,
                                'precio_total_antes_desc' => $partida_presupuestada->total_antes_descuento_format,
                                'precio_unitario_con_desc' =>  $partida_presupuestada->precio_unitario_despues_descuento_format,
                                'precio_unitario_con_desc_sf' =>  $partida_presupuestada->precio_unitario_despues_descuento,
                                'precio_total_con_desc' =>   $partida_presupuestada->total_despues_descuento_format,
                                'descuento' => $partida_presupuestada->porcentaje_descuento_format,
                                'moneda' => $partida_presupuestada->moneda->abreviatura,
                                'tipo_cambio' => number_format($partida_presupuestada->tipo_cambio, 4, '.', ','),
                                'importe_moneda_conversion' => $partida_presupuestada->total_despues_descuento_partida_mc_format,
                                'observaciones' => $partida_presupuestada->Observaciones,
                                'mejor_opcion' => $partida_presupuestada->mejor_opcion,
                                'justificacion' => '',
                                'cantidad_asignada' => '',
                            ];
                        }else{
                            $presupuestos[$presupuesto->id_transaccion]['partidas'][$i] = null;
                        }

                    }


                }
            }
            return ['items'=>$items,'presupuestos'=> $presupuestos, 'cantidad_presupuestos'=>count($presupuestos), 'precios_menores' => $precios];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public  function actualiza($data, $id)
    {
        $area =  $data['id_area'];

        $transaccion = $this->repository->show($id);
        $transaccion_area = $transaccion->areasSubcontratantes;
        if(count($transaccion_area) > 0){
            $solicitud = AreaSubcontratante::find($id);
            $solicitud = $solicitud->actualiza($id, $data['id_area']);
            $transaccion->refresh();
            return $transaccion;

        }else{
            try {
                DB::connection('cadeco')->beginTransaction();
                $datos = [
                    'id_area_subcontratante' => $area,
                    'id_transaccion' => $id,
                ];
                $solicitud = AreaSubcontratante::create($datos);

                DB::connection('cadeco')->commit();
                $transaccion->refresh();
                return $transaccion ;
            } catch (\Exception $e) {
                DB::connection('cadeco')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        }
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }

    public function pdf($id)
    {
        return $this->repository->show($id)->pdf();
    }

    public function getCuerpoCorreo($id)
    {
        return $this->repository->show($id)->getCuerpoCorreoInvitacion();
    }

    public function getComparativaCotizaciones($data,$id)
    {
        return $this->repository->show($id)->datosComparativos($data);
    }

    public function pdfComparativaCotizaciones($id)
    {
        $presupuestos = $this->repository->show($id)->presupuestos()->first();
        $pdf = new PresupuestoContratistaTablaComparativaFormato($presupuestos);
        return $pdf;
    }

    public function getLayoutAsignacion($id){
        $cotizaciones = $this->getCotizaciones($id);
        $file_name = $this->show($id)->numero_folio_format.'.xlsx';
        return Excel::download(new AsignacionContratistaLayout($cotizaciones, $id),$file_name);
    }

    public function procesaLayoutAsigancion($data){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $items = array();
        $presupuestos = array();
        $precios = [];
        $partidas_no_validas = false;
        $file_xls = $this->getFileXls($data['name'], $data['file']);
        $celdas = $this->getDatosAsignacionLayout($file_xls);
        $this->verifica = new ValidacionSistema();

        $cadena_validacion = $this->verifica->desencripta($celdas[0][0]);
        $cadena_validacion_exp = explode("|", $cadena_validacion);

        $base_datos = $cadena_validacion_exp[0];
        $id_obra = $cadena_validacion_exp[1];
        $id_validar = $cadena_validacion_exp[2];

        if ($base_datos != Context::getDatabase() || $id_obra != Context::getIdObra() || $data['id'] != $id_validar)
        {
            abort(400, 'El archivo  XLS no corresponde al contrato proyectado');
        }

        $cant_pres =(count($celdas[0]) - 6) / 9;
        $cant_asignaciones = [];

        for($i = 3; $i < count($celdas);$i++){
            $id_contrato = $this->verifica->desencripta($celdas[$i][0]);
            $contrato = Contrato::where('id_concepto', '=', $id_contrato)->first();
            $cantidad_pendiente = 0;
            $items[$i] = [
                'id_concepto' => $contrato->id_concepto,
                'descripcion' => $contrato->descripcion,
                'descripcion_corta' => mb_substr($contrato->descripcion, 0, 20),
                'destino' => $contrato->destino->concepto->getAncestrosAttribute($contrato->destino->concepto->nivel),
                'destino_corto' => mb_substr($contrato->destino->concepto->getAncestrosAttribute($contrato->destino->concepto->nivel), 0, 20),
                'unidad' => $contrato->unidad,
                'cantidad_solicitada' => number_format($contrato->cantidad_original, 4, '.', ''),
                'cantidad_aprobada' => number_format($contrato->cantidad_original, 4, '.', ''),
                'cantidad_disponible' => number_format($contrato->cantidad_original - $contrato->asignados->sum('cantidad_asignada'), 4, '.', ''),
                'cantidad_base' => number_format($contrato->cantidad_original - $contrato->asignados->sum('cantidad_asignada'), 4, '.', ''),
                'item_pendiente' => $contrato->cantidad_original - $contrato->asignados->sum('cantidad_asignada') > 0?true:false,
                'cotizado' => false,
                'asignadas_mayor_disponible' => false,
            ];

            $indx_id_contrato = 6;
            $cant_asignada = 0;
            for($j = 0; $j < $cant_pres; $j++){
                $id_presupuesto = $this->verifica->desencripta($celdas[1][$indx_id_contrato]);
                $presupuesto = PresupuestoContratista::where('id_transaccion', '=',$id_presupuesto)->first();
                if(!array_key_exists($presupuesto->id_transaccion, $presupuestos)){
                    $presupuestos[$presupuesto->id_transaccion] = [
                        'id_transaccion' => $presupuesto->id_transaccion,
                        'rfc' => $presupuesto->empresa->rfc,
                        'razon_social' => $presupuesto->empresa->razon_social,
                        'sucursal' => $presupuesto->sucursal?$presupuesto->sucursal->descripcion:'',
                        'direccion' => $presupuesto->sucursal?$presupuesto->sucursal->direccion:'',
                        'numero_folio_format' => $presupuesto->numero_folio_format,
                        'justificar' => false,
                        'partidas_no_validas' => false,
                        'partidas_asignadas' => false,
                    ];
                    $presupuestos[$presupuesto->id_transaccion]['partidas'] = array();
                }
                array_key_exists($presupuesto->id_transaccion, $presupuestos)?'': $presupuestos[$presupuesto->id_transaccion] = array();
                $partida_presupuestada = PresupuestoContratistaPartida::where('id_transaccion', '=',$presupuesto->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();
                if (key_exists($partida_presupuestada->id_concepto, $precios)) {
                    if ($partida_presupuestada->precio_unitario_despues_descuento > 0 && $precios[$partida_presupuestada->id_concepto] > $partida_presupuestada->precio_unitario_despues_descuento)
                        $precios[$partida_presupuestada->id_concepto] = (float)$partida_presupuestada->precio_unitario_despues_descuento;
                } else {
                    if ($partida_presupuestada->precio_unitario_despues_descuento > 0) {
                        $precios[$partida_presupuestada->id_concepto] = (float)$partida_presupuestada->precio_unitario_despues_descuento;
                    }
                }
                if($celdas[$i][$indx_id_contrato] != null && $celdas[$i][$indx_id_contrato+8] != null ){
                    $c_pres = 0;
                    $c_valida = true;
                    if(is_numeric($celdas[$i][$indx_id_contrato+8]) && $celdas[$i][$indx_id_contrato+8] > 0){
                        $c_pres = (float)$celdas[$i][$indx_id_contrato+8];
                        $cant_asignada += (float)$celdas[$i][$indx_id_contrato+8];
                    }else{
                        $c_pres = 'N/V';
                        $c_valida = false;
                        $partidas_no_validas = true;
                        $presupuestos[$presupuesto->id_transaccion]['partidas_no_validas'] = true;
                    }
                    $presupuestos[$presupuesto->id_transaccion]['partidas'][$i] = [
                        'id_concepto' => $contrato->id_concepto,
                        'precio_unitario' => $partida_presupuestada->precio_unitario_antes_descuento_format,
                        'precio_total_antes_desc' => $partida_presupuestada->total_antes_descuento_format,
                        'precio_unitario_con_desc' =>  $partida_presupuestada->precio_unitario_despues_descuento_format,
                        'precio_unitario_con_desc_sf' =>  $partida_presupuestada->precio_unitario_despues_descuento,
                        'precio_total_con_desc' =>   $partida_presupuestada->total_despues_descuento_format,
                        'descuento' => $partida_presupuestada->porcentaje_descuento_format,
                        'moneda' => $partida_presupuestada->moneda->abreviatura,
                        'tipo_cambio' => number_format($partida_presupuestada->tipo_cambio, 4, '.', ','),
                        'importe_moneda_conversion' => $partida_presupuestada->total_despues_descuento_partida_mc_format,
                        'observaciones' => $partida_presupuestada->Observaciones,
                        'mejor_opcion' => $partida_presupuestada->mejor_opcion,
                        'justificacion' => '',
                        'cantidad_asignada' => $c_pres,
                        'cantidad_valida' => $c_valida,
                    ];
                    $presupuestos[$presupuesto->id_transaccion]['partidas_asignadas'] = true;
                    $cant_asignaciones[$presupuesto->id_transaccion] = 1;
                }else{
                    $presupuestos[$presupuesto->id_transaccion]['partidas'][$i] = null;
                }
                $indx_id_contrato +=9;
            }
            if((float)$cant_asignada > (float)$items[$i]['cantidad_disponible']){
                $items[$i]['asignadas_mayor_disponible'] = true;
                $partidas_no_validas = true;
                foreach($presupuestos as $key => $presupuesto){
                    if($presupuesto['partidas'][$i]){
                        $presupuestos[$key]['partidas_no_validas'] = true;
                    }
                }
            }

        }
        return ['items'=>$items,'presupuestos'=> $presupuestos, 'cantidad_presupuestos'=>count($cant_asignaciones), 'precios_menores' => $precios, 'partidas_no_validas' => $partidas_no_validas, 'origen' => 1];
    }

    private function getDatosAsignacionLayout($file_xls)
    {
        $rows = Excel::toArray(new CotizacionImport, $file_xls);
        unlink($file_xls);
        return $rows[0];
    }

    public function reclasificacion(array $data, $id)
    {
        return $this->repository->show($id)->reclasificacion($data);
    }
}
