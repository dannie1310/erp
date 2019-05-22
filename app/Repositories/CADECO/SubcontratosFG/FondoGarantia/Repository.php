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

    /*public function all($data = null)
    {
        if (isset($data['scope'])) {
            $this->scope($data['scope']);
        }
        return $this->model->get();
    }*/
    public function all()
    {
        $this->search();
        $this->scope();
        $this->sort();
        $this->limit();

        return $this->model->get();
    }
    public function paginate($data)
    {
        $this->model = $this->model
            ->join('transacciones','transacciones.id_transaccion', 'fondos_garantia.id_subcontrato')
            ->join('empresas','transacciones.id_empresa', 'empresas.id_empresa')
            ->select([
                'fondos_garantia.id_subcontrato',
                'fondos_garantia.saldo',
                'transacciones.numero_folio',
                'transacciones.monto',
                'empresas.razon_social',
                'transacciones.referencia'
            ]);
        if (count($data)) {
            #validar si $data['sort'] viene con doble guión __
            $doble_guion = strpos($data['sort'], '__');
            if ($doble_guion !== false) {
                $data['sort'] = explode("__", $data['sort']);
                $query = $this->model;
                if ($data['sort']) {
                    $query = $query
                        ->orderBy($data['sort'][1], $data['order']);
                    /*
                     * @todo Implementarlo con eloquent
                     * */
                    /*$query = $query->with(['subcontrato'=> function($query) use ($sorteable, $data) {
                        if($sorteable[1] == 'numero_folio')
                        {
                            $query->orderBy('numero_folio',$data['order']);
                        }
                    }
                    ]);*/

                }
                return $query->paginate($data['limit'], ['*'], 'page', ($data['offset'] / $data['limit']) + 1);
            } else {
                $query = $this->model;
                if ($data['sort']) {
                    $query = $query->orderBy('fondos_garantia.' . $data['sort'], $data['order']);
                }
                return $query->paginate($data['limit'], ['*'], 'page', ($data['offset'] / $data['limit']) + 1);
            }
        }

        return $this->model->paginate(10);
    }

    public function with($relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
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

    public function ajustarSaldo(array $data, $id)
    {
        $item = $this->show($id);
        $item->ajustarSaldo($data);
        return $item;
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

}