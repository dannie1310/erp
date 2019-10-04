<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\LayoutPago;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\Transaccion;
use App\Repositories\CADECO\Finanzas\LayoutPago\Repository;


class CargaLayoutPagoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(LayoutPago $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function validarLayout($pagos)
    {
        return $this->repository->validarLayout($pagos);
    }

    public function store(array $data)
    {
        $datos = [
            'pagos' => $data['pagos'],
            'resumen' => $data['resumen'],
            'file_pagos' => $data['file_pagos'],
            'nombre_archivo' => $data['file_pagos_name']
        ];
       return $this->repository->create($datos);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function autorizar($data)
    {
        $layout= $this->repository->show($data);
        $partidas = $layout->partidas;

        foreach($partidas as $partida){

            if(is_null($partida->id_transaccion_pago)){


                $transaccion= Transaccion::query()->find($partida->id_transaccion);


                /*Facturas*/
                if($transaccion->tipo_transaccion==='65'){

                    $orden_pago = OrdenPago::query()->where('id_referente','=', $transaccion->id_transaccion)->get()->first();

//                    dd($orden_pago);

                }


                /*Solicitud*/
                if($transaccion->tipo_transaccion==='72'){


                    $solicitud = Solicitud::query()->where()


                }




            }else{
//                echo "pagado";
            }
        }




    }
}
