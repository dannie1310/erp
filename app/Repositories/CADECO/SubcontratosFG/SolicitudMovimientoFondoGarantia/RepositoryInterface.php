<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 15/02/2019
 * Time: 01:02 PM
 */

namespace App\Repositories\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;


interface RepositoryInterface
{
    public function all();

    public function paginate($data);

    public function create(array $data);

    public function cancelar(array $data, $id);

    public function autorizar(array $data, $id);

    public function rechazar(array $data, $id);

    public function revertirAutorizacion(array $data, $id);

    public function show($id);

}