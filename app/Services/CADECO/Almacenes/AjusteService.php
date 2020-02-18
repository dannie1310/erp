<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/09/2019
 * Time: 06:19 PM
 */

namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\Ajuste;
use App\Models\CADECO\Almacen;
use App\Models\CADECO\Material;
use App\Repositories\Repository;

class AjusteService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AjusteService constructor.
     * @param Ajuste $model
     */
    public function __construct(Ajuste $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $ajuste = $this->repository;


        if (isset($data['numero_folio'])) {
            $ajuste = $ajuste->where([['numero_folio', 'LIKE', '%' . $data['numero_folio'] . '%']]);
        }

        if (isset($data['fecha'])) {
            $ajuste = $ajuste->where( [['fecha', '=', request( 'fecha' )]] );
        }

        if (isset($data['id_almacen'])) {
            $almacen = Almacen::query()->where([['descripcion', 'LIKE', '%'.$data['id_almacen'].'%']])->get();
            foreach ($almacen as $a){
                $ajuste = $ajuste->whereOr([['id_almacen', '=', $a->id_almacen]]);
            }
        }

        if (isset($data['referencia'])) {
            $ajuste = $ajuste->where([['referencia', 'LIKE', '%' . $data['referencia'] . '%']]);
        }

        if (isset($data['observaciones'])) {
            $ajuste = $ajuste->where([['observaciones', 'LIKE', '%' . $data['observaciones'] . '%']]);
        }

        return $ajuste->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function cargaLayout($file)
    {
        $partidas = $this->getCsvData($file);
        foreach ($partidas as $partida)
        {
            if($partida['No PARTE'] != null) {
                $material = Material::query()->where('numero_parte', '=', $partida['No PARTE'])->get(['id_material','numero_parte','descripcion', 'unidad', 'FechaHoraRegistro'])->first();
                // dd($partida, $material['numero_parte']);
                 if ($material['numero_parte'] == null)
                {
                    $materiales[] = array(
                        'i' => 1,
                        'material' => '',
                        'numero_parte' => $partida['No PARTE'],
                        'descripcion' => $partida['DESCRIPCION'],
                        'unidad' => $partida['UNIDAD'],
                        'cantidad' => $partida['CANTIDAD'],
                        'monto_total' => $partida['MONTO TOTAL'],
                        'monto_pagado' => $partida['MONTO PAGADO']
                    );
                }
                else {
                    $materiales[] = array(
                        'i' => '',
                        'material' => [
                            'id' => $material->id_material,
                            'label' => $material->numero_parte,
                            'numero_parte' => $material->numero_parte,
                            'descripcion' => $material->descripcion,
                            'unidad' => $material->unidad
                        ],
                        'cantidad' => $partida['CANTIDAD'],
                        'monto_total' => $partida['MONTO TOTAL'],
                        'monto_pagado' => $partida['MONTO PAGADO'],
                    );
                }
            }else{
                $materiales[] = array(
                    'i' => 1,
                    'material' => '',
                    'numero_parte' => $partida['No PARTE'],
                    'descripcion' => $partida['DESCRIPCION'],
                    'unidad' => $partida['UNIDAD'],
                    'cantidad' => $partida['CANTIDAD'],
                    'monto_total' => $partida['MONTO TOTAL'],
                    'monto_pagado' => $partida['MONTO PAGADO']
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
        $i=0;
        $mensaje = "";
        $mensaje_rechazos = [];
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
                        'No PARTE' =>  ($renglon[1] == '') ? null : $renglon[1],
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
