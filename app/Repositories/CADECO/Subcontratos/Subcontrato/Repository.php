<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 06:27 PM
 */

namespace App\Repositories\CADECO\Subcontratos\Subcontrato;


class Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    public function all($data = null)
    {
        if (isset($data['scope'])) {
            $this->scope($data['scope']);
        }
        return $this->model->get();
    }

    public function allSubcontratosSinFondo($data = null)
    {
        if (isset($data['scope'])) {
            $this->scope($data['scope']);
        }
        return $this->model->sinFondoGarantia()->get();
    }
}