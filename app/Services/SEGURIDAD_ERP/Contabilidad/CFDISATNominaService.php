<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;


use App\Jobs\ProcessCancelacionCFDI;

use App\Models\CONTROL_RECURSOS\ModuloUsuarioSerie;
use App\Models\IGH\Usuario;
use App\Models\IGH\VwUsuario;
use App\Models\MODULOSSAO\ControlRemesas\PerfilUsuario;
use App\Models\MODULOSSAO\Seguridad\PerfilUsuarioAplicacion;
use App\Models\MODULOSSAO\Seguridad\UsuarioAplicacion;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNomina;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNominaReceptor;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\SEGURIDAD_ERP\Rol;
use App\Models\SEGURIDAD_ERP\RoleUser;
use App\Models\SEGURIDAD_ERP\RoleUserGlobal;
use App\Models\SEGURIDAD_ERP\VwUsuarioIntranetGxcRel;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDISATNominaRepository;
use App\Utils\CFDINomina;
use DateTime;
use DateTimeZone;
use App\Utils\Files;
use App\Events\CambioEFOS;
use Chumper\Zipper\Zipper;
use App\Events\FinalizaCargaCFD;
use Illuminate\Support\Facades\Storage;

class CFDISATNominaService
{
    /**
     * @var CFDISATNominaRepository
     */
    protected $repository;
    protected $log;
    protected $carga;

    public function __construct(CFDISATNomina $model)
    {
        $this->repository = new CFDISATNominaRepository($model);
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

    public function llenaSolicitaGxCRel()
    {
        $cantidad= VwUsuarioIntranetGxcRel::all()
            ->count();

        $take = 1000;
        $ind = 1;
        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $usuarios = VwUsuarioIntranetGxcRel::
                skip($i)
                ->take($take)
                ->get();

            foreach ($usuarios as $usuario) {
                $ind++;

                $usuario_ex = explode(" ",$usuario->RazonSocial);
                $permutas = $this->permutar($usuario_ex);

                foreach ($permutas as $permuta)
                {


                    print_r($ind." ");
                    print_r(mb_strtoupper(trim($permuta)."\n"));
                    $receptor = CFDISATNominaReceptor::where("nombre","like","%".mb_strtoupper(trim($permuta)."%"))
                        ->first();
                    //print_r($usuario_intranet2);
                    if($receptor)
                    {
                        print_r($receptor->id.":".$usuario->IdUsuario);
                        $receptor->solicita_gxc_rel = 1;
                        $receptor->id_usuario_gxc_rel_scr = $usuario->IdUsuario;
                        $receptor->save();
                        break;
                        //dd(levenshtein($receptor->nombre,$usuario_intranet->Completo));
                    }

                }

            }
        }
    }

