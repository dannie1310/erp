<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 06:27 PM
 */

namespace App\Repositories\CADECO\Subcontratos\Subcontrato;


use App\Models\CADECO\Subcontrato;

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
    public function __construct(Subcontrato $model)
    {
        $this->model = $model;
    }

    public function all($data = null)
    {
        $this->search();
        $this->scope();
        $this->limit();

        return $this->model->get();
    }

    private function search()
    {
        if (request()->has('search')) {
            $this->model = $this->model->where(function ($query) {
                foreach ($this->model->searchable as $col) {
                    $explode = explode('.', $col);

                    if (isset($explode[1])) {
                        $query->orWhereHas($explode[0], function ($q) use ($explode) {
                            return $q->where($explode[1], 'LIKE', '%' . request('search') . '%');
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

    public function show($id)
    {
        $this->scope();
        return $this->model->find($id);
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

    public function sort()
    {
        if (request('sort')) {
            $this->model = $this->model->orderBy(request('sort'), request('order'));
        }
    }
}