<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 01/10/2019
 * Time: 07:15 PM
 */

namespace App\Repositories\CADECO\Finanzas\LayoutPago;


interface RepositoryInterface
{
    public function validarLayout($layout);
    public function getCSVData($file);
    public function resumenLayout($data);
    public function datosPago($data);
    public function validarEstatusPago($transaccion, $documento);
    public function validarRegistroPrevio($transaccion);
}