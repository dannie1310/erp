<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 15/02/2019
 * Time: 01:07 PM
 */

namespace App\Repositories\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;

use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia as Model;


class Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Repository constructor.
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

    public function paginate($data)
    {
        if (count($data)) {
            $query = $this->model;
            if ($data['sort'])
                $query = $query->orderBy($data['sort'], $data['order']);
            return $query->paginate($data['limit'], ['*'], 'page', ($data['offset'] / $data['limit']) + 1);
        }

        return $this->model->paginate(10);
    }

    public function cancelar(array $data, $id)
    {
        $item = $this->show($id);
        $item->cancelar($data);

        return $item;
    }

    public function autorizar(array $data, $id)
    {
        $item = $this->show($id);
        $item->autorizar($data);

        return $item;
    }

    public function rechazar(array $data, $id)
    {
        $item = $this->show($id);
        $item->rechazar($data);

        return $item;
    }

    public function revertirAutorizacion(array $data, $id)
    {
        $item = $this->show($id);
        $item->revertirAutorizacion($data);

        return $item;
    }

    public function with($relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    public function scope($scope)
    {
        if (is_string($scope)) {
            $scope = func_get_args();
        }

        foreach ($scope as $s) {
            $explode = explode(':', $s);
            $fn = $explode[0];
            $params = isset($explode[1]) ? $explode[1] : null;
            $this->model = $this->model->$fn($params);
        }
        return $this;
    }

    public function where($where)
    {
        $this->model = $this->model->where($where);
        return $this;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }


    public function show($id)
    {
        return $this->model->find($id);
    }

}