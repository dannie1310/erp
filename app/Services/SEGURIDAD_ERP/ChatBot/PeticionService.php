<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 24/10/2019
 * Time: 06:15 PM
 */


namespace App\Services\SEGURIDAD_ERP\ChatBot;


use App\Models\SEGURIDAD_ERP\ChatBot\Peticion;
use App\Repositories\SEGURIDAD_ERP\ChatBot\PeticionRepository;

class PeticionService
{
    /**
     * @var PeticionRepository
     */
    protected $repository;

    /**
     * @param Peticion $model
     */
    public function __construct(Peticion $model)
    {
        $this->repository = new PeticionRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function getRespuesta($parametros)
    {

    }
}
