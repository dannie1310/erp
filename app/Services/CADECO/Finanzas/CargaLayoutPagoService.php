<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\CSV\PagoLayout;
use App\Models\CADECO\Finanzas\LayoutPago;
use App\Repositories\CADECO\Finanzas\LayoutPago\Repository;
use Maatwebsite\Excel\Facades\Excel;


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

    public function autorizar($data)
    {
        dd($data);
        return "autorizar";
    }

    public function descargar_layout(){
        return Excel::download(new PagoLayout(), 'LayoutRegistroPagos.csv');
    }
}
