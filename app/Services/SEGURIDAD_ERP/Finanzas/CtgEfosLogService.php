<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;

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
        $conteos = $this->getCsvData($file);
        dd('Para para ');
        $layout = LayoutConteo::query()->create();
        $i=0;
        $mensaje = "";
        $mensaje_rechazos = [];

        foreach ($conteos as $c){
            $folio = $c['folio_marbete'];
            $folio_marbete =  (int)substr(str_replace(' ', '', $c['folio_marbete']), 4, 6);
            $c['folio_marbete'] = $folio_marbete;
            $c['id_layout_conteo'] = $layout->id;

            if(!is_numeric($c['tipo_conteo']) || !is_numeric($c['cantidad_nuevo']) || !is_numeric($c['total'])){
                $i++;
                array_push($mensaje_rechazos , " \n\nError en ".$folio.": \n - Error al ingresar cantidades");
            }else{
                $partidas_layout = LayoutConteoPartida::query()->create($c);
                if($marbete = Marbete::query()->find($partidas_layout->id_marbete)){
                    if($marbete->folio == $partidas_layout->folio_marbete){
                        if($partidas_layout->tipo_conteo <= 3 && $partidas_layout->tipo_conteo > 0){
                            if(count($marbete->conteos->where('tipo_conteo','=',$partidas_layout->tipo_conteo)) == 0){
                                $c['id_layout_conteos_partida'] = $partidas_layout->id;
                                $this->repository->create($c);
                            }else{
                                $i++;
                                array_push($mensaje_rechazos , " \n\nError en ".$folio.": \n - Ya existe el registro de la partida");
                            }
                        }else{
                            $i++;
                            array_push($mensaje_rechazos , " \n\nError en ".$folio.": \n - El tipo de conteo no es valido");
                        }
                    }else{
                        $i++;
                        array_push($mensaje_rechazos ,  " \n\nError en ".$folio.": \n - El Folio del Marbete no es valido");
                    }
                }else{
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$folio.": \n - Número de Marbete incorrecto");
                }
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
            return $mensaje;
        }
        return [];
    }

    public function getCsvData($file)
    {
        $myfile = fopen($file, "r") or die("Unable to open file!");
        $content = array();
        $linea = 1;
        $i=0;
        $mensaje = "";
        $mensaje_rechazos = [];
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            $renglon = explode(",",fgets($myfile));
            $renglon = explode(",",fgets($myfile));
            $renglon = explode(",",fgets($myfile));
//            $renglon = explode(",",fgets($myfile));
//            $renglon = explode(",",fgets($myfile));
//            $renglon = explode(",",fgets($myfile));
//            $renglon = explode(",",fgets($myfile));
//            $renglon = explode(",",fgets($myfile));
//            $renglon = explode(",",fgets($myfile));
//            $renglon = explode(",",fgets($myfile));
//            $renglon = explode(",",fgets($myfile));
            dd($renglon);
            if($linea == 1){
                $linea++;
            }else{
                if(count($renglon) != 8) {
                    abort(400,'No se puede procesar la Requisición');
                }else if(count($renglon) == 8 && substr($renglon[7],0,-2)   != ''){
                    if($renglon[1] == '')
                    {
                        $renglon[1] = null;
                    }
                    if($renglon[2] == '')
                    {
                        $renglon[2] = null;
                    }
                    if($renglon[3] == '')
                    {
                        $renglon[3] = null;
                    }
                    if($renglon[4] == '')
                    {
                        $renglon[4] = null;
                    }
                    $renglon[7] = substr($renglon[7],0,-2);

                    $content[] = array(
                        'PARTIDA' =>  $renglon[0],
                        'No PARTE' =>  $renglon[1],
                        'No PARTE EQUIVALENTE' =>  $renglon[2],
                        'PAGINA' =>  $renglon[3],
                        'REF.' =>  $renglon[4],
                        'UNIDAD' =>  $renglon[5],
                        'DESCRIPCION' =>  $renglon[6],
                        'CANTIDAD' =>  $renglon[7]
                    );
                }else
                {
                    if ($renglon[0] == ''){
                        $i++;
                        array_push($mensaje_rechazos , 'No cuenta Numero de Partida');
                    }
                    if ($renglon[5] == ''){
                        $i++;
                        array_push($mensaje_rechazos , 'El campo UNIDAD es obligatorio en la partida:'.$renglon[0]);
                    }
                    if ($renglon[6] == ''){
                        $i++;
                        array_push($mensaje_rechazos , 'El campo DESCRIPCIÓN es obligatorio en la partida:'.$renglon[0]);
                    }
                    if (substr($renglon[7],0,-2) == ''){
                        $i++;
                        array_push($mensaje_rechazos , 'El campo CANTIDAD es obligatorio en la partida:'.$renglon[0]);
                    }
                }
                $linea++;
            }
        }
        $mensaje_rechazos = array_unique($mensaje_rechazos);
        if($mensaje_rechazos != [])
        {
            $mensaje_fin = "";
            foreach ($mensaje_rechazos as $mensaje_rechazo) {
                $mensaje_fin = $mensaje_fin . $mensaje_rechazo.' --';
            }
            $mensaje = $mensaje.$mensaje_fin;
        }else{
            $mensaje = 'Listo';
        }
        $content[] = array('Mensaje' => $mensaje);
        return $content;
    }

}
