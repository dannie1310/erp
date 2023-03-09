<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\CSV\Finanzas\CFDILayout;
use App\CSV\Fiscal\CFDIREPPendiente;
use App\Events\IncidenciaCI;
use App\Jobs\ProcessCancelacionCFDI;
use App\Jobs\ProcessComplementaConceptosTxtCFDI;
use App\Jobs\ProcessComplementaDatosCFDI;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\PDF\Fiscal\Comunicado;
use App\PDF\Fiscal\InformeREPProveedor;
use App\PDF\Fiscal\InformeREPEmpresa;
use App\PDF\Fiscal\InformeREPEmpresaProveedor;
use App\PDF\Fiscal\InformeREPProveedorEmpresa;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDSATRepository;
use DateTime;
use DateTimeZone;
use App\Utils\CFD;
use App\Utils\Util;
use App\Utils\Files;
use App\PDF\Fiscal\CFDI;
use Maatwebsite\Excel\Facades\Excel;
use Webpatser\Uuid\Uuid;
use App\Events\CambioEFOS;
use Chumper\Zipper\Zipper;
use App\Events\FinalizaCargaCFD;
use App\Events\CambioNoLocalizados;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\PDF\Fiscal\InformeCFDICompleto;
use Illuminate\Support\Facades\Storage;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT as Model;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDSATRepository as Repository;

class CFDSATService
{
    /**
     * @var Repository
     */
    protected $repository;
    protected $arreglo_factura;
    protected $arreglos_factura;
    protected $log;
    protected $carga;

