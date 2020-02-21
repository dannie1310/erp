<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:20 PM
 */

namespace App\Services\CADECO\Almacenes;

use App\Models\CADECO\NuevoLote;
use App\Repositories\CADECO\NuevoLote\Repository;

class NuevoLoteService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * NuevoLoteService constructor.
     * @param NuevoLote $model
     */
    public function __construct(NuevoLote $model)
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
                 if (!$material)
                {
                    $materiales[] = array(
                        'i' => 1,
                        'material' => '',
                        'numero_parte' => NULL,
                        'descripcion' => $partida['DESCRIPCION'],
                        'unidad' => $partida['UNIDAD'],
                        'cantidad' => $partida['CANTIDAD'],
                        'monto_total' => $partida['MONTO TOTAL'],
                        'monto_pagado' => ($partida['MONTO PAGADO'] > $partida['MONTO TOTAL']) ? NULL : $partida['MONTO PAGADO']
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
                        'cantidad' => $partida['CANTIDAD'],
                        'monto_total' => $partida['MONTO TOTAL'],
                        'monto_pagado' => ($partida['MONTO PAGADO'] > $partida['MONTO TOTAL']) ? NULL : $partida['MONTO PAGADO']
                    );
                }
            }else{
                $materiales[] = array(
                    'i' => 1,
                    'material' => '',
                    'numero_parte' => NULL,
                    'descripcion' => $partida['DESCRIPCION'],
                    'unidad' => $partida['UNIDAD'],
                    'cantidad' => $partida['CANTIDAD'],
                    'monto_total' => $partida['MONTO TOTAL'],
                    'monto_pagado' => ($partida['MONTO PAGADO'] > $partida['MONTO TOTAL']) ? NULL : $partida['MONTO PAGADO']
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
                if(count($renglon) != 7) {
                    abort(400,'No se puede procesar el ajuste de inventario');
                }else if(count($renglon) == 7 && $renglon[1] != '' || $renglon[2] != ''){
                    
                    $content[] = array(
                        'PARTIDA' =>  ($renglon[0] == '') ? null : $renglon[0],
                        'ID MATERIAL' =>  ($renglon[1] == '') ? null : $renglon[1],
                        'DESCRIPCION' =>  ($renglon[2] == '') ? null : $renglon[2],
                        'UNIDAD' =>  ($renglon[3] == '') ? null : $renglon[3],
                        'CANTIDAD' =>  ($renglon[4] == '') ? null : $renglon[4],
                        'MONTO TOTAL' =>  ($renglon[5] == '') ? null : $renglon[5],
                        'MONTO PAGADO' => (substr($renglon[6],0,-2) == '') ? NULL : substr($renglon[6],0,-2)
                    );
                }
                $linea++;
            }
        }            
        return $content;
    }
}