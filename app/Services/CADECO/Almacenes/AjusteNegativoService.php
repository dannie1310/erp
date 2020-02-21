<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 11:55 AM
 */

namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\AjusteNegativo;
use App\Repositories\CADECO\AjusteNegativo\Repository;

class AjusteNegativoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AjusteNegativoService constructor.
     * @param AjusteNegativo $model
     */
    public function __construct(AjusteNegativo $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        $datos = [
            'id_almacen' => $data['id_almacen'],
            'referencia' => $data['referencia'],
            'observaciones' => $data['observaciones'],
            'fecha' => $data['fecha'],
            'items' =>  $data['items']
        ];

        return $this->repository->create($datos);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data'][0]);
    }

    public function cargaLayout($file)
    {
        $partidas = $this->getCsvData($file);
        foreach ($partidas as $partida)
        {
            if($partida['ID MATERIAL'] != null) {
                $material = $this->repository->busca($partida['ID MATERIAL']);
                 if ($material['numero_parte'] == null)
                {
                    $materiales[] = array(
                        'i' => 1,
                        'material' => '',
                        'numero_parte' => NULL,
                        'descripcion' => $partida['DESCRIPCION'],
                        'unidad' => $partida['UNIDAD'],
                        'cantidad' => $partida['CANTIDAD SUMAR']
                    );
                }
                else {
                    $materiales[] = array(
                        'i' => 2,
                        'material' => [
                            'id' => $material->id_material,
                            'label' => $material->numero_parte,
                            'numero_parte' => $material->numero_parte,
                            'descripcion' => $material->descripcion,
                            'unidad' => $material->unidad
                        ],
                        'id_material' => $material->id_material,
                        'cantidad' => $partida['CANTIDAD SUMAR']
                    );
                }
            }else{
                $materiales[] = array(
                    'i' => 1,
                    'material' => '',
                    'numero_parte' => NULL,
                    'descripcion' => $partida['DESCRIPCION'],
                    'unidad' => $partida['UNIDAD'],
                    'cantidad' => $partida['CANTIDAD SUMAR']
                );
            }
        }
        return $materiales;
    }

    public function getCsvData($file)
    {
        $myfile = fopen($file, "r") or die("Unable to open file!");
        $content = array();
        $linea = 1;
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            if($linea == 1){
                $linea++;
            }else{
                if(count($renglon) != 5) {
                    abort(400,'No se puede procesar el ajuste de inventario');
                }else if(count($renglon) == 5 && $renglon[1] != '' || $renglon[2] != ''){
                    $content[] = array(
                        'PARTIDA' =>  ($renglon[0] == '') ? null : $renglon[0],
                        'ID MATERIAL' =>  ($renglon[1] == '') ? null : $renglon[1],
                        'DESCRIPCION' =>  ($renglon[2] == '') ? null : $renglon[2],
                        'UNIDAD' =>  ($renglon[3] == '') ? null : $renglon[3],
                        'CANTIDAD SUMAR' => (substr($renglon[4],0,-2) == '') ? NULL : substr($renglon[4],0,-2)
                    );
                }
                $linea++;
            }
        }   
        return $content;
    }
}