<?php
/**
 * Created by PhpStorm.
 * User: jlopeza
 * Date: 13/01/2020
 * Time: 06:23 PM
 */

namespace App\Repositories\CADECO\ProveedorContratista;

use App\Models\CADECO\ProveedorContratista;




class Repository extends \App\Repositories\Repository implements RepositoryInterface
{

    /**
     * @var Suministrados
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Suministrados $model
     */
    public function __construct(ProveedorContratista $model)
    {
        $this->model = $model;
    }

    public function update(array $data, $id)
    {
        $this->model->actualizar($data, $id);
        // $item->update($data);

        return $this->show($id);
    }
}