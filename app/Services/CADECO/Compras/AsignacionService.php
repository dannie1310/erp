<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Compras\Asignacion;
use App\PDF\AsignacionFormato;
use App\Repositories\Repository;

class AsignacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Asignacion $model)
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

        $conteos = $this->getCsvData($file);
        dd($conteos,'linea conteos');
//        $layout = LayoutConteo::query()->create();
//        $i=0;
//        $mensaje = "";
//        $mensaje_rechazos = [];
//
//        foreach ($conteos as $c){
//            $folio = $c['folio_marbete'];
//            $folio_marbete =  (int)substr(str_replace(' ', '', $c['folio_marbete']), 4, 6);
//            $c['folio_marbete'] = $folio_marbete;
//            $c['id_layout_conteo'] = $layout->id;
//
//            if(!is_numeric($c['tipo_conteo']) || !is_numeric($c['cantidad_nuevo']) || !is_numeric($c['total'])){
//                $i++;
//                array_push($mensaje_rechazos , " \n\nError en ".$folio.": \n - Error al ingresar cantidades");
//            }else{
//                $partidas_layout = LayoutConteoPartida::query()->create($c);
//                if($marbete = Marbete::query()->find($partidas_layout->id_marbete)){
//                    if($marbete->folio == $partidas_layout->folio_marbete){
//                        if($partidas_layout->tipo_conteo <= 3 && $partidas_layout->tipo_conteo > 0){
//                            if(count($marbete->conteos->where('tipo_conteo','=',$partidas_layout->tipo_conteo)) == 0){
//                                $c['id_layout_conteos_partida'] = $partidas_layout->id;
//                                $this->repository->create($c);
//                            }else{
//                                $i++;
//                                array_push($mensaje_rechazos , " \n\nError en ".$folio.": \n - Ya existe el registro de la partida");
//                            }
//                        }else{
//                            $i++;
//                            array_push($mensaje_rechazos , " \n\nError en ".$folio.": \n - El tipo de conteo no es valido");
//                        }
//                    }else{
//                        $i++;
//                        array_push($mensaje_rechazos ,  " \n\nError en ".$folio.": \n - El Folio del Marbete no es valido");
//                    }
//                }else{
//                    $i++;
//                    array_push($mensaje_rechazos , " \n\nError en ".$folio.": \n - Número de Marbete incorrecto");
//                }
//            }
//        }
//        $mensaje_rechazos = array_unique($mensaje_rechazos);
//        if($mensaje_rechazos != [])
//        {
//            $mensaje_fin = "";
//            foreach ($mensaje_rechazos as $mensaje_rechazo) {
//                $mensaje_fin = $mensaje_fin . $mensaje_rechazo;
//            }
//            $mensaje = $mensaje.$mensaje_fin;
//        }
//
//        if($mensaje != "")
//        {
//            return $mensaje;
//        }
//        return [];
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
