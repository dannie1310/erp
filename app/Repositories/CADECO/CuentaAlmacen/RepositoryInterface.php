<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/05/2019
 * Time: 06:11 PM
 */

namespace App\Repositories\CADECO\CuentaAlmacen;


interface RepositoryInterface
{
    public function all();

    public function create(array $data);

    public function show($id);
}