    public function llenaDatosAccesoSistemas()
    {
        $cantidad= CFDISATNominaReceptor::tieneDatosIntranet()
            ->count();


        $take = 1000;
        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $receptores = CFDISATNominaReceptor::tieneDatosIntranet()
                ->skip($i)
                ->take($take)
                ->get();

            foreach ($receptores as $receptor)
            {
                $receptor->acceso = 0;
                $receptor->save();

                $count = ModuloUsuarioSerie::where("idusuario","=",$receptor->id_usuario_intranet)
                    ->count();

                if($count>0)
                {
                    $receptor->acceso_scr = 1;
                    $receptor->acceso = 1;
                    $receptor->save();
                }else{
                    $receptor->acceso_scr = 0;
                    $receptor->save();
                }

                $roles_erp = RoleUser::where("user_id","=",$receptor->id_usuario_intranet)
                    ->get()
                    ->pluck("role_id")
                    ->toArray();

                $obras_erp = RoleUser::where("user_id","=",$receptor->id_usuario_intranet)
                    ->get()
                    ->pluck("id_proyecto","id_obra")
                    ->toArray();


                $roles_erp_global = RoleUserGlobal::where("user_id","=",$receptor->id_usuario_intranet)
                    ->get()
                    ->pluck("role_id")
                    ->toArray();

                $roles_erp_integrados = array_unique(array_merge($roles_erp_global, $roles_erp));


                if(count($roles_erp_integrados) > 0)
                {
                    $receptor->acceso_sao_erp = 1;
                    $receptor->acceso = 1;

                    $roles = Rol::whereIn("id", $roles_erp_integrados)
                    ->get()
                    ->pluck("display_name")
                    ->toArray();

                    $receptor->roles = implode("|",$roles);
                    $receptor->save();

                    if(count($obras_erp)>0)
                    {
                        $obras = [];
                        foreach ($obras_erp as $id_obra => $id_proyecto)
                        {
                            $proyecto = Proyecto::withoutGlobalScopes()->find($id_proyecto);
                            $obra = ConfiguracionObra::withoutGlobalScopes()->where("id_proyecto","=",$id_proyecto)
                            ->where("id_obra","=",$id_obra)->first();
                            if($obra)
                            {
                                $obras[] = $proyecto->base_datos . "->" . $obra->nombre;
                            }
                        }
                        //dd($obras);
                        $receptor->obras_sao = implode("|", $obras);
                        $receptor->save();
                    }
                }else{
                    $receptor->roles = null;
                    $receptor->acceso_sao_erp = 0;
                    $receptor->save();
                }

                $usuario_ghi_app = \App\Models\MODULOSSAO\Seguridad\Usuario::where("Usuario","=",$receptor->usuario_intranet)
                ->first();

                if($usuario_ghi_app)
                {
                    $count_modulos_sao = PerfilUsuarioAplicacion::where("IDUsuario","=",$usuario_ghi_app->IDUsuario)
                        ->where("IDAplicacion","=",1)
                        ->count();

                    if($count_modulos_sao>0)
                    {
                        $receptor->acceso_modulos_sao = 1;
                        $receptor->acceso = 1;
                        $receptor->save();
                    }else{
                        $receptor->acceso_modulos_sao = 0;
                        $receptor->save();
                    }

                    $count_remesa = PerfilUsuario::where("IDUsuario","=",$usuario_ghi_app->IDUsuario)
                        ->count();

                    if($count_remesa>0)
                    {
                        $receptor->acceso_remesa = 1;
                        $receptor->acceso = 1;
                        $receptor->save();
                    }else{
                        $receptor->acceso_remesa = 0;
                        $receptor->save();
                    }

                    $count_nomina = UsuarioAplicacion::where("IDUsuario","=",$usuario_ghi_app->IDUsuario)
                        ->where("IDAplicacion","=",2)
                        ->count();

                    if($count_nomina>0)
                    {
                        $receptor->acceso_nomina = 1;
                        $receptor->acceso = 1;
                        $receptor->save();
                    }else{
                        $receptor->acceso_nomina = 0;
                        $receptor->save();
                    }
                }
            }
        }
    }

    public function llenaDatosIntranet()
    {

        $cantidad= CFDISATNominaReceptor::pendienteDatosIntranet()
            ->count();

        $take = 1000;
        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $receptores = CFDISATNominaReceptor::pendienteDatosIntranet()
                ->skip($i)
                ->take($take)
                ->get();
            $ind=0;

            foreach ($receptores as $receptor) {
                $usuario_intranet = Usuario::where("rfc","=",$receptor->rfc)
                    ->whereNotNull("usuario")
                ->first();

                if($usuario_intranet){
                    $receptor->id_usuario_intranet = $usuario_intranet->idusuario;
                    $receptor->usuario_intranet = $usuario_intranet->usuario;
                    $receptor->save();
                }else{

                    $receptor_ex = explode(" ",$receptor->nombre);
                    $permutas = $this->permutar($receptor_ex);

                    foreach ($permutas as $permuta)
                    {
                        $ind++;

                        print_r($ind." ");
                        print_r(mb_strtoupper(trim($permuta)."\n"));
                        $usuario_intranet2 = VwUsuario::where("Completo","like","%".mb_strtoupper(trim($permuta)."%"))
                            ->whereNotNull("Descripcion")
                            ->first();
                        //print_r($usuario_intranet2);
                        if($usuario_intranet2)
                        {
                            print_r('ui: '.$usuario_intranet2->IdUsuario);
                            $receptor->id_usuario_intranet = $usuario_intranet2->IdUsuario;
                            $receptor->usuario_intranet = $usuario_intranet2->Descripcion;
                            $receptor->save();
                            break;

                            //dd(levenshtein($receptor->nombre,$usuario_intranet->Completo));
                        }

                    }
                }
            }
        }
    }


    //1.- meter nss y curp
    public function reprocesaLlenadoReceptoresNominas()
    {

        $cantidad= CFDISATNominaReceptor::pendienteDatos()->count();

        $take = 1000;
        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $receptores = CFDISATNominaReceptor::pendienteDatos()
                ->skip($i)
                ->take($take)
                ->get();

            foreach ($receptores as $receptor) {
                try{
                    $rcfd = CFDISATNomina::where("rfc_receptor","=",$receptor->rfc)
                        ->orderBy("fecha","desc")
                    ->first();

                    $cfd_util = new CFDINomina(base64_decode($rcfd->xml_file));
                    $arreglo_cfd = $cfd_util->getArreglo();

                    try {
                        if(key_exists("receptor", $arreglo_cfd)){
                            if(key_exists("nss", $arreglo_cfd["receptor"])){
                                $receptor->nss = $arreglo_cfd["receptor"]["nss"];
                                $receptor->save();
                            }
                            if(key_exists("curp", $arreglo_cfd["receptor"])){
                                $receptor->curp = $arreglo_cfd["receptor"]["curp"];
                                $receptor->save();
                            }
                        }
                    }
                    catch (\Exception $e)
                    {
                        //dd('1',$e->getMessage());
                    }
                } catch (\Exception $e){
                    //dd('2',$e->getMessage());
                }

                /*if(key_exists("documentos_pagados",$arreglo_cfd)){
                    foreach($arreglo_cfd["documentos_pagados"] as $documento_pagado){
                        $cfdi_pagado = CFDSAT::where("uuid", $documento_pagado["uuid"])->first();
                        if($cfdi_pagado){
                            $documento_pagado["id_cfdi_pagado"] = $cfdi_pagado->id;
                        }
                        $rcfd->documentosPagados()->create($documento_pagado);
                    }
                }*/
            }
        }

    }

    //2.- meter registro patronal
    public function reprocesaLlenadoEmisorNominas()
    {

        $cantidad= CFDISATNomina::pendienteRegistroPatronal()->count();

        $take = 1000;
        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $nominas = CFDISATNomina::pendienteRegistroPatronal()
                ->skip($i)
                ->take($take)
                ->get();

            foreach ($nominas as $nomina) {
                try{

                    $cfd_util = new CFDINomina(base64_decode($nomina->xml_file));
                    $arreglo_cfd = $cfd_util->getArreglo();

                    try {
                        if(key_exists("emisor", $arreglo_cfd)){
                            if(key_exists("registro_patronal", $arreglo_cfd["emisor"])){
                                $nomina->registro_patronal = $arreglo_cfd["emisor"]["registro_patronal"];
                                $nomina->save();
                            }
                        }
                    }
                    catch (\Exception $e)
                    {
                        //dd('1',$e->getMessage());
                    }
                } catch (\Exception $e){
                    //dd('2',$e->getMessage());
                }

                /*if(key_exists("documentos_pagados",$arreglo_cfd)){
                    foreach($arreglo_cfd["documentos_pagados"] as $documento_pagado){
                        $cfdi_pagado = CFDSAT::where("uuid", $documento_pagado["uuid"])->first();
                        if($cfdi_pagado){
                            $documento_pagado["id_cfdi_pagado"] = $cfdi_pagado->id;
                        }
                        $rcfd->documentosPagados()->create($documento_pagado);
                    }
                }*/
            }
        }

    }

    public function procesaDirectorioZIPCFDI()
    {

        ini_set('max_execution_time', '7200');
        $this->carga = $this->repository->iniciaCarga("inicial");

        $path = "uploads/contabilidad/zip_cfdi_nomina/";
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

        if (count($contenido) <= 2 && $path != "uploads/contabilidad/zip_cfdi_nomina/") {
            closedir($dir);
            rmdir($path);
        }
    }

    private function procesaArchivoCFDI($ruta_archivo, $current)
    {
        $contenido_archivo_xml = file_get_contents($ruta_archivo);
        $util_cfdi = new CFDINomina($ruta_archivo);
        $arreglo_factura = $util_cfdi->getArreglo();
        $arreglo_factura = $this->complementaArreglo($arreglo_factura);

        if (key_exists("uuid", $arreglo_factura) && $arreglo_factura["tipo_comprobante"] == "N") {
            if (!$this->repository->validaExistencia($arreglo_factura["uuid"])) {
                if ($arreglo_factura["id_emisor"] > 0) {
                    $arreglo_factura["xml_file"] = $this->repository->getArchivoSQL(base64_encode($contenido_archivo_xml));
                    if ($this->repository->registrar($arreglo_factura)) {
                        unlink($ruta_archivo);
                        $this->log["archivos_cargados"] += 1;
                        $this->log["cfd_cargados"] += 1;
                    }
                } else {
                    $this->log["cfd_no_cargados"] += 1;
                    $this->log["archivos_no_cargados"] += 1;
                    $this->log["archivos_receptor_no_valido"] += 1;
                    $this->log["receptores_no_validos"][] = $arreglo_factura["id_emisor"];
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

    private function complementaArreglo($arreglo_factura)
    {
        $arreglo_factura["id_carga_zip_cfdi"] = $this->carga->id;
        $arreglo_factura["id_emisor"] = $this->repository->getIdEmisor($arreglo_factura["emisor"]);
        if($arreglo_factura["id_emisor"] >0 )
        {
            $arreglo_factura["id_receptor"] = $this->repository->getIdReceptor($arreglo_factura["receptor"]);
        }
        return $arreglo_factura;
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

        $cfdiRepository = new Repository(new Model());

        return $cfdiRepository->getListaCFDI($data);

    }

    public function obtenerListaCFDIMesAnio($data)
    {
        $cfdiRepository = new Repository(new Model());
        return $cfdiRepository->obtenerListaCFDIMesAnio($data);
    }

    public function obtenerListaCFDICostosCFDIBalanza($data)
    {
        $cfdiRepository = new Repository(new Model());
        return $cfdiRepository->obtenerListaCFDICostosCFDIBalanza($data);
    }

    public function detectarCancelaciones()
    {
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);

        $hoy_str = date('Y-m-d');
        $hace_1Y_str = date("Y-m-d",strtotime($hoy_str."- 1 years"));
        $hace_1Y = DateTime::createFromFormat('Y-m-d', $hace_1Y_str);

        $cantidad = Model::where("cancelado","=","0")
            /*->whereIn("tipo_comprobante",["I","E"])*/
            ->whereBetween("fecha",[$hace_1Y->format("Y-m-") . "01 00:00:00",$hoy_str." 23:59:59"])
            ->count();

        $take = 1000;

        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = Model::where("cancelado","=","0")
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
    }

    private function permutar($input){
        $miarray = array();
        $cadena="";
        //copio el array
        $temporal=$input;
        //borro el primer numero del array
        array_shift($input);
        //ahora la cuenta esta en que solo quedan 3
        for($u=0;$u<count($temporal);$u++){
            for($i=0;$i<count($input);$i++){
                array_push($input,$input[0]);
                array_shift($input);
                for($e=0;$e<count($input);$e++){
                    $cadena.=$input[$e]." ";
                }
                array_push($miarray,$temporal[$u]." ".$cadena);
                //array_push($miarray,$temporal[$u].strrev($cadena));
                $cadena="";

            }
            array_shift($input);
            array_push($input,$temporal[$u]);
        }
        return $miarray;
    }
}
