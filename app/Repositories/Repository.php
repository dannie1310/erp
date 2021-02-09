<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 5/02/19
 * Time: 05:21 PM
 */

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

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

    public function all()
    {
        $this->search();
        $this->scope();
        $this->sort();
        $this->limit();

        return $this->model->get();
    }

    public function paginate()
    {
        $this->search();
        $this->scope();
        $this->sort();
        $query = $this->model;

        if (request('limit') && request('offset') != '') {
            return $query->paginate(request('limit'), ['*'], 'page', (request('offset') / request('limit')) + 1);
        }

        return $query->paginate(10);
    }

    public function update(array $data, $id)
    {
        $item = $this->show($id);
        $item->update($data);

        return $item;
    }

    public function with($relations)
    {
        $this->model = $this->model->with($relations);
    }

    public function scope()
    {
        if (request('scope')) {
            $scope = request('scope');

            if (is_string($scope)) {
                $scope = [$scope];
            }

            foreach ($scope as $s) {
                $explode = explode(':', $s);
                $fn = $explode[0];
                $params = isset($explode[1]) ? $explode[1] : null;
                $this->model = $this->model->$fn($params);
            }
        }
    }

    public function where($where)
    {
        $this->model = $this->model->where($where);
        return $this;
    }

    public function whereOr($where)
    {
        $this->model = $this->model->orWhere($where);
        return $this;
    }
    public function whereIn($where)
    {

        $this->model = $this->model->whereIn($where[0], $where[1]);
        return $this;
    }

    public function whereBetween($where)
    {
        $this->model = $this->model->whereBetween($where[0], $where[1]);
        return $this;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete(array $data, $id)
    {
        $this->model->destroy($id);
    }

    public function cancelar($id){
        return $this->model->cancelar($id);
    }

    public function show($id)
    {
        $this->scope();
        return $this->model->find($id);
    }

    public function search()
    {
        if (request()->has('search'))
        {
            $this->model = $this->model->where(function($query) {
                foreach ($this->model->searchable as $col)
                {
                    $explode = explode('.', $col);

                    if (isset($explode[1])) {
                        $query->orWhereHas($explode[0], function ($q) use ($explode) {
                            if (isset($explode[2])) {
                                return $q->whereHas($explode[1], function ($q2) use ($explode) {
                                    return $q2->where($explode[2], 'LIKE', '%' . request('search') . '%');
                                });
                            } else {
                                return $q->where($explode[1], 'LIKE', '%' . request('search') . '%');
                            }
                        });
                    } else {
                        $query->orWhere($col, 'LIKE', '%' . request('search') . '%');
                    }
                }
            });
        }
    }

    private function limit()
    {
        if (request()->has('limit')) {
            $this->model = $this->model->limit(request('limit'));
        }
    }

    public function sort()
    {
        if (request('sort')) {
            $this->model = $this->model->orderBy(request('sort'), request('order'));
        }
    }

    public function withoutGlobalScopes()
    {
        $this->model = $this->model->withoutGlobalScopes();
        return $this;
    }

    public function join($tabla, $campo1, $signo, $campo2)
    {
        $this->model = $this->model->join($tabla,$campo1,$signo,$campo2);
        return $this;
    }

    public function leftJoin($tabla, $campo1, $signo, $campo2)
    {
        $this->model = $this->model->leftJoin($tabla,$campo1,$signo,$campo2);
        return $this;
    }

    public function whereNull($campo)
    {
        $this->model = $this->model->whereNull($campo);
        return $this;
    }
    public function whereNotNull($campo)
    {
        $this->model = $this->model->whereNotNull($campo);
        return $this;
    }

    public function whereHas($relacion)
    {
        $this->model = $this->model->whereHas($relacion);
        return $this;
    }

    public function whereDoesntHave($relacion)
    {
        $this->model = $this->model->whereDoesntHave($relacion);
        return $this;
    }
}
