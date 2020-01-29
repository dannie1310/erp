<?php
/**
 * Created by PhpStorm.
 * User: jlopeza
 * Date: 13/01/2020
 * Time: 06:23 PM
 */

namespace App\Repositories\CADECO\Suministrados;

use App\Models\CADECO\Suministrados;



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
    public function __construct(Suministrados $model)
    {
        $this->model = $model;
    }

    public function delete(array $data, $id)
    {
        $this->model->eliminarSuministrado($data);
    }
}