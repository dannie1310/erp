<?php


namespace App\Repositories\SEGURIDAD_ERP\CtgEfos;

use App\Facades\Context;
use App\Informes\EFOSEmpresaInforme;
use App\Informes\EFOSEmpresaInformeCFDIDesglosado;
use App\Informes\EFOSEmpresaInformeDesglosado;
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

            $respuesta .= "\nFecha de Procesamiento de Lista de EFOS: ".$ultimo_procesamiento_cambio->fecha_hora_format;
            $respuesta .= "\nFecha de Actualización de Lista de EFOS: ".$ultimo_procesamiento_cambio->fecha_actualizacion_lista_efos."\n";
            //$respuesta .= "\n Id Procesamiento: ".$ultimo_procesamiento_cambio->id;

            foreach ($ultimo_procesamiento_cambio->cambios as $cambio_efos)
            {
                $respuesta .= "\n*_".$cambio_efos->efos->razon_social."_*";
                $respuesta .= "\n".$cambio_efos->efos->rfc;

                if($cambio_efos->estadoInicialObj){
                    $respuesta .= "\nEstado Inicial en Lista de EFOS: ".$cambio_efos->estadoInicialObj->descripcion;
                }else{
                    $respuesta .= "\nEstado Inicial: Proveedor/Contratista";
                }

                $respuesta .= "\nEstado Final Lista en Lista de EFOS: ".$cambio_efos->estadoFinalObj->descripcion;
            }
            if($i<count($ultimos_procesamientos_cambios)-1){
                $respuesta .= "\n__________________________________\n";
            }
            $i++;
        }

        return $respuesta;

    }
}
