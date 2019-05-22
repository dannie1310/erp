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
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        /*
         * NO PUEDE HABER DOS CAMPOS CON EL MISMO NOMBRE PORQUE NO PERMITE CAMBIAR DE PÁGINA
         * @todo
         * */
        $this->model = $model;

    }

    public function all($data = null)
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
            ->join('transacciones','transacciones.id_transaccion', 'solicitudes.id_fondo_garantia')
            ->join('SubcontratosFG.ctg_tipos_mov_sol','ctg_tipos_mov_sol.estado_resultante', 'solicitudes.estado')
            ->join('SubcontratosFG.ctg_tipos_solicitud', 'ctg_tipos_solicitud.id', 'solicitudes.id_tipo_solicitud')
            ->select([
                'solicitudes.*',
                'transacciones.numero_folio',
                'ctg_tipos_mov_sol.estado_resultante_desc',
                'ctg_tipos_solicitud.descripcion'
            ])
        ;

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
                     * @todo Implementarlo con eloquent with
                     * */
                }
                return $query->paginate($data['limit'], ['*'], 'page', ($data['offset'] / $data['limit']) + 1);
            } else {
                $query = $this->model;
                if ($data['sort']) {
                    $query = $query->orderBy('solicitudes.' . $data['sort'], $data['order']);
                }
                return $query->paginate($data['limit'], ['*'], 'page', ($data['offset'] / $data['limit']) + 1);
            }
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

    public function create(array $data)
    {
        return $this->model->create($data);
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

    public function show($id)
    {
        return $this->model->find($id);
    }

}