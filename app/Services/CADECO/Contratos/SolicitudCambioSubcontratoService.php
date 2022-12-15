<?php


namespace App\Services\CADECO\Contratos;

use App\Facades\Context;
use App\Imports\SolicitudEdicionImport;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\Documentacion\Archivo;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SolicitudCambioSubcontrato as Model;
use App\Models\CADECO\Unidad;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\PDF\Contratos\SolicitudCambioSubcontratoFormato;
use App\Repositories\CADECO\SubcontratosCM\SolicitudCambioSubcontratoRepository as Repository;
use App\Services\CADECO\Documentacion\ArchivoService;
use App\Utils\ValidacionSistema;
use DateTime;
use DateTimeZone;
use Maatwebsite\Excel\Facades\Excel;

class SolicitudCambioSubcontratoService
{
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio'])){
            $this->repository->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['total'])){
            $this->repository->where([['monto', '=', $data['total']]]);
        }

        if(isset($data['numero_folio_subcontrato'])){
            $subcontratos = Subcontrato::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio_subcontrato'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente', $subcontratos]);

        }

        if (isset($data['estado'])) {
            if (strpos('REGISTRADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 0]]);
            }
            else if (strpos('APLICADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 1]]);
            }
        }

        if(isset($data['referencia_subcontrato'])){
            $subcontratos = Subcontrato::query()->where([['referencia', 'LIKE', '%'.$data['referencia_subcontrato'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente', $subcontratos]);
        }

        if(isset($data['contratista'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['contratista'].'%']])->get();
            foreach ($empresa as $e){
                $this->repository->whereOr([['id_empresa', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['observaciones'])){
            $this->repository->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }

        return $this->repository->paginate($data);
    }

    public function registrar($request)
    {
        $fecha = New DateTime($request->fecha);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $solicitud = [
            "fecha"=>$fecha->format("Y-m-d"),
            "observaciones"=>$request->observaciones,
            "id_antecedente"=>$request->id_subcontrato,
            "monto"=>$request->monto,
            "impuesto"=>$request->impuesto
        ];

        $archivo_data = [
            "archivos"=>\json_encode([["archivo"=>$request->archivo]]),
            "archivos_nombres"=>\json_encode([["nombre"=>$request->archivo_nombre]]),
            "descripcion"=>"Soporte de solicitud de cambio a subcontrato",
            "id_tipo_archivo"=>2
        ];

        $partidas = [];

        $i = 0;
        foreach($request->conceptos as $concepto){
            $partidas[$i] = [
                "id_item_subcontrato"=>$concepto["id_item_subcontrato"],
                "cantidad"=>$concepto["cantidad"],
                "importe"=>$concepto["importe"],
                "precio"=>$concepto["precio"],
            ];
            if($concepto["cantidad"]>0){
                $partidas[$i]["id_tipo_modificacion"] = 1;
            } else {
                $partidas[$i]["id_tipo_modificacion"] = 2;
            }
            $i++;
        }

        foreach($request->conceptos_cambios_precio as $concepto_cambio_precio){
            $partidas[$i] = [
                "id_item_subcontrato"=>$concepto_cambio_precio["id_item_subcontrato"],
                "cantidad"=>$concepto_cambio_precio["cantidad"],
                "importe"=>$concepto_cambio_precio["importe"],
                "precio"=>$concepto_cambio_precio["precio"],
                "id_tipo_modificacion"=>3,
            ];
            $i++;
        }

        foreach($request->conceptos_extraordinarios as $concepto_extraordinario){
            $partidas[$i] = [
                "cantidad"=>$concepto_extraordinario["cantidad"],
                "importe"=>$concepto_extraordinario["importe"],
                "precio"=>$concepto_extraordinario["precio"],
                "id_tipo_modificacion"=>4,
                "clave"=>$concepto_extraordinario["clave"],
                "descripcion"=>$concepto_extraordinario["descripcion"],
                "unidad"=>$concepto_extraordinario["unidad"],
                "id_concepto"=>$concepto_extraordinario["destino"],
                "nivel"=>$concepto_extraordinario["nivel"],
                "id_nodo_carga"=>$concepto_extraordinario["id_nodo_carga"],
            ];
            $i++;
        }


        if($request->archivo == null){
            abort(500, "Debe seleccionar un archivo que soporte la solicitud de cambio");
        }

        $solicitud = $this->repository->registrar($solicitud, $archivo_data, $partidas);
        $archivo_data["id"] = $solicitud->id_transaccion;
        $ArchivoService = new ArchivoService(new Archivo());
        $ArchivoService->cargarArchivo($archivo_data);

        return $solicitud;
    }

    public function pdf($id)
    {
        $pdf = new SolicitudCambioSubcontratoFormato($id);
        return $pdf;
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
        $dir_xls = "uploads/contratos/solicitud_cambio_subcotrato/extraordinarios/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }

    private function getDatosExtraordinarios($file_xls)
    {
        $rows = Excel::toArray(new SolicitudEdicionImport, $file_xls);
        $partidas = [];
        foreach ($rows[0] as $key => $row) {
            if((!is_numeric($row[2]) || !is_numeric($row[4]) || !is_numeric($row[5])) && $row[3] != null){
                abort(500, "Las columnas para especificar el nivel (C), precio (E) y cantidad (F) de los conceptos extraordinarios deben tener un valor numérico, favor de verificar.");
            }
            $partidas[$key] = [
                'clave' => $row[0],
                'descripcion' => $row[1],
                'nivel' => $row[2],
                'unidad' => $row[3],
                'precio' => $row[4],
                'cantidad' => $row[5],
                'destino' => array_key_exists(6, $row)?$row[6]:null,
            ];
        }
        return $partidas;
    }

    private function getDatosCambioPrecioVolumen($file_xls)
    {
        $rows = Excel::toArray(new SolicitudEdicionImport, $file_xls);
        $partidas = [];
        foreach ($rows[0] as $key => $row) {
            if($key == 1){
                $partidas[$key-1] = [
                    'id_subcontrato' => $row[0],
                ];
            }
            if($key>1){
                if(key_exists(9,$row) && key_exists(10,$row) && key_exists(1,$row)){
                    $partidas[$key-1] = [
                        'id_item' => $row[1],
                        'aditiva_deductiva' => $row[9],
                        'nuevo_precio' => $row[10],
                    ];
                } else {
                    abort(500,"El layout tiene un formato incorrecto, favor de verificar");
                }
            }
        }
        return $partidas;
    }

    public function procesarLayoutExtraordinarios($data){
        $file_xls = $this->getFileXLS($data->nombre_archivo, $data->extraordinarios);
        $partidas = $this->getDatosExtraordinarios($file_xls);

        $index_padre = 0;
        $nivel_anterior = 0;
        $contratos = array();

        foreach($partidas as $key => $partida){
            $destino ='';
            $destino_path = '';
            $destino_path_corta = '';
            $destino_error = '';
            $unidad = '';
            $unidad_error = '';
            $clave = '';
            $clave_error = '';
            $descripcion = '';
            $descripcion_error = '';
            if(is_numeric($partida["destino"])){
                if($partida['destino'] && $concepto = Concepto::where('clave_concepto', '=', "'".$partida['destino']."'")->orWhere("id_concepto","=","'".$partida['destino']."'")->first()){
                    if($concepto->es_agrupador){
                        $destino = $concepto->id_concepto;
                        $destino_path = $concepto->path;
                        $destino_path_corta = $concepto->path_corta;
                    }else{
                        $destino_error = $partida["destino"].': no es un concepto agrupador';
                    }
                } else if($partida["destino"]) {
                    $destino_error = $partida["destino"].": concepto no encontrato en presupuesto de obra";
                }
            } else {
                if($partida['destino'] && $concepto = Concepto::where('clave_concepto', '=', $partida['destino'])->first()){
                    if($concepto->es_agrupador){
                        $destino = $concepto->id_concepto;
                        $destino_path = $concepto->path;
                        $destino_path_corta = $concepto->path_corta;
                    }else{
                        $destino_error = $partida["destino"].': no es un concepto agrupador';
                    }
                } else if($partida["destino"]) {
                    $destino_error = $partida["destino"].": concepto no encontrato en presupuesto de obra";
                }
            }

            if($partida['unidad'] && $unidadCat = Unidad::where('unidad', '=', $partida['unidad'])->first()){
                if($unidadCat){
                    $unidad = $unidadCat->unidad;
                }
            } else if($partida["unidad"]) {
                $unidad_error = $partida["unidad"];
            }
            $clave_preexistente = Contrato::where("id_transaccion","=", $data->id_contrato_proyectado)
                ->where("clave","=",$partida["clave"])->first();
            if($clave_preexistente){
                $clave_error = $partida["clave"];
            } else {
                $clave = $partida["clave"];
            }
            if(strlen($partida["descripcion"])>255){
                $descripcion_error = "LA LONGITUD DEBE SER MENOR O IGUAL A 255 CARACTERES".$partida["descripcion"];
            } else{
                $descripcion = $partida["descripcion"];
            }
            $contratos[$key] = [
                'clave' => $clave,
                'descripcion' => $descripcion,
                'unidad' => $unidad,
                'cantidad' => $partida['cantidad'],
                'destino' => $destino,
                'destino_path' => $destino_path,
                'destino_path_corta' => $destino_path_corta,
                'precio' => $partida['precio'],
                'importe' => $partida['precio']*$partida["cantidad"],
                'nivel' => (int) $partida['nivel'],
                'es_hoja' => $partida['cantidad']?true:false,
                'cantidad_hijos' => 0,
                'destino_error' => $destino_error,
                'descripcion_error' => $descripcion_error,
                'unidad_error' => $unidad_error,
                'clave_error' => $clave_error,
                'id_nodo_carga' => $data->id_contrato_nodo_carga,
            ];
            if($key == 0){
                $index_padre = $key;
                $nivel_anterior = $partida['nivel'];
                continue;
            }

            /*if($nivel_anterior == $partida['nivel']){
                $contratos[$index_padre]['cantidad_hijos'] = $contratos[$index_padre]['cantidad_hijos'] + 1;
                continue;
            }*/

            if($nivel_anterior < $partida['nivel']){
                $index_base = $key - 1;
                while($contratos[$index_base]['nivel'] >= $partida['nivel']){$index_base--;}
                $contratos[$index_base]['cantidad_hijos'] = $contratos[$index_base]['cantidad_hijos'] + 1;
            }
        }
        return $contratos;
    }

    public function procesarLayoutCambioPrecioVolumen($data){
        $validacionSistema = new ValidacionSistema();
        $file_xls = $this->getFileXLS($data->nombre_archivo, $data->cambios_precio_volumen);
        $partidas = $this->getDatosCambioPrecioVolumen($file_xls);

        $cadena_validacion = $validacionSistema->desencripta($partidas[0]["id_subcontrato"]);
        $cadena_validacion_exp = explode("|", $cadena_validacion);

        $base_datos = $cadena_validacion_exp[0];
        $id_obra = $cadena_validacion_exp[1];
        $id_subcontrato = $cadena_validacion_exp[2];

        if(Context::getDatabase() !=$base_datos){
            $id_proyecto = Proyecto::where("base_datos","=", $base_datos)->pluck("id")->first();
            $obra = ConfiguracionObra::where("id_proyecto", "=",$id_proyecto )->where("id_obra","=", $id_obra)->withoutGlobalScopes()->first();
            abort(500,"El layout que cargó corresponde la obra ". $obra->nombre ." favor de verificar");
        }

        $subcontratoLayout = Subcontrato::find($id_subcontrato);
        $subcontratoFormulario = Subcontrato::find($data->id_subcontrato);
        if($id_subcontrato != $data->id_subcontrato){
            abort(500,"El layout que cargó corresponde al subcontrato ".$subcontratoLayout->numero_folio_format ." favor de verificar");
        }

        $contratos = array();

        $i = 0;
        foreach($partidas as $key => $partida){
            if($key>0){
                $id_item_desencriptado = $validacionSistema->desencripta($partida["id_item"]);
                $itemSubcontrato = ItemSubcontrato::find($id_item_desencriptado);
                if($itemSubcontrato->id_transaccion == $id_subcontrato)
                {
                    $contratos[$i] = [
                        'id_item' => $id_item_desencriptado,
                        'aditiva_deductiva' => $partida["aditiva_deductiva"],
                        'nuevo_precio' => $partida["nuevo_precio"],
                    ];
                    $i++;
                }
            }
        }
        return $contratos;
    }
}
