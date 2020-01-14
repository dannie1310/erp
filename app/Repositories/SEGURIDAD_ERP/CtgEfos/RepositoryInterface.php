<?php


namespace App\Repositories\SEGURIDAD_ERP\CtgEfos;


interface RepositoryInterface
{
    public function all();

    public function create(array $data);

    public function show($id);
}
