<?php


namespace App\Repositories\CADECO\Material;


interface RepositoryInterface
{
    public function all();

    public function create(array $data);

    public function show($id);
}
