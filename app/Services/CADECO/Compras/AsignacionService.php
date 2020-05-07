<?php


namespace App\Services\CADECO\Compras;


use App\Repositories\Repository;
use App\PDF\Compras\AsignacionFormato;
use App\Models\CADECO\Compras\Asignacion;
use App\Models\CADECO\Compras\AsignacionProveedores;

class AsignacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(AsignacionProveedores $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function asignacion($id)
    {
        $pdf = new AsignacionFormato($id);
        return $pdf;
    }

    public function descargaLayout()
    {
        var_dump('Descarga de layout por asignacion services');
    }
    public function cargaLayout($file){

    }

    public function getCsvData($file){
        $myfile = fopen($file, "r") or die("Unable to open file!");
        $content = array();
        $linea = 1;
        $i=0;
        $mensaje = "";
        $mensaje_rechazos = [];
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            dd($renglon);
            if($linea == 1){
                $linea++;
            }else{
                if(count($renglon) != 9) {
                    dd($renglon);
                    abort(400,'No se pueden procesar los conteos');
                }else if(count($renglon) == 9 && $renglon[0] != '' && $renglon[1] != '' && $renglon[2] != '' && $renglon[4] != '' && $renglon[6] != ''){
                    if($renglon[3] == ''){
                        $renglon[3] = null;
                    }if($renglon[5] == ''){
                        $renglon[5] = null;
                    }if($renglon[7] == ''){
                        $renglon[7] = null;
                    }if($renglon[8] == '' || $renglon[8] == "\r\n"){
                        $renglon[8] = null;
                    }
                    $content[] = array(
                        'folio_marbete' =>  $renglon[0],
                        'id_marbete' =>  $renglon[1],
                        'tipo_conteo' =>  $renglon[2],
                        'cantidad_usados' =>  $renglon[3],
                        'cantidad_nuevo' =>  $renglon[4],
                        'cantidad_inservible' =>  $renglon[5],
                        'total' =>  $renglon[6],
                        'iniciales' =>  $renglon[7],
                        'observaciones' =>  $renglon[8],
                    );
                }else if ($renglon[1] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - Id de Marbete incorrecto");
                }else if ($renglon[2] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Conteo es obligatorio");
                }else if ($renglon[4] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Nuevos es obligatorio");
                }else if ($renglon[6] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Total es obligatorio");
                }
                $linea++;
            }
        }
        $mensaje_rechazos = array_unique($mensaje_rechazos);
        if($mensaje_rechazos != [])
        {
            $mensaje_fin = "";
            foreach ($mensaje_rechazos as $mensaje_rechazo) {
                $mensaje_fin = $mensaje_fin . $mensaje_rechazo;
            }
            $mensaje = $mensaje.$mensaje_fin;
        }

        if($mensaje != "")
        {
            abort(400,'No se realizó la carga de conteos debido a los siguientes errores:'.$mensaje);
        }
        fclose($myfile);
        return $content;

    }
}
