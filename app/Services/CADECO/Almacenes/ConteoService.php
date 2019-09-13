<?php


namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\Conteo;
use App\Models\CADECO\Inventarios\InventarioFisico;
use App\Models\CADECO\Inventarios\LayoutConteo;
use App\Models\CADECO\Inventarios\LayoutConteoPartida;
use App\Models\CADECO\Inventarios\Marbete;
use App\Repositories\Repository;

class ConteoService
{
    protected $repository;

    public function __construct(Conteo $model)
    {
        $this->repository = new Repository($model);
    }

    public function cargaLayout($file){
        $conteos = $this->getCsvData($file);
        $layout = LayoutConteo::query()->create();

        foreach ($conteos as $c){
            $folio_marbete =  (int)substr(str_replace(' ', '', $c['folio_marbete']), 4, 6);
            $c['folio_marbete'] = $folio_marbete;
            $c['id_layout_conteo'] = $layout->id;
            $partidas_layout = LayoutConteoPartida::query()->create($c);

            if($marbete = Marbete::query()->find($partidas_layout->id_marbete)){
                if($marbete->folio == $partidas_layout->folio_marbete){
                    if($partidas_layout->tipo_conteo <= 3 && $partidas_layout->tipo_conteo > 0){
                        if(count($marbete->conteos->where('tipo_conteo','=',$partidas_layout->tipo_conteo)) == 0){
                            $c['id_layout_conteos_partida'] = $partidas_layout->id;
                            $this->repository->create($c);
                        }
                    }
                }

            }
        }

    }
    public function getCsvData($file){
        $myfile = fopen($file, "r") or die("Unable to open file!");

        $content = array();
        $linea = 1;
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            if($linea == 1){
                $linea++;
            }else{
                if(count($renglon) != 9 && count($renglon) != 8) {
                    abort(400,'No se pueden procesar los conteos');
                }else if(count($renglon) == 9 || (count($renglon) == 8 && $renglon[0] != '' && $renglon[1] != '' && $renglon[2] != '' && $renglon[3] != '' && $renglon[4] != ''
                    && $renglon[5] != ''  && $renglon[6] != ''  && $renglon[7] != '')){
                    $content[] = array(
                        'folio_marbete' =>  $renglon[0],
                        'id_marbete' =>  $renglon[1],
                        'tipo_conteo' =>  $renglon[2],
                        'cantidad_usados' =>  $renglon[3],
                        'cantidad_nuevo' =>  $renglon[4],
                        'cantidad_inservible' =>  $renglon[5],
                        'total' =>  $renglon[6],
                        'iniciales' =>  $renglon[7],
                        'observaciones' =>  array_key_exists(8,$renglon)?$renglon[8]:null,
                    );
                }
                $linea++;
            }
        }
        fclose($myfile);
        return $content;

    }

}