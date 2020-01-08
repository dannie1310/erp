<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;

use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfosLog;
use App\Repositories\Repository;

class CtgEfosLogService
{
    /**
     * @Var Repository
     */
    protected $repository;

    /**
     * CtgEfosLogService
     * @param CtgEfosLog $model
     */

    public function __construct(CtgEfosLog $model)
    {
        $this->repository = new Repository($model);
    }

    public function cargaLayout($file){
//        dd('Carga de layout en el servicio', 'Jorge', 10, $file);
        $efos = $this->getCsvData($file);
        $i=0;
        $mensaje = "";
        $mensaje_rechazos = [];

        foreach ($efos as $efo){

            $efo['razon_social'] = str_replace('"','',$efo['razon_social']);
            $efo['fecha_presunto'] = str_replace('/','-',$efo['fecha_presunto']);
            $efo['fecha_definitivo'] = str_replace('/','-',$efo['fecha_definitivo']);
            $efo['fecha_presunto'] = date("Y-d-m", strtotime($efo['fecha_presunto']));
            $efo['fecha_definitivo'] = date("Y-d-m", strtotime($efo['fecha_definitivo']));
//            $efos_layout = CtgEfos::query()->create($efo);
            dd('Para para ', $efo);
            }
    }

    public function getCsvData($file)
    {
        $myfile = fopen($file, "r") or die("Unable to open file!");
        $content = array();
        $linea = 1;
        $t = 2;
        $razon = '';
        $estado = array('Definitivo', 'Desvirtuado', 'Presunto', 'Sentencia Favorable', 'Situación del contribuyente');
        while($linea <= 20) {
            $renglon = explode(",",fgets($myfile));
            if($linea <= 3){
                $linea++;
            }else{
                if($renglon[1] == '')
                {
                    abort(400,'---Verificar RFC vacio No'.$renglon[0]);
                }
                if(substr_count($renglon[1], substr($renglon[1], 0,1)) > 6)
                {
                }else{
                    while (!in_array($renglon[$t], $estado))
                    {
                        $razon = $razon.$renglon[$t];
                        $t++;
                    }
                    if($renglon[$t + 2] == '' || strlen($razon) === 0)
                    {
                        abort(400,(($renglon[$t + 2] =='')? "--Verificar Fecha de Publicación de la página del  SAT \n":"")
                            .((strlen($razon) === 0)? "--Verificar Razon Social\n":"").'------- Registro '. $renglon[0].' -------');
                    }
                    $content[] = array(
                        'id' => $renglon[0],
                        'rfc' => $renglon[1],
                        'razon_social' => $razon,
                        'estado' => $renglon[$t],
                        'fecha_presunto' => $renglon[$t + 2],
                        'fecha_definitivo' => ($renglon[$t + 10] != '') ? $renglon[$t + 10] : null
                    );
                    $linea++;
                    $t = 2;
                    $razon = '';
                }
            }
        }
        return $content;
    }

}
