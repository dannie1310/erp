<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Factura;
use App\Models\CADECO\Finanzas\LayoutPago;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
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

    public function show($id)
    {
        return $this->repository->show($id);
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

    public function autorizar($id)
    {

        $layout = $this->repository->show($id);
        $partidas = $layout->partidas;

        foreach ($partidas as $partida) {

            $id_transaccion = $partida->id_transaccion;

            if (is_null($partida->id_transaccion_pago)) {


                $transaccion = Transaccion::query()->find($partida->id_transaccion);


                /*Facturas*/
                if ($transaccion->tipo_transaccion === '65') {

                    $factura = new Factura();
                    $pago = $factura->verificaOrdenPago($transaccion);


                        $layout = LayoutPagoPartida::query()->where('id_transaccion', '=', $partida->id_transaccion)
                            ->update(['id_transaccion_pago' => $pago->id_transaccion]);



                }


                /*Solicitud*/
                if ($transaccion->tipo_transaccion === '72') {

                    $solicitud = new Solicitud();
                    $pago = $solicitud->verificaPago($transaccion);



                        $layout = LayoutPagoPartida::query()->where('id_transaccion', '=', $id_transaccion)
                            ->update(['id_transaccion_pago' => $pago->id_transaccion]);

                }


            }


        }

        /*Se autoriza el Layout de Pago*/
        LayoutPago::query()->where('id', '=', $id)
            ->update(['id_usuario_autorizo' => auth()->id(),
                'fecha_hora_autorizado' => date('Y-m-d h:i:s'), 'estado' => 1]);


    }
}
