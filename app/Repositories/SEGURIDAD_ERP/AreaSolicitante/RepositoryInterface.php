<?php


namespace App\Repositories\SEGURIDAD_ERP\AreaSolicitante;


interface RepositoryInterface
{
    public function all();

    public function create(array $data);

    public function show($id);
}
