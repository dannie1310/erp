<?php


namespace App\Repositories\SEGURIDAD_ERP\CtgEfos;

use App\Facades\Context;
use App\Informes\EFOSEmpresaInforme;
use App\Informes\EFOSEmpresaInformeCFDIDesglosado;
use App\Informes\EFOSEmpresaInformeDesglosado;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
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
        $fecha_informacion = '';
        if($ultimo_procesamiento){
            $fecha_informacion = $ultimo_procesamiento->fecha_informacion;
        }
        $procesamiento = ProcesamientoListaEfos::create([
            'fecha_actualizacion_sat_txt' => '',
            'hash_file'=>$file_fingerprint,
            'nombre_archivo'=> '',
            'fecha_informacion' => $fecha_informacion
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
}
