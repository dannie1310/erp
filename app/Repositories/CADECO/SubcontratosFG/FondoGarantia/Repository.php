<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2019
 * Time: 08:36 PM
 */

namespace App\Repositories\CADECO\SubcontratosFG\FondoGarantia;
use App\Models\CADECO\SubcontratosFG\FondoGarantia as Model;

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

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function show($id)
    {
        return $this->model->find($id);
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
}