    public function __construct(Model $model)
    {
        $this->arreglos_factura = [];
        $this->repository = new Repository($model);
        $this->log["nombre_archivo_zip"] = "";
        $this->log["archivos_leidos"] = 0;
        $this->log["archivos_cargados"] = 0;
        $this->log["cfd_cargados"] = 0;
        $this->log["archivos_corruptos"] = 0;
        $this->log["archivos_tipo_incorrecto"] = 0;
        $this->log["archivos_no_cargados"] = 0;
        $this->log["cfd_no_cargados"] = 0;
        $this->log["archivos_preexistentes"] = 0;
        $this->log["cfd_preexistentes"] = 0;
        $this->log["archivos_receptor_no_valido"] = 0;
        $this->log["cfd_receptor_no_valido"] = 0;
        $this->log["receptores_no_validos"] = [];
        $this->log["proveedores_preexistentes"] = 0;
        $this->log["proveedores_nuevos"] = 0;
        $this->log["archivos_no_cargados_error_app"] = 0;
        $this->log["cfd_no_cargados_error_app"] = 0;
        $this->log["errores"] = [];
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

        if (isset($data['startDate'])) {
            $this->repository->where([['cfd_sat.fecha', '>=', $data['startDate']]]);
        }
        if (isset($data['endDate'])) {
            $this->repository->where([['cfd_sat.fecha', '<=', $data['endDate']]]);
        }
        if (isset($data['rfc_emisor'])) {
            $this->repository->where([['rfc_emisor', 'LIKE', '%' . $data['rfc_emisor'] . '%']]);
        }
        if (isset($data['emisor'])) {
            $proveedoresSAT = ProveedorSAT::query()->where([['razon_social', 'LIKE', '%' . $data['emisor'] . '%']])->get();
            $arreglo_proveedor = [];
            foreach ($proveedoresSAT as $e) {
                $arreglo_proveedor[] = $e->id;
            }
            $this->repository->whereIn(['id_proveedor_sat', $arreglo_proveedor]);
        }
        if (isset($data['rfc_receptor'])) {
            $this->repository->where([['rfc_receptor', 'LIKE', '%' . $data['rfc_receptor'] . '%']]);
        }
        if (isset($data['receptor'])) {
            $empresasSAT = EmpresaSAT::query()->where([['razon_social', 'LIKE', '%' . $data['receptor'] . '%']])->get();
            $arreglo_empresa = [];
            foreach ($empresasSAT as $es) {
                $arreglo_empresa[] = $es->id;
            }
            $this->repository->whereIn(['id_empresa_sat', $arreglo_empresa]);
        }
        if (isset($data['uuid'])) {
            $this->repository->where([['cfd_sat.uuid', 'LIKE', '%' . $data['uuid'] . '%']]);
        }
        if (isset($data['moneda'])) {
            $this->repository->where([['moneda', 'LIKE', '%' . $data['moneda'] . '%']]);
        }
        if (isset($data['total'])) {
            //$this->repository->where([['total', '=', $data['total'] ]]);

            if (strpos($data['total'], ">=") !== false) {
                $total_cfdi = str_replace(">=", "", $data['total']);
                $this->repository->where([['total', ">=", $total_cfdi]]);
            } else if (strpos($data['total'], ">") !== false) {
                $total_cfdi = str_replace(">", "", $data['total']);
                $this->repository->where([['total', ">", $total_cfdi]]);
            } else if (strpos($data['total'], "<=") !== false) {
                $total_cfdi = str_replace("<=", "", $data['total']);
                $this->repository->where([['total', "<=", $total_cfdi]]);
            } else if (strpos($data['total'], "<") !== false) {
                $total_cfdi = str_replace("<", "", $data['total']);
                $this->repository->where([['total', "<", $total_cfdi]]);
            } else if (strpos($data['total'], "=") !== false) {
                $total_cfdi = str_replace("=", "", $data['total']);
                $this->repository->where([['total', "=", $total_cfdi]]);
            } else {
                $this->repository->where([['total', "=", $data['total']]]);
            }
        }


        if (isset($data['tipo_cambio'])) {
            $this->repository->where([['tipo_cambio', '=', $data['tipo_cambio'] ]]);
        }
        if (isset($data['subtotal'])) {
            $this->repository->where([['subtotal', '=', $data['subtotal'] ]]);
        }
        if (isset($data['descuento'])) {
            $this->repository->where([['descuento', '=', $data['descuento'] ]]);
        }
        if (isset($data['impuestos_retenidos'])) {
            $this->repository->where([['total_impuestos_retenidos', '=', $data['impuestos_retenidos'] ]]);
        }
        if (isset($data['impuestos_trasladados'])) {
            $this->repository->where([['total_impuestos_trasladados', '=', $data['impuestos_trasladados'] ]]);
        }
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['cfd_sat.fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        if (isset($data['tipo_comprobante'])) {
            $this->repository->where([['cfd_sat.tipo_comprobante', 'LIKE', '%' .$data['tipo_comprobante']. '%' ]]);
        }
        if (isset($data['serie'])) {
            $this->repository->where([['cfd_sat.serie', 'like', '' .$data['serie']. '' ]]);
        }
        if (isset($data['folio'])) {
            $this->repository->where([['cfd_sat.folio', 'like', '' .$data['folio']. '' ]]);
        }
        if (isset($data['estado'])) {
            if (strpos('CANCELADO', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 1]]);
            }
            else if (strpos('VIGENTE', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 0]]);
            }
        }
        if (isset($data['obra'])) {
            $obras = ConfiguracionObra::withoutGlobalScopes()->where([['nombre', 'LIKE', '%' . $data['obra'] . '%']])->get();

            foreach($obras as $obra){
                $id_obra[] = $obra->id_obra;
                $id_proyecto[] = $obra->id_proyecto;
            }

            $uuid = FacturaRepositorio::whereIn("id_obra", $id_obra)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }
        if (isset($data['base_datos'])) {
            $id_proyecto = Proyecto::where([['base_datos', 'LIKE', '%' . $data['base_datos'] . '%']])->pluck("id");

            $uuid = FacturaRepositorio::whereIn("id_proyecto", $id_proyecto)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }
        if (isset($data['solo_pendientes'])) {
            if($data['solo_pendientes']==="true"){
                $this->repository->whereDoesntHave("facturaRepositorio")->whereDoesntHave("polizaCFDI");
            }
        }

        if (isset($data['solo_asociados'])) {
            if($data['solo_asociados']==="true"){
                $this->repository->whereHas("facturaRepositorio");
            }
        }

        if (isset($data['solo_asociados_contabilidad'])) {
            if($data['solo_asociados_contabilidad']==="true"){
                $this->repository->whereHas("polizaCFDI");
            }
        }

        if (isset($data['solo_no_asociados_contabilidad'])) {
            if($data['solo_no_asociados_contabilidad']==="true"){
                $this->repository->whereDoesntHave("polizaCFDI");
            }
        }

        if (isset($data['base_datos_ctpq'])) {
            $this->repository->where([["ubicacion_contabilidad","like", '%'.$data['base_datos_ctpq']."%"]]);
        }
        if (isset($data['ejercicio'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_eje", "pol_eje.uuid","=","cfd_sat.uuid")
                ->where([['pol_eje.ejercicio', '=', $data['ejercicio'] ]])->select("cfd_sat.*");
        }
        if (isset($data['periodo'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_per", "pol_per.uuid","=","cfd_sat.uuid")
                ->where([['pol_per.periodo', '=', $data['periodo'] ]])->select("cfd_sat.*");
        }
        if (isset($data['tipo_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_tipo", "pol_tipo.uuid","=","cfd_sat.uuid")
                ->where([['pol_tipo.tipo', 'like', '%' .$data['tipo_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['folio_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_folio", "pol_folio.uuid","=","cfd_sat.uuid")
                ->where([['pol_folio.folio', 'like', '%' .$data['folio_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['fecha_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_fecha", "pol_fecha.uuid","=","cfd_sat.uuid")
                ->whereBetween( ['pol_fecha.fecha', [ request( 'fecha_poliza' )." 00:00:00",request( 'fecha_poliza' )." 23:59:59"]] )->select("cfd_sat.*");
        }

        if($data['no_hermes'] === "false" && $data['es_hermes'] === "true"){
            $this->repository->whereHas("proveedorHermes");
        }else if($data['no_hermes'] === "true" && $data['es_hermes'] === "false"){
            $this->repository->whereHas("proveedorNoHermes");
        }

        if($data['con_contactos'] === "false" && $data['sin_contactos'] === "true"){
            $this->repository->whereHas("proveedorSinContactos");
        }else if($data['con_contactos'] === "true" && $data['sin_contactos'] === "false"){
            $this->repository->whereHas("proveedorConContactos");
        }

        return $this->repository->paginate($data);
    }

    private function store($arreglo_factura = null)
    {
        if($arreglo_factura){
            $transaccion_cfd = $this->repository->registrar($arreglo_factura);
        } else {
            $transaccion_cfd = $this->repository->registrar($this->arreglo_factura);
        }

        return $transaccion_cfd;
    }

    public function storeZIPCFD($nombre_archivo, $archivo_zip)
    {
        $this->carga = $this->repository->iniciaCarga($nombre_archivo);
        $this->arreglo_factura["id_carga_cfd_sat"] = $this->carga->id;
        $this->log["nombre_archivo_zip"] = $nombre_archivo;
        $paths = $this->generaDirectorios();
        $exp = explode("base64,", $archivo_zip);
        $data = base64_decode($exp[1]);
        $file = public_path($paths["path_zip"]);
        file_put_contents($file, $data);
        $this->extraeZIP($paths["path_zip"], $paths["path_xml"]);
        $this->procesaDirectorio($paths["path_xml"]);
        $this->log["fecha_hora_fin"] = date("Y-m-d H:i:s");
        $this->carga->update($this->log);
        $this->repository->actualizaNoLocalizados($this->carga);
        return $this->carga;
    }

    private function extraeZIP($ruta_origen, $ruta_destino)
    {
        try {
            $zipper = new Zipper;
            $zipper->make(public_path($ruta_origen))->extractTo(public_path($ruta_destino));
        } catch (\Exception $e) {
            abort(500, "Hubo un error al extraer el archivo zip proporcionado. Ruta Origen: " . $ruta_origen . 'Ruta Destino: ' . $ruta_destino . ' Ln.' . $e->getLine() . ' ' . $e->getMessage());
        }
        $zipper->delete();
    }

    private function generaDirectorios()
    {
        $nombre = date("Ymdhis");
        $nombre_zip = $nombre . ".zip";
        $dir_zip = "uploads/contabilidad/cfd/zip/";
        $dir_xml = "uploads/contabilidad/cfd/xml/";
        $path_xml = $dir_xml . $nombre . "/";
        $path_zip = $dir_zip . $nombre_zip;
        if (!file_exists($dir_zip) && !is_dir($dir_zip)) {
            mkdir($dir_zip, 777, true);
        }
        if (!file_exists($dir_xml) && !is_dir($dir_xml)) {
            mkdir($dir_xml, 777, true);
        }
        return ["path_zip" => $path_zip, "path_xml" => $path_xml, "dir_xml" => $dir_xml];
    }

    public function reprocesaCFDILlenadoMetodoPago()
    {
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);

        $cantidad= CFDSAT::whereNull("metodo_pago")
            ->where("cancelado","=",0)
            ->where("tipo_comprobante","=","I")
            ->where("version","=","3.3")
            ->count();

        $take = 1000;
        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = CFDSAT::whereNull("metodo_pago")
                ->where("cancelado","=",0)
                ->where("tipo_comprobante","=","I")
                ->where("version","=","3.3")
                ->skip($i)
                ->take($take)
                ->orderBy("id","desc")
                ->get();

            foreach ($cfd as $rcfd) {
                try{
                    $cfd_util = new CFD($rcfd->xml);
                    $arreglo_cfd = $cfd_util->getArregloFactura();

                    try {
                        if(key_exists("metodo_pago",$arreglo_cfd)){
                            $rcfd->metodo_pago = $arreglo_cfd["metodo_pago"];
                            $rcfd->save();
                        }
                    }
                    catch (\Exception $e)
                    {
                        //dd('1',$e->getMessage());
                    }
                } catch (\Exception $e){
                    //dd('2',$e->getMessage());
                }
            }
        }
    }

    public function reprocesaCFDILlenadoPago()
    {
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);

        $cantidad= CFDSAT::pendienteProcesamientoDoctosPagados()->count();

        $take = 1000;
        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = CFDSAT::pendienteProcesamientoDoctosPagados()
                //->where("uuid","=","ad84e2f6-911b-4294-a586-079ee751fa99") 00FB75DC-04A7-424A-B25F-97977CF23A82
                //where("uuid","=","00FB75DC-04A7-424A-B25F-97977CF23A82")
                ->skip($i)
                ->take($take)
                ->get();

            foreach ($cfd as $rcfd) {
                try{
                    $cfd_util = new CFD($rcfd->xml);
                    $arreglo_cfd = $cfd_util->getArregloFactura();

                    try {
                        if(key_exists("forma_pago_p",$arreglo_cfd)){
                            $rcfd->forma_pago_p = $arreglo_cfd["forma_pago_p"];
                            $rcfd->moneda_pago = $arreglo_cfd["moneda_pago"];
                            $rcfd->monto_pago = $arreglo_cfd["monto_pago"];
                            $rcfd->save();
                        }
                    }
                    catch (\Exception $e)
                    {
                        //dd('1',$e->getMessage());
                    }
                } catch (\Exception $e){
                    //dd('2',$e->getMessage());
                }

                if(key_exists("documentos_pagados",$arreglo_cfd)){
                    foreach($arreglo_cfd["documentos_pagados"] as $documento_pagado){
                        $cfdi_pagado = CFDSAT::where("uuid", $documento_pagado["uuid"])->first();
                        if($cfdi_pagado){
                            $documento_pagado["id_cfdi_pagado"] = $cfdi_pagado->id;
                        }
                        $rcfd->documentosPagados()->create($documento_pagado);
                    }
                }
            }
        }
    }

    public function reprocesaCFDObtenerTipo()
    {
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);
        $cantidad = CFDSAT::where("id_empresa_sat","=",1)
            ->where("cancelado","=","0")
            ->where("rfc_emisor","=","GMS971110BTA")
            ->whereIn("tipo_comprobante",["I","E"])
            ->whereBetween("fecha",["2014-01-01 00:00:00","2016-12-31 23:59:59"])
            ->count();

        $take = 1000;

        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = CFDSAT::where("id_empresa_sat","=",1)
                ->where("cancelado","=","0")
                ->where("rfc_emisor","=","GMS971110BTA")
                ->whereIn("tipo_comprobante",["I","E"])
                ->whereBetween("fecha",["2014-01-01 00:00:00","2016-12-31 23:59:59"])
                ->skip($i)
                ->take($take)
                ->get();
            foreach ($cfd as $rcfd) {
                try{
                    $cfd = new CFD($rcfd->xml);
                } catch (\Exception $e){
                    dd("No se cargo el CFDI");
                }

                $arreglo_cfd = $cfd->getArregloFactura();
                $rcfd->subtotal = $arreglo_cfd["subtotal"];
                $rcfd->descuento = $arreglo_cfd["descuento"];
                $rcfd->metodo_pago = $arreglo_cfd["metodo_pago"];
                $rcfd->tipo_cambio = $arreglo_cfd["tipo_cambio"];
                $rcfd->total_impuestos_retenidos = $arreglo_cfd["total_impuestos_retenidos"];
                $rcfd->total_impuestos_trasladados = $arreglo_cfd["total_impuestos_trasladados"];
                $rcfd->save();
                if($arreglo_cfd["tipo_relacion"]>0){
                    $rcfd->tipo_relacion = $arreglo_cfd["tipo_relacion"];
                    $rcfd->cfdi_relacionado = $arreglo_cfd["cfdi_relacionado"];
                    $rcfd->save();
                }else{
                    $rcfd->tipo_relacion = null;
                    $rcfd->cfdi_relacionado = null;
                    $rcfd->save();
                }


            }
            /*if ($i > 15000) {
                break;
            }*/
        }
    }

    public function procesaDirectorioZIPCFD()
    {
        //$carga = CargaCFDSAT::find(213);
        //event(new CambioEFOS($carga->cambios));

        ini_set('max_execution_time', '7200');
        $this->carga = $this->repository->iniciaCarga("inicial");
        $this->arreglo_factura["id_carga_cfd_sat"] = $this->carga->id;

        $path = "uploads/contabilidad/zip_cfd/";
        $this->preparaDirectorio($path);
        $this->procesaDirectorio($path);
        $this->log["fecha_hora_fin"] = date("Y-m-d H:i:s");
        $this->carga->update($this->log);

        if(file_exists(public_path("uploads/contabilidad/XML_errores/".$this->carga->id)))
        {
            $zipper = new Zipper;
            $zipper->make(public_path("uploads/contabilidad/XML_errores/".$this->carga->id.".zip"))->add(public_path("uploads/contabilidad/XML_errores/".$this->carga->id));
            $zipper->close();
        }
        $this->repository->finalizaCarga($this->carga);

        event(new FinalizaCargaCFD($this->carga));
        if(count($this->carga->cambios)>0){
            event(new CambioEFOS($this->carga->cambios));
        }

        $this->carga->load("usuario");
        $this->repository->actualizaNoLocalizados($this->carga);

        if(count($this->carga->noLocalizados)>0){
            event(new CambioNoLocalizados($this->carga->noLocalizados));
        }

        return $this->carga;
    }

    private function preparaDirectorio($path)
    {
        $dir = opendir(public_path($path));
        while ($current = readdir($dir)) {
            if ($current != "." && $current != "..") {
                if (is_dir($path . $current)) {
                    $this->preparaDirectorio($path . $current . '/');
                } else {
                    if (strpos($current, ".zip")) {
                        Files::extraeZIP($path . $current);
                    }
                }
            }
        }
        return true;
    }

    public function procesaFacturaRepositorio(FacturaRepositorio $facturaRepositorio)
    {
        $exp = explode("base64,", $facturaRepositorio->xml_file);
        $contenido_archivo= base64_decode($exp[1]);
        $file = public_path("uploads/".$facturaRepositorio->uuid.".xml");
        file_put_contents($file, $contenido_archivo);
        $this->procesaArchivoCFDI($file, $facturaRepositorio->uuid.".xml");
        $cfdi = $this->repository->where([["uuid","=",$facturaRepositorio->uuid]])->first();
        $cfdi->id_factura_repositorio = $facturaRepositorio->id;
        $cfdi->save();
    }

    private function procesaArchivoCFDI($ruta_archivo, $current)
    {
        $contenido_archivo_xml = file_get_contents($ruta_archivo);
        $resultado = $this->setArregloFactura($ruta_archivo);
        if ($resultado == 0) {
            Storage::disk('xml_errores')->put($this->carga->id . '/error_app/' . $current, fopen($ruta_archivo, "r"));
            unlink($ruta_archivo);
        } else {
            if (key_exists("uuid", $this->arreglo_factura)) {
                if (!$this->repository->validaExistencia($this->arreglo_factura["uuid"])) {
                    if ($this->arreglo_factura["id_empresa_sat"] > 0) {
                        $this->arreglo_factura["xml_file"] = $this->repository->getArchivoSQL(base64_encode($contenido_archivo_xml));
                        if ($this->store()) {
                            Storage::disk('xml_sat')->put($this->arreglo_factura["uuid"] . ".xml", fopen($ruta_archivo, "r"));
                            unlink($ruta_archivo);
                            $this->log["archivos_cargados"] += 1;
                            $this->log["cfd_cargados"] += 1;
                        }
                    } else {
                        $this->log["cfd_no_cargados"] += 1;
                        $this->log["archivos_no_cargados"] += 1;
                        $this->log["archivos_receptor_no_valido"] += 1;
                        $this->log["receptores_no_validos"][] = $this->arreglo_factura["receptor"];
                        Storage::disk('xml_errores')->put($this->carga->id . '/receptor_no_valido/' . $current, fopen($ruta_archivo, "r"));
                        unlink($ruta_archivo);
                    }
                } else {
                    $this->log["archivos_preexistentes"] += 1;
                    $this->log["archivos_no_cargados"] += 1;
                    $this->log["cfd_no_cargados"] += 1;
                    unlink($ruta_archivo);
                }
            } else {
                $this->log["cfd_no_cargados"] += 1;
                $this->log["archivos_no_cargados"] += 1;
                $this->log["archivos_corruptos"] += 1;
                Storage::disk('xml_errores')->put($this->carga->id . '/corruptos/' . $current, fopen($ruta_archivo, "r"));
                unlink($ruta_archivo);
            }
        }
    }

    private function procesaDirectorio($path)
    {
        $dir = opendir($path);
        while ($current = readdir($dir)) {
            if ($current != "." && $current != "..") {
                if (is_dir($path . $current)) {
                    $this->procesaDirectorio($path . $current . "/");
                } else {
                    $this->log["archivos_leidos"] += 1;
                    $ruta_archivo = $path . "/" . $current;
                    if (strpos($current, ".xml")) {
                        $this->procesaArchivoCFDI($ruta_archivo, $current);
                    } else if (strpos($current, ".txt") && $current !== "errors.txt") {
                        $contenido_archivo_txt = file_get_contents($ruta_archivo);
                        $resultado = $this->setArreglosFacturas($ruta_archivo);
                        if ($resultado == 0) {
                            Storage::disk('xml_errores')->put($this->carga->id . '/error_app/' . $current, fopen($ruta_archivo, "r"));
                            unlink($ruta_archivo);
                            $this->log["archivos_no_cargados"] += 1;
                            $this->log["archivos_corruptos"] += 1;

                        } else {
                            $this->log["archivos_cargados"] += 1;
                            Storage::disk('xml_sat')->put($this->carga->id .'/'.$this->arreglos_factura[0]["rfc_receptor"]. '/' . $current, fopen($ruta_archivo, "r"));
                            unlink($ruta_archivo);
                            foreach ($this->arreglos_factura as $arreglo_factura){
                                if (key_exists("uuid", $arreglo_factura)) {
                                    if (!$this->repository->validaExistencia($arreglo_factura["uuid"])) {
                                        if ($arreglo_factura["id_empresa_sat"] > 0) {
                                            if ($this->store($arreglo_factura)) {
                                                $this->log["cfd_cargados"] += 1;
                                            }
                                        } else {
                                            $this->log["cfd_no_cargados"] += 1;
                                            $this->log["cfd_receptor_no_valido"] += 1;
                                            $this->log["receptores_no_validos"][] = $arreglo_factura["receptor"];
                                        }
                                    } else {
                                        $this->log["cfd_preexistentes"] += 1;
                                        $this->log["cfd_no_cargados"] += 1;
                                    }
                                }
                            }
                        }
                    }
                    else {
                        $this->log["archivos_no_cargados"] += 1;
                        $this->log["cfd_no_cargados"] += 1;
                        $this->log["archivos_tipo_incorrecto"] += 1;
                        Storage::disk('xml_errores')->put($this->carga->id . '/tipo_incorrecto/' . $current, fopen($ruta_archivo, "r"));
                        unlink($ruta_archivo);
                    }
                }
            }
        }

        $contenido = @scandir($path);

        if (count($contenido) <= 2 && $path != "uploads/contabilidad/zip_cfd/") {
            closedir($dir);
            rmdir($path);
        }
    }

    private function setArreglosFacturas($archivo_txt){
        $this->arreglos_factura = [];
        /*
         * TODO: implementar uso de auxiliar CFD para generar el arreglo y contenga traslados y relaciones
         */
        /*$cfd = new CFD($archivo_xml);
        $arreglo_cfd = $cfd->getArregloFactura();*/

        try{
            $myfile = fopen($archivo_txt, "r");
        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        $linea = 0;
        $i = 0;
        while(!feof($myfile)) {
            $linea_archivo = fgets($myfile);
            $renglon = explode("~", $linea_archivo);
            if(key_exists(1, $renglon) &&  $renglon[0] != "Uuid"){

                if(substr($renglon[count($renglon)-1], -2) != "" && substr($renglon[count($renglon)-1], -2) != "\r\n"){
                    $renglon[count($renglon)-1] = str_replace(["\n", '"'],"",$renglon[count($renglon)-1]);
                    $fin = false;
                    while(!$fin){
                        $add = explode("~",fgets($myfile));
                        $renglon[count($renglon)-1] .= " ".$add[0];
                        array_shift($add);
                        $renglon = array_merge($renglon , $add);
                        $fin = substr($renglon[count($renglon)-1], -2) == "\r\n";
                    }
                }
                $this->arreglos_factura[$i]["id_carga_cfd_sat"] = $this->carga->id;
                $this->arreglos_factura[$i]["version"] = "txt";
                $this->arreglos_factura[$i]["uuid"] = $renglon[0];
                $this->arreglos_factura[$i]["rfc_emisor"] = $renglon[1];
                $this->arreglos_factura[$i]["rfc_receptor"] = $renglon[3];
                $this->arreglos_factura[$i]["fecha"] = $renglon[6].".000";
                $this->arreglos_factura[$i]["total"] = $renglon[8];
                $this->arreglos_factura[$i]["subtotal"] = 100 * $renglon[8] /116;
                $this->arreglos_factura[$i]["importe_iva"] = 16 * $renglon[8] /116;
                $this->arreglos_factura[$i]["tipo_comprobante"] = $renglon[9];
                $this->arreglos_factura[$i]["estado_txt"] = $renglon[10];
                $this->arreglos_factura[$i]["fecha_cancelacion"] = ($renglon[11]!="\r\n")?$renglon[11].".000":null;

                $this->arreglos_factura[$i]["emisor"]["rfc"] = (string)$renglon[1];
                $this->arreglos_factura[$i]["emisor"]["razon_social"] = (string)$renglon[2];
                $this->arreglos_factura[$i]["receptor"]["rfc"] = (string)$renglon[3];
                $this->arreglos_factura[$i]["receptor"]["nombre"] = (string)$renglon[4];

                $this->arreglos_factura[$i]["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglos_factura[$i]["receptor"]);
                $proveedor = $this->repository->getProveedorSAT($this->arreglos_factura[$i]["emisor"], $this->arreglos_factura[$i]["id_empresa_sat"]);
                $this->arreglos_factura[$i]["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglos_factura[$i]["receptor"]);
                $this->arreglos_factura[$i]["id_proveedor_sat"] = $proveedor["id_proveedor"];

                if ($proveedor["nuevo"] > 0) {
                    $this->log["proveedores_nuevos"] += 1;
                }
                $i++;
            }
            $linea++;

        }
        return 1;
    }

    private function setArregloFactura($archivo_xml)
    {
        $this->arreglo_factura = [];
        $this->arreglo_factura["id_carga_cfd_sat"] = ($this->carga)?$this->carga->id:null;
        try {
            libxml_use_internal_errors(true);
            $factura_xml = simplexml_load_file($archivo_xml);
            if($factura_xml === false)
            {
                $factura_xml = simplexml_load_string($archivo_xml);
            }

            if(!$factura_xml){
                $errors = libxml_get_errors();
                //dd(var_export($errors, true));
                $this->log["archivos_no_cargados_error_app"] += 1;
                $this->log["cfd_no_cargados_error_app"] += 1;
                return 0;
            }

        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el archivo XML proporcionado. " . ' Ln.' . $e->getLine() . ' ' . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        //$factura_simple_xml = new \SimpleXMLElement(file_get_contents($archivo_xml));
        if ((string)$factura_xml["version"] == "3.2") {
            $this->arreglo_factura["version"] = (string)$factura_xml["version"];
            return $this->setArreglo32($factura_xml);
        } else if ($factura_xml["Version"] == "3.3" || $factura_xml["Version"] == "4.0") {
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            return $this->setArreglo33($factura_xml);
        }
        return 1;
    }

    private function getFecha(string $fecha)
    {
        $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s', $fecha);
        if (!$fecha_xml) {
            $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s.u', $fecha);
            if (!$fecha_xml) {
                $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $fecha);
                if (!$fecha_xml) {
                    $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s', substr($fecha, 0, 19));
                    if (!$fecha_xml) {
                        $fecha_xml = substr($fecha, 0, 19);
                    }
                }
            }
        }
        return $fecha_xml;
    }

    private function getFechaHora(string $fecha)
    {
        $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s', $fecha);
        if (!$fecha_xml) {
            $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s.u', $fecha);
            if (!$fecha_xml) {
                $fecha_xml = substr($fecha, 0, 19);
            }
        }
        return $fecha_xml->format('Y-m-d H:i:s');
    }

    private function setArreglo33($factura_xml)
    {
        try {
            $this->arreglo_factura["descuento"] = (float)$factura_xml["Descuento"];
            $this->arreglo_factura["total"] = (float)$factura_xml["Total"];
            $this->arreglo_factura["subtotal"] = (float)$factura_xml["SubTotal"];
            $this->arreglo_factura["tipo_comprobante"] = strtoupper(substr((string)$factura_xml["TipoDeComprobante"], 0, 1));
            $this->arreglo_factura["serie"] = (string)$factura_xml["Serie"];
            $this->arreglo_factura["folio"] = (string)$factura_xml["Folio"];
            $this->arreglo_factura["fecha"] = $this->getFecha((string)$factura_xml["Fecha"]);
            $this->arreglo_factura["fecha_hora"] = $this->getFechaHora((string)$factura_xml["Fecha"]);
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
            $this->arreglo_factura["tipo_cambio"] = (string)$factura_xml["TipoCambio"];
            $this->arreglo_factura["metodo_pago"] = (string)$factura_xml["MetodoPago"];
            $this->arreglo_factura["no_certificado"] = (string)$factura_xml["NoCertificado"];
            $this->arreglo_factura["certificado"] = (string)$factura_xml["Certificado"];
            $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
            $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["Rfc"][0];
            $this->arreglo_factura["emisor"]["razon_social"] = (string)$emisor["Nombre"][0];
            $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)Util::eliminaCaracteresEspeciales($emisor["RegimenFiscal"][0]);
            $this->arreglo_factura["rfc_emisor"] = $this->arreglo_factura["emisor"]["rfc"];
            $receptor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
            $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["Rfc"][0];
            $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["Nombre"][0];
            $this->arreglo_factura["rfc_receptor"] = $this->arreglo_factura["receptor"]["rfc"];
            $this->arreglo_factura["importe_iva"] = 0;
            $this->arreglo_factura["tasa_iva"] = 0;
            $this->arreglo_factura["traslados"] = [];
        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        if($this->arreglo_factura["tipo_comprobante"] == "P"){
            $this->setDatosPago($factura_xml);
        }

        $this->arreglo_factura["tipo_relacion"] = '';
        $this->arreglo_factura["cfdi_relacionado"]  ='';

        $CFDIRelacionado = $factura_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados');
        if(count($CFDIRelacionado)>0){
            $CFDIRelacionado = $factura_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados')[0];
            $this->arreglo_factura["tipo_relacion"] = (string)$CFDIRelacionado["TipoRelacion"][0];
            $this->arreglo_factura["cfdi_relacionado"] = (string)$factura_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados//cfdi:CfdiRelacionado')[0]["UUID"];
        }

        try {
            $ns = $factura_xml->getNamespaces(true);
            $impuestos = $factura_xml->xpath('//cfdi:Comprobante/cfdi:Impuestos');
            if (count($impuestos) >= 1) {
                $this->arreglo_factura["total_impuestos_trasladados"] = (float)$impuestos[0]["TotalImpuestosTrasladados"];
            } else {
                $this->arreglo_factura["total_impuestos_trasladados"] = (float)0;
            }
            $traslados = $factura_xml->xpath('//cfdi:Comprobante/cfdi:Impuestos//cfdi:Traslado');

            $i = 0;
            foreach ($traslados as $traslado) {
                if ($traslado["Impuesto"] == "002") {
                    $this->arreglo_factura["importe_iva"] = (float)$traslado["Importe"];
                    $this->arreglo_factura["tasa_iva"] = (float)$traslado["TasaOCuota"];
                }
                $this->arreglo_factura["traslados"][$i]["impuesto"] = (string)$traslado["Impuesto"];
                $this->arreglo_factura["traslados"][$i]["tipo_factor"] = (string)$traslado["TipoFactor"];
                $this->arreglo_factura["traslados"][$i]["tasa_o_cuota"] = (float)$traslado["TasaOCuota"];
                $this->arreglo_factura["traslados"][$i]["importe"] = (float)$traslado["Importe"];
                $this->arreglo_factura["traslados"][$i]["base"] =  (float)$traslado["Base"];
                $i++;
            }
            if (count($impuestos) >= 1) {
                $this->arreglo_factura["total_impuestos_retenidos"] = (float)$impuestos[0]["TotalImpuestosRetenidos"];
            } else {
                $this->arreglo_factura["total_impuestos_retenidos"] = (float)0;
            }

            $retenciones = $factura_xml->xpath('//cfdi:Comprobante/cfdi:Impuestos//cfdi:Retencion');

            $iret = 0;
            foreach ($retenciones as $retencion) {
                if ($retencion["Impuesto"] == "002") {
                    $this->arreglo_factura["importe_iva_retenido"] = (float)$retencion["Importe"];
                    $this->arreglo_factura["tasa_iva_retenido"] = (float)$retencion["TasaOCuota"];
                }
                $this->arreglo_factura["retenciones"][$iret]["impuesto"] = (string)$retencion["Impuesto"];
                $this->arreglo_factura["retenciones"][$iret]["tipo_factor"] = (string)$retencion["TipoFactor"];
                $this->arreglo_factura["retenciones"][$iret]["tasa_o_cuota"] = (float)$retencion["TasaOCuota"];
                $this->arreglo_factura["retenciones"][$iret]["importe"] = (float)$retencion["Importe"];
                $this->arreglo_factura["retenciones"][$iret]["base"] = (float)$retencion["Base"];
                $iret++;
            }

            $conceptos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Concepto');
            $i = 0;
            $ic = 1;
            foreach ($conceptos as $concepto) {
                $this->arreglo_factura["conceptos"][$i]["clave_prod_serv"] = (string)$concepto["ClaveProdServ"];
                $this->arreglo_factura["conceptos"][$i]["no_identificacion"] = (string)$concepto["NoIdentificacion"];
                $this->arreglo_factura["conceptos"][$i]["cantidad"] = (float)$concepto["Cantidad"];
                $this->arreglo_factura["conceptos"][$i]["clave_unidad"] = (string)$concepto["ClaveUnidad"];
                $this->arreglo_factura["conceptos"][$i]["unidad"] = (string)$concepto["Unidad"];
                $this->arreglo_factura["conceptos"][$i]["descripcion"] = (string)$concepto["Descripcion"];
                $this->arreglo_factura["conceptos"][$i]["valor_unitario"] = (float)$concepto["ValorUnitario"];
                $this->arreglo_factura["conceptos"][$i]["importe"] = (float)$concepto["Importe"];
                $this->arreglo_factura["conceptos"][$i]["descuento"] = (float)$concepto["Descuento"];
                $traslados_concepto = $factura_xml->xpath("/cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto[".$ic."]/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado");
                $itc = 0;
                foreach ($traslados_concepto as $traslado_concepto) {
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["base"] = (float)$traslado_concepto["Base"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["impuesto"] = (string)$traslado_concepto["Impuesto"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["importe"] = (float)$traslado_concepto["Importe"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["tasa_o_cuota"] = (float)$traslado_concepto["TasaOCuota"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["tipo_factor"] = (string)$traslado_concepto["TipoFactor"];
                    $itc++;
                }
                $retenciones_concepto = $factura_xml->xpath("/cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto[".$ic."]/cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion");
                $irc = 0;
                foreach ($retenciones_concepto as $retencion_concepto) {
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["base"] = (float)$retencion_concepto["Base"];
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["impuesto"] = (string)$retencion_concepto["Impuesto"];
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["importe"] = (float)$retencion_concepto["Importe"];
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["tasa_o_cuota"] = (float)$retencion_concepto["TasaOCuota"];
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["tipo_factor"] = (string)$retencion_concepto["TipoFactor"];
                    $irc++;
                }
                $i++;
                $ic++;
            }

        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        try {
            if (key_exists("cfdi", $ns)) {
                $factura_xml->registerXPathNamespace('c', $ns['cfdi']);
            }
            $factura_xml->registerXPathNamespace('t', $ns['tfd']);
            $complemento = $factura_xml->xpath('//t:TimbreFiscalDigital')[0];
            $this->arreglo_factura["uuid"] = (string)$complemento["UUID"][0];
            if (!$this->arreglo_factura["folio"]) {
                try {
                    $factura_xml->registerXPathNamespace('rf', $ns['registrofiscal']);
                    $CFDI_RF = $factura_xml->xpath('//rf:CFDIRegistroFiscal')[0];
                    $this->arreglo_factura["folio"] = $CFDI_RF["Folio"];
                } catch (\Exception $e) {
                    $this->arreglo_factura["folio"] = "";
                }
            }
        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        $this->arreglo_factura["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglo_factura["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($this->arreglo_factura["emisor"], $this->arreglo_factura["id_empresa_sat"]);
        $this->arreglo_factura["id_proveedor_sat"] = $proveedor["id_proveedor"];

        if ($proveedor["nuevo"] > 0) {
            $this->log["proveedores_nuevos"] += 1;
        }
        return 1;
    }

    private function setArreglo32($factura_xml)
    {
        $this->arreglo_factura["subtotal"] = (float)$factura_xml["subTotal"];
        $this->arreglo_factura["tipo_comprobante"] = strtoupper(substr((string)$factura_xml["tipoDeComprobante"], 0, 1));
        $this->arreglo_factura["descuento"] = (float)$factura_xml["descuento"];
        $this->arreglo_factura["total"] = (float)$factura_xml["total"];
        $this->arreglo_factura["serie"] = (string)$factura_xml["serie"];
        $this->arreglo_factura["folio"] = (string)$factura_xml["folio"];
        $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
        $this->arreglo_factura["tipo_cambio"] = (float)$factura_xml["TipoCambio"];
        $this->arreglo_factura["fecha"] = $this->getFecha((string)$factura_xml["fecha"]);

        $ns = $factura_xml->getNamespaces(true);
        $factura_xml->registerXPathNamespace('t', $ns['tfd']);
        $complemento = $factura_xml->xpath('//t:TimbreFiscalDigital')[0];
        $uuid = (string)$complemento["UUID"][0];
        $this->arreglo_factura["uuid"] = $uuid;

        try {
            $emisor_arr = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor');
            if ($emisor_arr) {
                if (key_exists(0, $emisor_arr)) {
                    $emisor = $emisor_arr[0];
                    $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string) Util::eliminaCaracteresEspeciales($factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:RegimenFiscal')[0]["Regimen"]);
                } else {
                    $emisor = $factura_xml->Emisor;
                    $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)Util::eliminaCaracteresEspeciales($emisor->RegimenFiscal[0]["Regimen"]);
                }
            } else {
                $emisor = $factura_xml->Emisor;
                $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)Util::eliminaCaracteresEspeciales($emisor->RegimenFiscal[0]["Regimen"]);
            }

            $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["rfc"][0];
            $this->arreglo_factura["rfc_emisor"] = $this->arreglo_factura["emisor"]["rfc"];
            $this->arreglo_factura["emisor"]["razon_social"] = (string)$emisor["nombre"][0];
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el emisor del comprobante: ".$uuid." mensaje:" . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        try {
            $receptor_arr = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor');
            if ($receptor_arr) {
                if (key_exists(0, $receptor_arr)) {
                    $receptor = $receptor_arr[0];
                } else {
                    $receptor = $factura_xml->Receptor;
                }
            } else {
                $receptor = $factura_xml->Receptor;
            }

            $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["rfc"][0];
            $this->arreglo_factura["rfc_receptor"] = $this->arreglo_factura["receptor"]["rfc"];
            $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["nombre"][0];
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el receptor del comprobante: ".$uuid." mensaje:" . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        try {
            $impuestos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos');
            $this->arreglo_factura["total_impuestos_trasladados"] = (float)$impuestos[0]["totalImpuestosTrasladados"][0];
            $traslados = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslado');
            $i = 0;
            foreach ($traslados as $traslado) {
                if ($traslado["impuesto"] == "IVA") {
                    $this->arreglo_factura["importe_iva"] = (float)$traslado["importe"];
                    $this->arreglo_factura["tasa_iva"] = (float)$traslado["tasa"];
                }
                $this->arreglo_factura["traslados"][$i]["impuesto"] = (string)$traslado["impuesto"];
                $this->arreglo_factura["traslados"][$i]["tasa_o_cuota"] = (string)$traslado["tasa"];
                $this->arreglo_factura["traslados"][$i]["importe"] = (string)$traslado["importe"];
                $i++;
            }
            $conceptos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Concepto');
            $i = 0;
            foreach ($conceptos as $concepto) {
                $this->arreglo_factura["conceptos"][$i]["cantidad"] = (float)$concepto["cantidad"];
                $this->arreglo_factura["conceptos"][$i]["descripcion"] = (string)$concepto["descripcion"];
                $this->arreglo_factura["conceptos"][$i]["importe"] = (float)$concepto["importe"];
                $this->arreglo_factura["conceptos"][$i]["no_identificacion"] = (string)$concepto["noIdentificacion"];
                $this->arreglo_factura["conceptos"][$i]["unidad"] = (string)$concepto["unidad"];
                $this->arreglo_factura["conceptos"][$i]["valor_unitario"] = (float)$concepto["valorUnitario"];
                $i++;
            }
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer la ruta de impuestos o conceptos: " . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        try {
            if (key_exists("cfdi", $ns)) {
                $factura_xml->registerXPathNamespace('c', $ns['cfdi']);
            }

            if (!$this->arreglo_factura["folio"]) {
                try {
                    $factura_xml->registerXPathNamespace('rf', $ns['registrofiscal']);
                    $CFDI_RF = $factura_xml->xpath('//rf:CFDIRegistroFiscal')[0];
                    $this->arreglo_factura["folio"] = $CFDI_RF["folio"];
                } catch (\Exception $e) {
                    $this->arreglo_factura["folio"] = "";
                }
            }
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer la ruta de complemento: " . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        $this->arreglo_factura["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglo_factura["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($this->arreglo_factura["emisor"], $this->arreglo_factura["id_empresa_sat"]);
        $this->arreglo_factura["id_proveedor_sat"] = $proveedor["id_proveedor"];
        if ($proveedor["nuevo"] > 0) {
            $this->log["proveedores_nuevos"] += 1;
        }
        return 1;
    }

    public function obtenerInformeEmpresaMes()
    {
        return $this->repository->getInformeEmpresaMes();
    }

    public function obtenerInformeCompleto()
    {
        return $this->repository->getInformeCompleto();
    }

    public function obtenerInformeCompletoPDF()
    {
        $informe = $this->obtenerInformeCompleto();
        $pdf = new InformeCFDICompleto($informe);
        return $pdf->create();
    }

    public function getContenidoDirectorio()
    {
        $path = "uploads/contabilidad/zip_cfd/";
        $contenido = Files::getFiles($path);
        return $contenido;
    }

    public function descargarIndividual($id)
    {
        $uuid =  $this->repository->show($id);

        $dir_xml = "uploads/contabilidad/XML_SAT/";
        $dir_descarga = "downloads/fiscal/descarga/".date("Ymdhis")."/";
        if (!file_exists($dir_descarga) && !is_dir($dir_descarga)) {
            mkdir($dir_descarga, 777, true);
        }
        try{
            copy($dir_xml.$uuid->uuid.".xml", $dir_descarga.$uuid->uuid.".xml");
        }catch (\Exception $e){
            $data_cfdi =  base64_decode($uuid->xml_file);
            $file = public_path($dir_descarga.$uuid->uuid.".xml");
            file_put_contents($file, $data_cfdi);
        }

        if(file_exists(public_path($dir_descarga.$uuid->uuid.".xml"))){
            return response()->download(public_path($dir_descarga.$uuid->uuid.".xml"));
        } else {
            return response()->json(["mensaje"=>"No hay CFDI para la descarga: ".$uuid->uuid]);
        }
    }

    public function descargar($data){
        if (isset($data['startDate'])) {
            $this->repository->where([['cfd_sat.fecha', '>=', $data['startDate']]]);
        }
        if (isset($data['endDate'])) {
            $this->repository->where([['cfd_sat.fecha', '<=', $data['endDate']]]);
        }
        if (isset($data['rfc_emisor'])) {
            $this->repository->where([['rfc_emisor', 'LIKE', '%' . $data['rfc_emisor'] . '%']]);
        }
        if (isset($data['emisor'])) {
            $proveedoresSAT = ProveedorSAT::query()->where([['razon_social', 'LIKE', '%' . $data['emisor'] . '%']])->get();
            $arreglo_proveedor = [];
            foreach ($proveedoresSAT as $e) {
                $arreglo_proveedor[] = $e->id;
            }
            $this->repository->whereIn(['id_proveedor_sat', $arreglo_proveedor]);
        }
        if (isset($data['rfc_receptor'])) {
            $this->repository->where([['rfc_receptor', 'LIKE', '%' . $data['rfc_receptor'] . '%']]);
        }
        if (isset($data['receptor'])) {
            $empresasSAT = EmpresaSAT::query()->where([['razon_social', 'LIKE', '%' . $data['receptor'] . '%']])->get();
            $arreglo_empresa = [];
            foreach ($empresasSAT as $es) {
                $arreglo_empresa[] = $es->id;
            }
            $this->repository->whereIn(['id_empresa_sat', $arreglo_empresa]);
        }
        if (isset($data['uuid'])) {
            $this->repository->where([['cfd_sat.uuid', 'LIKE', '%' . $data['uuid'] . '%']]);
        }
        if (isset($data['moneda'])) {
            $this->repository->where([['moneda', 'LIKE', '%' . $data['moneda'] . '%']]);
        }
        if (isset($data['total'])) {
            $this->repository->where([['total', '=', $data['total'] ]]);
        }
        if (isset($data['tipo_cambio'])) {
            $this->repository->where([['tipo_cambio', '=', $data['tipo_cambio'] ]]);
        }
        if (isset($data['subtotal'])) {
            $this->repository->where([['subtotal', '=', $data['subtotal'] ]]);
        }
        if (isset($data['descuento'])) {
            $this->repository->where([['descuento', '=', $data['descuento'] ]]);
        }
        if (isset($data['impuestos_retenidos'])) {
            $this->repository->where([['total_impuestos_retenidos', '=', $data['impuestos_retenidos'] ]]);
        }
        if (isset($data['impuestos_trasladados'])) {
            $this->repository->where([['total_impuestos_trasladados', '=', $data['impuestos_trasladados'] ]]);
        }
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['cfd_sat.fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        if (isset($data['tipo_comprobante'])) {
            $this->repository->where([['cfd_sat.tipo_comprobante', 'LIKE', '%' .$data['tipo_comprobante']. '%' ]]);
        }
        if (isset($data['serie'])) {
            $this->repository->where([['cfd_sat.serie', 'like', '' .$data['serie']. '' ]]);
        }
        if (isset($data['folio'])) {
            $this->repository->where([['cfd_sat.folio', 'like', '' .$data['folio']. '' ]]);
        }
        if (isset($data['estado'])) {
            if (strpos('CANCELADO', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 1]]);
            }
            else if (strpos('VIGENTE', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 0]]);
            }
        }
        if (isset($data['obra'])) {
            $obras = ConfiguracionObra::withoutGlobalScopes()->where([['nombre', 'LIKE', '%' . $data['obra'] . '%']])->get();

            $id_obra = [];
            $id_proyecto = [];
            foreach($obras as $obra){
                $id_obra[] = $obra->id_obra;
                $id_proyecto[] = $obra->id_proyecto;
            }

            $uuid = FacturaRepositorio::whereIn("id_obra", $id_obra)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }
        if (isset($data['base_datos'])) {
            $id_proyecto = Proyecto::where([['base_datos', 'LIKE', '%' . $data['base_datos'] . '%']])->pluck("id");

            $uuid = FacturaRepositorio::whereIn("id_proyecto", $id_proyecto)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }

        if (isset($data['solo_pendientes'])) {
            if($data['solo_pendientes']==="true"){
                $this->repository->whereDoesntHave("facturaRepositorio")->whereDoesntHave("polizaCFDI");
            }
        }

        if (isset($data['solo_asociados'])) {
            if($data['solo_asociados']==="true"){
                $this->repository->whereHas("facturaRepositorio");
            }
        }

        if (isset($data['solo_asociados_contabilidad'])) {
            if($data['solo_asociados_contabilidad']==="true"){
                $this->repository->whereHas("polizaCFDI");
            }
        }

        if (isset($data['base_datos_ctpq'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_bd", "pol_bd.uuid","=","cfd_sat.uuid")
                ->where([['pol_bd.base_datos_contpaq', 'like', '%' .$data['base_datos_ctpq']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['ejercicio'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_eje", "pol_eje.uuid","=","cfd_sat.uuid")
                ->where([['pol_eje.ejercicio', '=', $data['ejercicio'] ]])->select("cfd_sat.*");
        }
        if (isset($data['periodo'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_per", "pol_per.uuid","=","cfd_sat.uuid")
                ->where([['pol_per.periodo', '=', $data['periodo'] ]])->select("cfd_sat.*");
        }
        if (isset($data['tipo_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_tipo", "pol_tipo.uuid","=","cfd_sat.uuid")
                ->where([['pol_tipo.tipo', 'like', '%' .$data['tipo_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['folio_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_folio", "pol_folio.uuid","=","cfd_sat.uuid")
                ->where([['pol_folio.folio', 'like', '%' .$data['folio_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['fecha_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_fecha", "pol_fecha.uuid","=","cfd_sat.uuid")
                ->whereBetween( ['pol_fecha.fecha', [ request( 'fecha_poliza' )." 00:00:00",request( 'fecha_poliza' )." 23:59:59"]] )->select("cfd_sat.*");
        }

        $uuid =  $this->repository->all();

        $dir_xml = "uploads/contabilidad/XML_SAT/";
        $dir_descarga = "downloads/fiscal/descarga/".date("Ymdhis")."/";
        if (!file_exists($dir_descarga) && !is_dir($dir_descarga)) {
            mkdir($dir_descarga, 777, true);
        }
        foreach ($uuid as $uuid_individual){
            try{
                copy($dir_xml.$uuid_individual->uuid.".xml", $dir_descarga.$uuid_individual->uuid.".xml");
            }catch (\Exception $e){

            }

        }

        $path = "downloads/fiscal/descarga/";
        $nombre_zip = $path.date("Ymd_his").".zip";

        $zipper = new Zipper;
        $zipper->make(public_path($nombre_zip))
            ->add(public_path($dir_descarga));
        $zipper->close();

        Files::eliminaDirectorio($dir_descarga);

        if(file_exists(public_path($nombre_zip))){
            return response()->download(public_path($nombre_zip));
        } else {
            return response()->json(["mensaje"=>"No hay CFDI para la descarga "]);
        }
    }

    public function descargarComunicados($data)
    {
        if (isset($data['startDate'])) {
            $this->repository->where([['cfd_sat.fecha', '>=', $data['startDate']]]);
        }
        if (isset($data['endDate'])) {
            $this->repository->where([['cfd_sat.fecha', '<=', $data['endDate']]]);
        }
        if (isset($data['rfc_emisor'])) {
            $this->repository->where([['rfc_emisor', 'LIKE', '%' . $data['rfc_emisor'] . '%']]);
        }
        if (isset($data['emisor'])) {
            $proveedoresSAT = ProveedorSAT::query()->where([['razon_social', 'LIKE', '%' . $data['emisor'] . '%']])->get();
            $arreglo_proveedor = [];
            foreach ($proveedoresSAT as $e) {
                $arreglo_proveedor[] = $e->id;
            }
            $this->repository->whereIn(['id_proveedor_sat', $arreglo_proveedor]);
        }
        if (isset($data['rfc_receptor'])) {
            $this->repository->where([['rfc_receptor', 'LIKE', '%' . $data['rfc_receptor'] . '%']]);
        }
        if (isset($data['receptor'])) {
            $empresasSAT = EmpresaSAT::query()->where([['razon_social', 'LIKE', '%' . $data['receptor'] . '%']])->get();
            $arreglo_empresa = [];
            foreach ($empresasSAT as $es) {
                $arreglo_empresa[] = $es->id;
            }
            $this->repository->whereIn(['id_empresa_sat', $arreglo_empresa]);
        }
        if (isset($data['uuid'])) {
            $this->repository->where([['cfd_sat.uuid', 'LIKE', '%' . $data['uuid'] . '%']]);
        }
        if (isset($data['moneda'])) {
            $this->repository->where([['moneda', 'LIKE', '%' . $data['moneda'] . '%']]);
        }
        if (isset($data['total'])) {
            $this->repository->where([['total', '=', $data['total'] ]]);
        }
        if (isset($data['tipo_cambio'])) {
            $this->repository->where([['tipo_cambio', '=', $data['tipo_cambio'] ]]);
        }
        if (isset($data['subtotal'])) {
            $this->repository->where([['subtotal', '=', $data['subtotal'] ]]);
        }
        if (isset($data['descuento'])) {
            $this->repository->where([['descuento', '=', $data['descuento'] ]]);
        }
        if (isset($data['impuestos_retenidos'])) {
            $this->repository->where([['total_impuestos_retenidos', '=', $data['impuestos_retenidos'] ]]);
        }
        if (isset($data['impuestos_trasladados'])) {
            $this->repository->where([['total_impuestos_trasladados', '=', $data['impuestos_trasladados'] ]]);
        }
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['cfd_sat.fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        if (isset($data['tipo_comprobante'])) {
            $this->repository->where([['cfd_sat.tipo_comprobante', 'LIKE', '%' .$data['tipo_comprobante']. '%' ]]);
        }
        if (isset($data['serie'])) {
            $this->repository->where([['cfd_sat.serie', 'like', '' .$data['serie']. '' ]]);
        }
        if (isset($data['folio'])) {
            $this->repository->where([['cfd_sat.folio', 'like', '' .$data['folio']. '' ]]);
        }
        if (isset($data['estado'])) {
            if (strpos('CANCELADO', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 1]]);
            }
            else if (strpos('VIGENTE', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 0]]);
            }
        }
        if (isset($data['obra'])) {
            $obras = ConfiguracionObra::withoutGlobalScopes()->where([['nombre', 'LIKE', '%' . $data['obra'] . '%']])->get();

            $id_obra = [];
            $id_proyecto = [];
            foreach($obras as $obra){
                $id_obra[] = $obra->id_obra;
                $id_proyecto[] = $obra->id_proyecto;
            }

            $uuid = FacturaRepositorio::whereIn("id_obra", $id_obra)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }
        if (isset($data['base_datos'])) {
            $id_proyecto = Proyecto::where([['base_datos', 'LIKE', '%' . $data['base_datos'] . '%']])->pluck("id");

            $uuid = FacturaRepositorio::whereIn("id_proyecto", $id_proyecto)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }

        if (isset($data['solo_pendientes'])) {
            if($data['solo_pendientes']==="true"){
                $this->repository->whereDoesntHave("facturaRepositorio")->whereDoesntHave("polizaCFDI");
            }
        }

        if (isset($data['solo_asociados'])) {
            if($data['solo_asociados']==="true"){
                $this->repository->whereHas("facturaRepositorio");
            }
        }

        if (isset($data['solo_asociados_contabilidad'])) {
            if($data['solo_asociados_contabilidad']==="true"){
                $this->repository->whereHas("polizaCFDI");
            }
        }

        if (isset($data['base_datos_ctpq'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_bd", "pol_bd.uuid","=","cfd_sat.uuid")
                ->where([['pol_bd.base_datos_contpaq', 'like', '%' .$data['base_datos_ctpq']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['ejercicio'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_eje", "pol_eje.uuid","=","cfd_sat.uuid")
                ->where([['pol_eje.ejercicio', '=', $data['ejercicio'] ]])->select("cfd_sat.*");
        }
        if (isset($data['periodo'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_per", "pol_per.uuid","=","cfd_sat.uuid")
                ->where([['pol_per.periodo', '=', $data['periodo'] ]])->select("cfd_sat.*");
        }
        if (isset($data['tipo_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_tipo", "pol_tipo.uuid","=","cfd_sat.uuid")
                ->where([['pol_tipo.tipo', 'like', '%' .$data['tipo_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['folio_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_folio", "pol_folio.uuid","=","cfd_sat.uuid")
                ->where([['pol_folio.folio', 'like', '%' .$data['folio_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['fecha_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_fecha", "pol_fecha.uuid","=","cfd_sat.uuid")
                ->whereBetween( ['pol_fecha.fecha', [ request( 'fecha_poliza' )." 00:00:00",request( 'fecha_poliza' )." 23:59:59"]] )->select("cfd_sat.*");
        }


        $uuids =  $this->repository->all();
        $arr_comunicados = [];
        foreach ($uuids as $uuid)
        {
            $arr_comunicados[$uuid->rfc_emisor]["proveedor"] = $uuid->proveedor->razon_social;
            $arr_comunicados[$uuid->rfc_emisor]["receptores"][$uuid->rfc_receptor]["empresa"] = $uuid->empresa->razon_social;
            $arr_comunicados[$uuid->rfc_emisor]["receptores"][$uuid->rfc_receptor]["uuid"][] = $uuid;

        }

        $dir_descarga = "downloads/fiscal/descarga/comunicados/";
        if (!file_exists($dir_descarga) && !is_dir($dir_descarga)) {
            mkdir($dir_descarga, 777, true);
        }

        foreach ($arr_comunicados as $rfc=>$arr_comunicado) {
            $comunicado = new Comunicado($arr_comunicado);
            //return $comunicado->create();
            $comunicado->create()->Output("F", $dir_descarga.$rfc.".pdf",1);
        }

        $path = "downloads/fiscal/descarga/";
        $nombre_zip = $path.date("Ymd_his").".zip";

        $zipper = new Zipper;
        $zipper->make(public_path($nombre_zip))
            ->add(public_path($dir_descarga));
        $zipper->close();

        //Files::eliminaDirectorio($dir_descarga);

        if(file_exists(public_path($nombre_zip))){
            return response()->download(public_path($nombre_zip));
        } else {
            return response()->json(["mensaje"=>"No hay comunicados para la descarga "]);
        }
    }

    public function pdfCFDI($id)
    {
        $CFDI = $this->repository->show($id);
        try{
            $cfd = new CFD($CFDI->xml);
        } catch (\Exception $e){
            dd("No se cargo el CFDI");
        }

        $arreglo_cfd = $cfd->getArregloFactura();
        $pdf = new CFDI($arreglo_cfd);
        return $pdf;
    }

    public function cargaXMLProveedor(array $data)
    {
        $archivo_xml = $data["xml"];
        $cfd = new CFD($archivo_xml);
        $arreglo_cfd = $cfd->getArregloFactura();
        if(auth()->user()->usuario != $arreglo_cfd["emisor"]["rfc"]){
            abort(500, "El emisor de los CFDI no coincide con su usuario, favor de verificar");
        }
        $this->validaReceptor($arreglo_cfd);

        $arreglo_cfd["id_empresa_sat"] = $this->repository->getIdEmpresa($arreglo_cfd["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($arreglo_cfd["emisor"], $arreglo_cfd["id_empresa_sat"]);
        $arreglo_cfd["id_proveedor_sat"] = $proveedor["id_proveedor"];

        $exp = explode("base64,", $data["xml"]);
        $contenido_xml = base64_decode($exp[1]);
        $arreglo_cfd["contenido_xml"] = $contenido_xml;

        $this->validaTipoTransaccion($data["id_tipo_transaccion"], $arreglo_cfd["tipo_comprobante"]);

        $arreglo_cfd["id_tipo_transaccion"] = $data["id_tipo_transaccion"];
        $cfd->validaCFDI33($contenido_xml, $arreglo_cfd);

        $cfdi = $this->registraCFDI($arreglo_cfd);

        return $cfdi;
    }

    private function validaDisponibilidad($cfdi)
    {
        if($cfdi->id_solicitud_recepcion>0){
            if($cfdi->solicitudRecepcion->estado>=0){
                abort(500, "Este CFDI esta asociado a la solicitud de revisión con número de folio: ". $cfdi->solicitudRecepcion->numero_folio);
            }
        }
        $cfdi_repositorio = FacturaRepositorio::where("uuid","=",$cfdi->uuid)->whereNotNull("id_transaccion")->first();
        if($cfdi_repositorio)
        {
            $cfdi_repositorio->load("usuario");
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 4,
                        "id_factura_repositorio" => $cfdi_repositorio->id,
                        "mensaje" => 'CFDI utilizado previamente:
            Registró: ' . $cfdi_repositorio->usuario->nombre_completo . '
            BD: ' . $cfdi_repositorio->proyecto->base_datos . '
            Proyecto: ' . $cfdi_repositorio->obra . '
            Tipo Transacción: ' . $cfdi_repositorio->transaccion->tipo_transaccion_str . '
            Folio Transacción: ' . $cfdi_repositorio->transaccion->numero_folio . '
            Fecha Registro: '. $cfdi_repositorio->fecha_hora_registro_format . '
            UUID: ' . $cfdi->uuid . '
            Emisor: ' . $cfdi->proveedor->razon_social . '
            RFC Emisor: ' .  $cfdi->rfc_emisor
                    ]
                ));
                abort(403, 'CFDI utilizado previamente:
            Registró: ' . $cfdi_repositorio->usuario->nombre_completo . '
            BD: ' . $cfdi_repositorio->proyecto->base_datos . '
            Proyecto: ' . $cfdi_repositorio->obra . '
            Tipo Transacción: ' . $cfdi_repositorio->transaccion->tipo_transaccion_str . '
            Folio Transacción: ' . $cfdi_repositorio->transaccion->numero_folio . '
            Fecha Registro: '. $cfdi_repositorio->fecha_hora_registro_format . '
            UUID: ' . $cfdi->uuid . '
            Emisor: ' . $cfdi->proveedor->razon_social . '
            RFC Emisor: ' .  $cfdi->rfc_emisor);
            }
    }

    private function validaTipoTransaccion($id_tipo_transaccion, $tipo_comprobante_actual)
    {
        $tipo_transaccion = $this->repository->getTipoTransaccion($id_tipo_transaccion);
        $tipo_comprobante = $tipo_transaccion->tipo_comprobante;
        $tipoComprobanteActual = $this->repository->getTipoComprobante($tipo_comprobante_actual);
        if($tipo_comprobante != $tipo_comprobante_actual){
            abort(500, "El tipo de transacción seleccionada: '".$tipo_transaccion->descripcion."' requiere un CFDI tipo: '".$tipo_transaccion->tipoComprobante->descripcion."', el CFDI seleccionado es de tipo: '".$tipoComprobanteActual->descripcion."'");
        }
    }

    private function validaReceptor($arreglo_cfd)
    {
        $rfc_receptoras = $this->repository->getRFCReceptoras();
        if (!in_array( $arreglo_cfd["receptor"]["rfc"], $rfc_receptoras)) {
            abort(500, "El RFC del receptor en el comprobante digital (" . $arreglo_cfd["receptor"]["rfc"] . ") no esta dado de alta en los registros de Hermes Infraestructura.");
        }
    }

    private function registraCFDI($arreglo_factura)
    {
        $contenido_archivo_xml = $arreglo_factura["contenido_xml"];
        if (key_exists("uuid", $arreglo_factura)) {
            $cfdi = $this->repository->validaExistencia($arreglo_factura["uuid"]);
            if (!$cfdi) {
                $arreglo_factura["xml_file"] = $this->repository->getArchivoSQL(base64_encode($contenido_archivo_xml));
                $cfdi = $this->store($arreglo_factura);
                if ($cfdi) {
                    Storage::disk('xml_sat')->put($arreglo_factura["uuid"].".xml", $contenido_archivo_xml);
                }
            } else {
                $this->validaDisponibilidad($cfdi);
                //$cfdi->complementarDatos($arreglo_factura);
                if(key_exists("id_tipo_transaccion", $arreglo_factura))
                {
                    if($cfdi->id_tipo_transaccion != $arreglo_factura["id_tipo_transaccion"])
                    {
                        $cfdi->id_tipo_transaccion = $arreglo_factura["id_tipo_transaccion"];
                        $cfdi->save();
                        $cfdi->eliminaDocumentos();
                        $cfdi->actualizaObligatoriedadDocumentos();
                    }
                }
            }

            $cfdi->generaDocumentos();
            $cfdi->load("archivos");

            return $cfdi;
        }
        return null;
    }

    public function obtenerInformeSATLP2020($data)
    {
        $fecha = New DateTime($data["fecha_inicial"]);
        $fecha_final = New DateTime($data["fecha_final"]);
        if(!($fecha_final>$fecha)){
            $fecha = New DateTime($data["fecha_final"]);
            $fecha_final = New DateTime($data["fecha_inicial"]);
        }
        $data["fecha_inicial"]=$fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data["fecha_final"]=$fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        return $this->repository->obtenerInformeSATLP2020($data);
    }

    public function obtenerInformeCostosCFDIvsCostosBalanza($data)
    {
        return $this->repository->obtenerInformeCostosCFDIvsCostosBalanza($data);
    }

    public function obtenerCuentasInformeSATLP2020($data)
    {

        $fecha = New DateTime($data["fecha_inicial"]);
        $fecha_final = New DateTime($data["fecha_final"]);
        if(!($fecha_final>$fecha)){
            $fecha = New DateTime($data["fecha_final"]);
            $fecha_final = New DateTime($data["fecha_inicial"]);
        }
        $data["fecha_inicial"]=$fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $data["fecha_final"]=$fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        return $this->repository->obtenerCuentasInformeSATLP2020($data);
    }

    public function obtenerMovimientosCuentasInformeSATLP2020($data)
    {

        $fecha = New DateTime($data["fecha_inicial"]);
        $fecha_final = New DateTime($data["fecha_final"]);
        if(!($fecha_final>$fecha)){
            $fecha = New DateTime($data["fecha_final"]);
            $fecha_final = New DateTime($data["fecha_inicial"]);
        }
        $data["fecha_inicial"]=$fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $data["fecha_final"]=$fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        return $this->repository->obtenerMovimientosCuentasInformeSATLP2020($data);
    }

    public function obtenerListaCFDI($data)
    {

        $fecha = New DateTime($data["fecha_inicial"]);
        $fecha_final = New DateTime($data["fecha_final"]);

        if(!($fecha_final>$fecha)){
            $fecha = New DateTime($data["fecha_final"]);
            $fecha_final = New DateTime($data["fecha_inicial"]);
        }

        $data["fecha_inicial"]=$fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data["fecha_final"]=$fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));

        $cfdiRepository = new CFDSATRepository(new CFDSAT());

        return $cfdiRepository->getListaCFDI($data);

    }

    public function obtenerListaCFDIMesAnio($data)
    {
        $cfdiRepository = new CFDSATRepository(new CFDSAT());
        return $cfdiRepository->obtenerListaCFDIMesAnio($data);
    }

    public function obtenerListaCFDICostosCFDIBalanza($data)
    {
        $cfdiRepository = new CFDSATRepository(new CFDSAT());
        return $cfdiRepository->obtenerListaCFDICostosCFDIBalanza($data);
    }

    public function obtenerNumeroEmpresa()
    {
        return $this->repository->obtenerNumeroEmpresa();
    }

    public function descargaExcel($data)
    {
        if (isset($data['startDate'])) {
            $this->repository->where([['cfd_sat.fecha', '>=', $data['startDate']]]);
        }
        if (isset($data['endDate'])) {
            $this->repository->where([['cfd_sat.fecha', '<=', $data['endDate']]]);
        }
        if (isset($data['rfc_emisor'])) {
            $this->repository->where([['rfc_emisor', 'LIKE', '%' . $data['rfc_emisor'] . '%']]);
        }
        if (isset($data['emisor'])) {
            $proveedoresSAT = ProveedorSAT::query()->where([['razon_social', 'LIKE', '%' . $data['emisor'] . '%']])->get();
            $arreglo_proveedor = [];
            foreach ($proveedoresSAT as $e) {
                $arreglo_proveedor[] = $e->id;
            }
            $this->repository->whereIn(['id_proveedor_sat', $arreglo_proveedor]);
        }
        if (isset($data['rfc_receptor'])) {
            $this->repository->where([['rfc_receptor', 'LIKE', '%' . $data['rfc_receptor'] . '%']]);
        }
        if (isset($data['receptor'])) {
            $empresasSAT = EmpresaSAT::query()->where([['razon_social', 'LIKE', '%' . $data['receptor'] . '%']])->get();
            $arreglo_empresa = [];
            foreach ($empresasSAT as $es) {
                $arreglo_empresa[] = $es->id;
            }
            $this->repository->whereIn(['id_empresa_sat', $arreglo_empresa]);
        }
        if (isset($data['uuid'])) {
            $this->repository->where([['cfd_sat.uuid', 'LIKE', '%' . $data['uuid'] . '%']]);
        }
        if (isset($data['moneda'])) {
            $this->repository->where([['moneda', 'LIKE', '%' . $data['moneda'] . '%']]);
        }
        if (isset($data['total'])) {
            $this->repository->where([['total', '=', $data['total'] ]]);
        }
        if (isset($data['tipo_cambio'])) {
            $this->repository->where([['tipo_cambio', '=', $data['tipo_cambio'] ]]);
        }
        if (isset($data['subtotal'])) {
            $this->repository->where([['subtotal', '=', $data['subtotal'] ]]);
        }
        if (isset($data['descuento'])) {
            $this->repository->where([['descuento', '=', $data['descuento'] ]]);
        }
        if (isset($data['impuestos_retenidos'])) {
            $this->repository->where([['total_impuestos_retenidos', '=', $data['impuestos_retenidos'] ]]);
        }
        if (isset($data['impuestos_trasladados'])) {
            $this->repository->where([['total_impuestos_trasladados', '=', $data['impuestos_trasladados'] ]]);
        }
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['cfd_sat.fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        if (isset($data['tipo_comprobante'])) {
            $this->repository->where([['cfd_sat.tipo_comprobante', 'LIKE', '%' .$data['tipo_comprobante']. '%' ]]);
        }
        if (isset($data['serie'])) {
            $this->repository->where([['cfd_sat.serie', 'like', '' .$data['serie']. '' ]]);
        }
        if (isset($data['folio'])) {
            $this->repository->where([['cfd_sat.folio', 'like', '' .$data['folio']. '' ]]);
        }
        if (isset($data['estado'])) {
            if (strpos('CANCELADO', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 1]]);
            }
            else if (strpos('VIGENTE', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 0]]);
            }
        }
        if (isset($data['obra'])) {
            $obras = ConfiguracionObra::withoutGlobalScopes()->where([['nombre', 'LIKE', '%' . $data['obra'] . '%']])->get();
            $id_obra = [];
            $id_proyecto = [];
            foreach($obras as $obra){
                $id_obra[] = $obra->id_obra;
                $id_proyecto[] = $obra->id_proyecto;
            }

            $uuid = FacturaRepositorio::whereIn("id_obra", $id_obra)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }
        if (isset($data['base_datos'])) {
            $id_proyecto = Proyecto::where([['base_datos', 'LIKE', '%' . $data['base_datos'] . '%']])->pluck("id");

            $uuid = FacturaRepositorio::whereIn("id_proyecto", $id_proyecto)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }

        if (isset($data['solo_pendientes'])) {
            if($data['solo_pendientes']==="true"){
                $this->repository->whereDoesntHave("facturaRepositorio")->whereDoesntHave("polizaCFDI");
            }
        }

        if (isset($data['solo_asociados'])) {
            if($data['solo_asociados']==="true"){
                $this->repository->whereHas("facturaRepositorio");
            }
        }

        if (isset($data['solo_asociados_contabilidad'])) {
            if($data['solo_asociados_contabilidad']==="true"){
                $this->repository->whereHas("polizaCFDI");
            }
        }

        if (isset($data['solo_no_asociados_contabilidad'])) {
            if($data['solo_no_asociados_contabilidad']==="true"){
                $this->repository->whereDoesntHave("polizaCFDI");
            }
        }

        if (isset($data['base_datos_ctpq'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_bd", "pol_bd.uuid","=","cfd_sat.uuid")
                ->where([['pol_bd.base_datos_contpaq', 'like', '%' .$data['base_datos_ctpq']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['ejercicio'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_eje", "pol_eje.uuid","=","cfd_sat.uuid")
                ->where([['pol_eje.ejercicio', '=', $data['ejercicio'] ]])->select("cfd_sat.*");
        }
        if (isset($data['periodo'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_per", "pol_per.uuid","=","cfd_sat.uuid")
                ->where([['pol_per.periodo', '=', $data['periodo'] ]])->select("cfd_sat.*");
        }
        if (isset($data['tipo_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_tipo", "pol_tipo.uuid","=","cfd_sat.uuid")
                ->where([['pol_tipo.tipo', 'like', '%' .$data['tipo_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['folio_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_folio", "pol_folio.uuid","=","cfd_sat.uuid")
                ->where([['pol_folio.folio', 'like', '%' .$data['folio_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['fecha_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_fecha", "pol_fecha.uuid","=","cfd_sat.uuid")
                ->whereBetween( ['pol_fecha.fecha', [ request( 'fecha_poliza' )." 00:00:00",request( 'fecha_poliza' )." 23:59:59"]] )->select("cfd_sat.*");
        }
        return Excel::download(new CFDILayout($this->repository->all()), 'cfdi_layout_'. date('Y-m-d H:i:s').'.xlsx');
    }

    public function descargaExcelCFDIRepPendiente($data)
    {
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;

        return Excel::download(new CFDIREPPendiente($data), 'cfdi_rep_pendiente_'. date('Y-m-d H:i:s').'.xlsx');
    }

    public function cargaXMLComprobacion(array $data)
    {
        $archivos_xml = json_decode($data["xmls"]);
        $nombres_archivo = json_decode($data["nombres_archivo"]);
        $conceptos = null;
        $i = 0;
        foreach ($archivos_xml as $archivo_xml){
            $cfd = new CFD($archivo_xml);
            $arreglo_cfd = $cfd->getArregloFactura();

            $this->validaReceptorContexto($arreglo_cfd, $nombres_archivo[$i]);

            $arreglo_cfd["id_empresa_sat"] = $this->repository->getIdEmpresa($arreglo_cfd["receptor"]);
            $proveedor = $this->repository->getProveedorSAT($arreglo_cfd["emisor"], $arreglo_cfd["id_empresa_sat"]);
            $arreglo_cfd["id_proveedor_sat"] = $proveedor["id_proveedor"];

            $exp = explode("base64,", $archivo_xml);
            $contenido_xml = base64_decode($exp[1]);
            $arreglo_cfd["contenido_xml"] = $contenido_xml;
            $cfd->validaCFDI33($contenido_xml, $arreglo_cfd);
            $cfdi = $this->registraCFDI($arreglo_cfd);
            $cfdi->conceptos->load("traslados");
            $cfdi->conceptos->load("cfdi");
            if($cfdi->tipo_comprobante == "I"){
                if($conceptos == null){
                    $conceptos = $cfdi->conceptos;
                }else{
                    $conceptos = $conceptos->merge($cfdi->conceptos);
                }
            }else {
                abort(400,"Los CFDI deben ser de tipo Ingreso, favor de verificar");
            }
            $i++;
        }
        return $conceptos;
    }

    private function validaReceptorContexto($arreglo_cfd, $nombre = null)
    {
        $rfc_obra = $this->repository->getRFCObra();
        if(key_exists("receptor",$arreglo_cfd))
        {
            if ($arreglo_cfd["receptor"]["rfc"] != $rfc_obra) {
                event(new IncidenciaCI(
                    [
                        "id_tipo_incidencia" => 6,
                        "rfc" => $arreglo_cfd["receptor"]["rfc"],
                    ]
                ));
                abort(500, "El RFC de la obra (" . $rfc_obra . ") no corresponde al RFC del receptor en el comprobante digital (" . $arreglo_cfd["receptor"]["rfc"] . ")");
            }
        }else{
            abort(500, "Error de lectura del archivo: ".$nombre);
        }
    }
    private function setDatosPago($factura_xml)
    {
        $pagos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Complemento//pago10:Pagos//pago10:Pago');
        $doctos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Complemento//pago10:Pagos//pago10:Pago//pago10:DoctoRelacionado');
        $monto = 0 ;
        if($pagos){
            foreach($pagos as $pago)
            {
                $monto += (float) $pago["Monto"];
                $moneda = (string) $pago["MonedaP"];
                $forma_pago = (string) $pago["FormaDePagoP"];
                $fecha_pago = $this->getFecha((string)$pago["FechaPago"]);
            }

            $this->arreglo_factura["total"] = $monto;
            $this->arreglo_factura["moneda"] = $moneda;
            $this->arreglo_factura["forma_pago"] = $forma_pago;
            $this->arreglo_factura["fecha_pago"] = $fecha_pago;
        }

        if($doctos){
            $id = 0;
            foreach($doctos as $docto)
            {
                $this->arreglo_factura["documentos_pagados"][$id]["uuid"] = (string)$docto["IdDocumento"];
                $this->arreglo_factura["documentos_pagados"][$id]["moneda"] = (string)$docto["MonedaDR"];
                $this->arreglo_factura["documentos_pagados"][$id]["imp_saldo_insoluto"] = (float)$docto["ImpSaldoInsoluto"];
                $this->arreglo_factura["documentos_pagados"][$id]["imp_pagado"] = (float)$docto["ImpPagado"];
                $this->arreglo_factura["documentos_pagados"][$id]["imp_saldo_ant"] = (float)$docto["ImpSaldoAnt"];
                $this->arreglo_factura["documentos_pagados"][$id]["num_parcialidad"] = (int)$docto["NumParcialidad"];
                $this->arreglo_factura["documentos_pagados"][$id]["metodo_pago"] = (string)$docto["MetodoDePagoDR"];
                $id++;
            }
        }
    }

    public function reprocesaCFDIComplementarConceptosTxt()
    {
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);

        $hoy_str = date('Y-m-d');
        $hace_1Y_str = date("Y-m-d",strtotime($hoy_str."- 1 years"));
        $hace_1Y = DateTime::createFromFormat('Y-m-d', $hace_1Y_str);

        $cantidad = CFDSAT::where("cancelado","=","0")
            ->whereNull("conceptos_txt")
            ->count();

        $take = 1000;

        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = CFDSAT::where("cancelado","=","0")
                ->whereNull("conceptos_txt")
                ->skip($i)
                ->take($take)
                ->orderBy("id","asc")
                ->get();

            $idistribucion = 0;
            foreach ($cfd as $rcfd) {
                ProcessComplementaConceptosTxtCFDI::dispatch($rcfd)
                    ->onQueue("q".$idistribucion);
                //$rcfd->complementarDatos();
                $idistribucion ++;
                if($idistribucion==5){
                    $idistribucion=0;
                }
            }
        }
    }

    public function reprocesaCFDIComplementarDatos()
    {

        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);

        $hoy_str = date('Y-m-d');
        $hace_1Y_str = date("Y-m-d",strtotime($hoy_str."- 1 years"));
        $hace_1Y = DateTime::createFromFormat('Y-m-d', $hace_1Y_str);

        $cantidad = CFDSAT::where("cancelado","=","0")
            /*->whereIn("tipo_comprobante",["I","E"])*/
            ->whereBetween("fecha",[$hace_1Y->format("Y-m-") . "01 00:00:00",$hoy_str." 23:59:59"])
            ->count();

        $take = 1000;

        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = CFDSAT::where("cancelado","=","0")
                /*->whereIn("tipo_comprobante",["I","E"])*/
                ->whereBetween("fecha",[$hace_1Y->format("Y-m-") . "01 00:00:00",$hoy_str." 23:59:59"])
                ->skip($i)
                ->take($take)
                ->orderBy("id","asc")
                ->get();

            $idistribucion = 0;
            foreach ($cfd as $rcfd) {
                ProcessComplementaDatosCFDI::dispatch($rcfd)->onQueue("q".$idistribucion);
                //$rcfd->complementarDatos();
                $idistribucion ++;
                if($idistribucion==5){
                    $idistribucion=0;
                }
            }
        }

    }

    public function detectarCancelaciones()
    {
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);

        $hoy_str = date('Y-m-d');
        $hace_1Y_str = date("Y-m-d",strtotime($hoy_str."- 1 years"));
        $hace_1Y = DateTime::createFromFormat('Y-m-d', $hace_1Y_str);

        $cantidad = CFDSAT::where("cancelado","=","0")
            /*->whereIn("tipo_comprobante",["I","E"])*/
            ->whereBetween("fecha",[$hace_1Y->format("Y-m-") . "01 00:00:00",$hoy_str." 23:59:59"])
            ->count();

        $take = 1000;

        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = CFDSAT::where("cancelado","=","0")
                /*->whereIn("tipo_comprobante",["I","E"])*/
                ->whereBetween("fecha",[$hace_1Y->format("Y-m-") . "01 00:00:00",$hoy_str." 23:59:59"])
                ->skip($i)
                ->take($take)
                ->orderBy("id","asc")
                ->get();

            $idistribucion = 0;
            foreach ($cfd as $rcfd) {
                ProcessCancelacionCFDI::dispatch($rcfd)->onQueue("q".$idistribucion);
                //$rcfd->validaVigencia();
                $idistribucion ++;
                if($idistribucion==5){
                    $idistribucion=0;
                }
            }
        }


        /*$cantidad = CFDSAT::where("id_empresa_sat","=",1)
            ->where("cancelado","=","0")
            ->whereIn("tipo_comprobante",["I","E"])
            ->whereBetween("fecha",["2021-01-01 00:00:00","2021-01-31 23:59:59"])
            ->count();

        $take = 1000;

        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = CFDSAT::where("id_empresa_sat","=",1)
                ->where("cancelado","=","0")
                ->whereIn("tipo_comprobante",["I","E"])
                ->whereBetween("fecha",["2021-01-01 00:00:00","2021-01-31 23:59:59"])
                ->skip($i)
                ->take($take)
                ->get();
            foreach ($cfd as $rcfd) {
                try{
                    $cfd = new CFD($rcfd->xml);
                } catch (\Exception $e){
                    $rcfd->no_verificable =  1;
                    $rcfd->save();
                }

                $vigente = $cfd->validaVigente();
                if(!$vigente)
                {
                    $rcfd->cancelado = 1;
                    $rcfd->fecha_cancelacion =  date('Y-m-d H:i:s');
                    $rcfd->save();
                } else{
                    $rcfd->ultima_verificacion =  date('Y-m-d H:i:s');
                    $rcfd->save();
                }
            }
        }*/
    }

    public function obtenerInformeREPProveedorPDF($data)
    {
        $informe = $this->obtenerInformeREPProveedor($data);
        $pdf = new InformeREPProveedor($informe);
        return $pdf->create();
    }

    public function obtenerInformeREPProveedorEmpresaPDF($data)
    {
        $informe = $this->obtenerInformeREPProveedorEmpresa($data);
        $pdf = new InformeREPProveedorEmpresa($informe);
        return $pdf->create();
    }

    public function obtenerInformeREPEmpresaProveedorPDF($data)
    {
        $informe = $this->obtenerInformeREPEmpresaProveedor($data);
        $pdf = new InformeREPEmpresaProveedor($informe);
        return $pdf->create();
    }

    public function obtenerInformeREPEmpresaPDF($data)
    {
        $informe = $this->obtenerInformeREPEmpresa($data);
        $pdf = new InformeREPEmpresa($informe);
        return $pdf->create();
    }

    public function obtenerInformeREPProveedor($data)
    {
        return $this->repository->getInformeREPProveedor($data);
    }

    public function obtenerInformeREPProveedorEmpresa($data)
    {
        return $this->repository->getInformeREPProveedorEmpresa($data);
    }

    public function obtenerInformeREPEmpresaProveedor($data)
    {
        return $this->repository->getInformeREPEmpresaProveedor($data);
    }

    public function obtenerInformeREPEmpresa($data)
    {
        return $this->repository->getInformeREPEmpresa($data);
    }
}
