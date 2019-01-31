<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 12:27 PM
 */

namespace App\Traits;


trait RepositoryTrait
{
    public function all($data = null) {
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

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($data, $id) {
        $item = $this->find($id);

        $item->update($data);

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

    public function where($where) {
        $this->model = $this->model->where($where);
        return $this;
    }

    public function destroy($data = [], $id)
    {
        $item = $this->find($id);
        $item->delete();
    }
}