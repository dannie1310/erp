<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2019
 * Time: 08:35 PM
 */

namespace App\Repositories\CADECO\SubcontratosFG\FondoGarantia;


interface RepositoryInterface
{
    public function all();

    public function create(array $data);

    public function show($id);
}