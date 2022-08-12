<?php


namespace App\Repositories\SEGURIDAD_ERP\CtgEfos;

use App\Facades\Context;
use App\Informes\EFOSEmpresaInforme;
use App\Informes\EFOSEmpresaInformeCFDIDesglosado;
use App\Informes\EFOSEmpresaInformeDesglosado;
use App\Informes\Fiscal\Chatbot\InformeDetalleUltimosCambiosEFOS;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOSCambio;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaEfos;
use Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;


    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(CtgEfos $model)
    {
        $this->model = $model;
    }

    public function carga($data)
    {
        $file_fingerprint = hash_file('md5', $data);
        $ultimo_procesamiento = ProcesamientoListaEfos::orderBy("id","desc")
            ->first();
        $fecha_actualizacion_sat_txt = '';
        if($ultimo_procesamiento){
            $fecha_actualizacion_sat_txt = $ultimo_procesamiento->fecha_actualizacion_sat_txt;
        }
        $procesamiento = ProcesamientoListaEfos::create([
            'fecha_actualizacion_sat_txt' => $fecha_actualizacion_sat_txt,
            'hash_file'=>$file_fingerprint,
            'nombre_archivo'=> '',
            'fecha_informacion' => ''
        ]);
        $logs = $this->model->reg($procesamiento, $data);

        if(count($logs) > 0)
        {
            foreach($logs as $log)
            {
                $procesamiento->logs()->create(
                    [
                        "log_procesamiento" =>$log["descripcion"],
                        "tipo" =>$log["tipo"]
                    ]
                );
            }
        } else {
            $procesamiento->logs()->create(
                [
                    "log_procesamiento" =>"Procesamiento Correcto",
                    "tipo" =>"2"
                ]
            );
        }
        return $procesamiento;
    }

    public function rfc($data)
    {
        return $this->model->api($data);
    }

    public function getInformeCFD()
    {
        $informe = EFOSEmpresaInforme::getInforme();
        return $informe;
    }
    public function getInformeDefinitivos()
    {
        $informe = EFOSEmpresaInforme::getInformeDefinitivos();
        return $informe;
    }
    public function getInformeCFDDesglosado()
    {
        $informe = EFOSEmpresaInformeDesglosado::getInforme();
        return $informe;
    }

    public function getInformeCFDIDesglosado()
    {
        $informe = EFOSEmpresaInformeCFDIDesglosado::getInforme();
        return $informe;
    }

    public function getCorreccionesPendientes()
    {

    }

    public function getUltimasListas()
    {
        $ultimas_listas = ProcesamientoListaEfos::where("nombre_archivo","!=","")->orderBy("id","desc")->take(5)->get();
        return $ultimas_listas;
    }

    public function getUltimosProcesamientosCambios()
    {
        $ultimos_id_procesamientos_cambios = EFOSCambio::where("estado","=",1)->orderBy("id_procesamiento_efos","desc")->distinct()->take(3)->pluck("id_procesamiento_efos")->toArray();
        $ultimos_procesamientos_cambios = ProcesamientoListaEfos::whereIn("id",$ultimos_id_procesamientos_cambios)->orderBy("id","desc")->get();
        return $ultimos_procesamientos_cambios;
    }

    public function getUltimosCambiosEFOSTXT()
    {
        $respuesta = "";
        $ultimos_procesamientos_cambios = $this->getUltimosProcesamientosCambios();

        $i = 0;
        foreach ($ultimos_procesamientos_cambios as $ultimo_procesamiento_cambio) {

            $respuesta .= "Fecha de Procesamiento de Lista de EFOS: ".$ultimo_procesamiento_cambio->fecha_hora_format;
            $respuesta .= "\nFecha de Actualización de Lista de EFOS: ".$ultimo_procesamiento_cambio->fecha_actualizacion_lista_efos."\n";

            $j = 1;
            $cambios_efos = $ultimo_procesamiento_cambio->cambios;
            foreach ($cambios_efos as $cambio_efos)
            {
                $respuesta .= "\n🚫 "."*_".$cambio_efos->efos->rfc."_*";
                $respuesta .= "\n       ".$cambio_efos->efos->razon_social;

                if($cambio_efos->estadoInicialObj){
                    $respuesta .= "\n       Estado Anterior: ".$cambio_efos->estadoInicialObj->descripcion;
                }else{
                    $respuesta .= "\n       Estado Anterior: Proveedor/Contratista";
                }

                $respuesta .= "\n       Estado Actualizado: ".$cambio_efos->estadoFinalObj->descripcion;

                if(in_array($cambio_efos->estadoFinalObj->descripcion, ["Presunto","Definitivo"]))
                {
                    $respuesta .= "\n\n       📑 Detalle de CFDI:";

                    $partidas = InformeDetalleUltimosCambiosEFOS::getPartidas($cambio_efos->efos->rfc);
                    if($partidas["pendientes"])
                    {
                        $respuesta .= "\n\n              🔴️ Estatus Pendiente de Corrección:";
                        foreach ($partidas["pendientes"] as $pendiente)
                        {
                            $respuesta .= "\n                     🏢 "."*_".$pendiente["empresa"]."_*";
                            $respuesta .= "\n                            ".$pendiente["no_CFDI"]." CFDIs";
                            $respuesta .= "\n                            ".$pendiente["importe_format"];
                        }
                    }

                    if($partidas["en_aclaracion"])
                    {
                        $respuesta .= "\n\n              🟡 Estatus En Aclaración:";
                        foreach ($partidas["en_aclaracion"] as $aclaracion)
                        {
                            $respuesta .= "\n                     🏢 "."*_".$aclaracion["empresa"]."_*";
                            $respuesta .= "\n                            ".$aclaracion["no_CFDI"]." CFDIs";
                            $respuesta .= "\n                            ".$aclaracion["importe_format"];
                        }
                    }

                    if($partidas["corregidos"])
                    {
                        $respuesta .= "\n\n              🟢 Estatus Corregido:";
                        foreach ($partidas["corregidos"] as $corregido)
                        {
                            $respuesta .= "\n                     🏢 "."*_".$corregido["empresa"]."_*";
                            $respuesta .= "\n                            ".$corregido["no_CFDI"]." CFDIs";
                            $respuesta .= "\n                            ".$corregido["importe_format"];
                        }
                    }

                    if($partidas["no_deducidos"])
                    {
                        $respuesta .= "\n\n              🟢 Estatus No Deducido:";
                        foreach ($partidas["no_deducidos"] as $no_deducido)
                        {
                            $respuesta .= "\n                     🏢 "."*_".$no_deducido["empresa"]."_*";
                            $respuesta .= "\n                            ".$no_deducido["no_CFDI"]." CFDIs";
                            $respuesta .= "\n                            ".$no_deducido["importe_format"];
                        }
                    }

                    if($partidas["presuntos"])
                    {
                        $respuesta .= "\n\n              🟠 Presuntos:";
                        foreach ($partidas["presuntos"] as $presunto)
                        {
                            $respuesta .= "\n                     🏢 "."*_".$presunto["empresa"]."_*";
                            $respuesta .= "\n                            ".$presunto["no_CFDI"]." CFDIs";
                            $respuesta .= "\n                            ".$presunto["importe_format"];
                        }
                    }

                    $total = InformeDetalleUltimosCambiosEFOS::getTotal($cambio_efos->efos->rfc);
                    $respuesta .= "\n\n"."*_Total:_*". " ".$total["importe_format"] . " (".$total["no_CFDI"]." CFDIs)";

                }

                if(count($cambios_efos)>$j)
                {
                    $respuesta .= "\n";
                }

                $j++;
            }
            if($i<count($ultimos_procesamientos_cambios)-1){
                $respuesta .= "\n_________________________________________\n\n";
            }
            $i++;
        }

        return $respuesta;

    }

    public function getUltimasListasEFOSTXT()
    {
        $respuesta = "";
        $ultimas_listas = $this->getUltimasListas();

        $i = 0;
        foreach ($ultimas_listas as $ultimas_lista)
        {
            $definitivos = $ultimas_lista->cambios()->where("estado_final","=",0)->get();
            $presuntos = $ultimas_lista->cambios()->where("estado_final","=",2)->get();
            $sentencias_favorable = $ultimas_lista->cambios()->where("estado_final","=",3)->get();
            $desvirtuados = $ultimas_lista->cambios()->where("estado_final","=",1)->get();

            $respuesta .= "Fecha Actualización Lista: ".$ultimas_lista->fecha_actualizacion_lista_efos;
            $respuesta .= "\nFecha Procesamiento Lista: ".$ultimas_lista->fecha_hora_format."\n";

            if(count($definitivos)>0)
            {
                $respuesta .= "\n🔴️ # EFOS Definitivos Detectados: ".count($definitivos) . "\n";
                $jd = 0;
                foreach ($definitivos as $definitivo)
                {
                    $respuesta .= "\n       "."*".$definitivo->efos->rfc."* ";
                    $respuesta .= "\n       " . $definitivo->efos->razon_social;

                    $total = InformeDetalleUltimosCambiosEFOS::getTotal($definitivo->efos->rfc);
                    $respuesta .= "\n       " . $total["no_CFDI"]." CFDIs";
                    $respuesta .= "\n       " .$total["importe_format"];

                    if($jd<count($definitivos)-1){
                        $respuesta .= "\n";
                    }

                    $jd++;
                }

            }

            if(count($presuntos)>0)
            {
                $respuesta .= "\n";

                $respuesta .= "\n🟠️ # EFOS Presuntos Detectados: ".count($presuntos)."\n";
                $jp = 0;
                foreach ($presuntos as $presunto)
                {
                    $respuesta .= "\n       "."*".$presunto->efos->rfc."* ";
                    $respuesta .= "\n       " . $presunto->efos->razon_social;

                    $total = InformeDetalleUltimosCambiosEFOS::getTotal($presunto->efos->rfc);
                    $respuesta .= "\n       " . $total["no_CFDI"]." CFDIs";
                    $respuesta .= "\n       " .$total["importe_format"];

                    if($jp<count($definitivos)-1){
                        $respuesta .= "\n";
                    }

                    $jp++;

                }

            }

            if(count($sentencias_favorable)>0)
            {
                $respuesta .= "\n";
                $respuesta .= "\n🟢 # Sentencias Favorables: ".count($sentencias_favorable)."\n";
                $jsf = 0;
                foreach ($sentencias_favorable as $sentencia_favorable)
                {
                    $respuesta .= "\n       "."*".$sentencia_favorable->efos->rfc."* ";
                    $respuesta .= "\n       " . $sentencia_favorable->efos->razon_social;

                    $total = InformeDetalleUltimosCambiosEFOS::getTotal($sentencia_favorable->efos->rfc);
                    $respuesta .= "\n       " . $total["no_CFDI"]." CFDIs ";
                    $respuesta .= "\n       " .$total["importe_format"];

                    if($jsf< count($sentencias_favorable)-1){
                        $respuesta .= "\n";
                    }

                    $jsf++;
                }

            }

            if(count($desvirtuados)>0)
            {
                $respuesta .= "\n";
                $respuesta .= "\n🟢 # Desvirtuados: ".count($desvirtuados)."\n";
                $jd = 0;
                foreach ($desvirtuados as $desvirtuado)
                {
                    $respuesta .= "\n       "."*".$desvirtuado->efos->rfc."* ";
                    $respuesta .= "\n       " . $desvirtuado->efos->razon_social;

                    $total = InformeDetalleUltimosCambiosEFOS::getTotal($desvirtuado->efos->rfc);
                    $respuesta .= "\n       " . $total["no_CFDI"]." CFDIs";
                    $respuesta .= "\n       " .$total["importe_format"];

                    if($jd<=count($desvirtuado)-1){
                        $respuesta .= "\n";
                    }

                    $jd++;
                }

            }

            if(count($presuntos)==0 && count($definitivos)==0 && count($desvirtuados)==0 && count($sentencias_favorable)==0)
            {
                $respuesta .= "\nSIN CAMBIOS DETECTADOS";
            }


            if($i<count($ultimas_listas)-1){
                $respuesta .= "\n_________________________________________\n\n";
            }
            $i++;
        }

        return $respuesta;
    }


    public function getCorreccionesPendientesTXT()
    {
        $respuesta = "";

        $pendientes_correccion = InformeDetalleUltimosCambiosEFOS::getEFOSPendientesCorreccion();

        $i = 0;
        foreach ($pendientes_correccion as $pendiente_correccion)
        {
            $respuesta .= "\n🚫 "."*_".$pendiente_correccion["rfc"]."_* "."\n       ".$pendiente_correccion["razon_social"];
            $respuesta .= "\n       📆 Fecha Definitivo: ".$pendiente_correccion["fecha_definitivo"]."\n";

            $pendientes = InformeDetalleUltimosCambiosEFOS::getPendientesCorreccion($pendiente_correccion["rfc"]);

            if($pendientes)
            {
                foreach ($pendientes as $pendiente)
                {
                    $respuesta .= "\n       🏢 "."*_".$pendiente["empresa"]."_*";
                    $respuesta .= "\n              ".$pendiente["no_CFDI"]." CFDIs";
                    $respuesta .= "\n              ".$pendiente["importe_format"];

                }
            }

            $respuesta .= "\n\n"."*_Total:_*". " ".$pendiente_correccion["importe_format"] . " (".$pendiente_correccion["no_CFDI"]." CFDIs)";


            if($i<=count($pendientes_correccion)-1){
                $respuesta .= "\n_________________________________________\n";
            }

            $i++;
        }

        return $respuesta;
    }

    public function getEFOSEnAclaracionTXT()
    {
        $respuesta = "";

        $aclaraciones = InformeDetalleUltimosCambiosEFOS::getEFOSEnAclaracion();

        $i = 0;
        foreach ($aclaraciones as $aclaracion)
        {
            $respuesta .= "\n🚫 "."*_".$aclaracion["rfc"]."_* "."\n       ".$aclaracion["razon_social"];

            $cfdis_aclaracion = InformeDetalleUltimosCambiosEFOS::getEnAclaracion($aclaracion["rfc"]);

            if($cfdis_aclaracion)
            {
                foreach ($cfdis_aclaracion as $cfdi_aclaracion)
                {
                    $respuesta .= "\n       🏢 "."*_".$cfdi_aclaracion["empresa"]."_*";
                    $respuesta .= "\n              ".$cfdi_aclaracion["no_CFDI"]." CFDIs";
                    $respuesta .= "\n              ".$cfdi_aclaracion["importe_format"];

                }
            }

            $respuesta .= "\n\n"."*_Total:_*". " ".$aclaracion["importe_format"] . " (".$aclaracion["no_CFDI"]." CFDIs)";

            if($i<=count($aclaraciones)-1){
                $respuesta .= "\n_________________________________________\n";
            }

            $i++;
        }

        return $respuesta;
    }

    public function getUltimasCorreccionesTXT()
    {
        $respuesta = "";

        $correcciones = InformeDetalleUltimosCambiosEFOS::getUltimosEFOSCorregidos();

        $i = 0;
        foreach ($correcciones as $correccion)
        {
            $respuesta .= "\n🟢 "."*_".$correccion["rfc"]."_* ";
            $respuesta .= "\n       ".$correccion["razon_social"];
            $respuesta .= "\n       🏢 ".$correccion["empresa"];



            $respuesta .= "\n\n"."*_Total:_*". " ".$correccion["importe_format"] . " (".$correccion["no_CFDI"]." CFDIs)";

            if($i<=count($correcciones)-1){
                $respuesta .= "\n_________________________________________\n";
            }

            $i++;
        }

        return $respuesta;
    }

}
