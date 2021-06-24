<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 5/02/19
 * Time: 05:28 PM
 */

namespace App\Repositories;


interface RepositoryInterface
{
    public function all();

    public function first();

    public function create(array $data);

    public function update(array $data, $id);

    public function delete(array $data, $id);

    public function show($id);
}
