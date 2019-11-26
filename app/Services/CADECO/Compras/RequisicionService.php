<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Material;
use App\Models\CADECO\Requisicion;
use App\Repositories\CADECO\Compras\Requisicion\Repository;

class RequisicionService
{
    /**
     * @var Repository
     */
    protected $repsitory;

    /**
     * RequisicionService constructor.
     * @param Requisicion $model
     */
    public function __construct(Requisicion $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function cargaLayout($file)
    {
        $partidas = $this->getCsvData($file);
        $mensaje = array_pop($partidas);
        $materiales = array();
        foreach ($partidas as $partida)
        {
            if($partida['No PARTE'] != null) {
                $material = Material::query()->where('numero_parte', '=', $partida['No PARTE'])->get(['id_material','numero_parte','descripcion', 'unidad', 'FechaHoraRegistro'])->first();
                $materiales[] = array(
                    'i' => '',
                    'material' => [
                        'id' => $material->id_material,
                        'label' => $material->numero_parte,
                        'numero_parte' => $material->numero_parte,
                        'descripcion' => $material->descripcion,
                        'unidad' => $material->unidad
                    ],
                    'numero_parte' => '',
                    'no_parte_equi' => $partida['No PARTE EQUIVALENTE'],
                    'pag' => $partida['PAGINA'],
                    'descripcion' =>'',
                    'unidad' => '',
                    'ref' => $partida['REF.'],
                    'fecha' => date('Y-m-d'),
                    'cantidad' => $partida['CANTIDAD'],
                    'observaciones' => ''
                );
            }else{
                $materiales[] = array(
                    'i' => 1,
                    'material' => '',
                    'numero_parte' => '',
                    'no_parte_equi' => $partida['No PARTE EQUIVALENTE'],
                    'pag' => $partida['PAGINA'],
                    'descripcion' => $partida['DESCRIPCION'],
                    'unidad' => $partida['UNIDAD'],
                    'ref' => $partida['REF.'],
                    'fecha' => date('Y-m-d'),
                    'cantidad' => $partida['CANTIDAD'],
                    'observaciones' => ''
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
                if(count($renglon) != 8) {
                    abort(400,'No se puede procesar la Requisición');
                }else if(count($renglon) == 8 && $renglon[0] != '' && $renglon[5] != '' && $renglon[6] != '' && $renglon[7] != ''){
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

    public function store($data)
    {
        $datos = [
            'fecha' => $data['fecha'],
            'id_area_compradora' => $data['id_area_compradora'],
            'id_tipo' => $data['id_tipo'],
            'id_area_solicitante' => $data['id_area_solicitante'],
            'concepto' => $data['concepto'],
            'partidas' => $data['partidas'],
            'observaciones'=> $data['observaciones']
        ];
        return $this->repository->create($datos);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}
