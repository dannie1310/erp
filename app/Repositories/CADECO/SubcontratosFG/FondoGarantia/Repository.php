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
        $this->model = $model::select([
            'fondos_garantia.id_subcontrato',
            'fondos_garantia.saldo',
            'transacciones.numero_folio',
            'transacciones.monto',
            'empresas.razon_social',
            'transacciones.referencia'
        ])
        ->join('transacciones','transacciones.id_transaccion', 'fondos_garantia.id_subcontrato')
        ->join('empresas','transacciones.id_empresa', 'empresas.id_empresa');
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