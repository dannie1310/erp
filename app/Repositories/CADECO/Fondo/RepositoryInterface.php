<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/04/2019
 * Time: 03:31 PM
 */

namespace App\Repositories\CADECO\Fondo;


interface RepositoryInterface
{
  public function all();

    public function create(array $data);

    public function show($id);
}
