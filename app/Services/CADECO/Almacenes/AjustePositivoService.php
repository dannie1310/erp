<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:43 PM
 */

namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\AjustePositivo;
use App\Models\CADECO\Almacen;
use App\Repositories\CADECO\AjustePositivo\Repository;

class AjustePositivoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AjustePositivoService constructor.
     * @param AjustePositivo $model
     */
    public function __construct(AjustePositivo $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $ajuste = $this->repository;


        if (isset($data['numero_folio'])) {
            $ajuste = $ajuste->where([['numero_folio', 'LIKE', '%' . $data['numero_folio'] . '%']]);
        }

        if (isset($data['fecha'])) {
            $ajuste = $ajuste->where( [['fecha', '=', request( 'fecha' )]] );
        }

        if (isset($data['id_almacen'])) {
            $almacen = Almacen::query()->where([['descripcion', 'LIKE', '%'.$data['id_almacen'].'%']])->get();
            foreach ($almacen as $a){
                $ajuste = $ajuste->whereOr([['id_almacen', '=', $a->id_almacen]]);
            }
        }

        if (isset($data['referencia'])) {
            $ajuste = $ajuste->where([['referencia', 'LIKE', '%' . $data['referencia'] . '%']]);
        }

        if (isset($data['observaciones'])) {
            $ajuste = $ajuste->where([['observaciones', 'LIKE', '%' . $data['observaciones'] . '%']]);
        }

        return $ajuste->paginate($data);
    }

    public function store(array $data)
    {
        $datos = [
            'id_almacen' => $data['id_almacen'],
            'referencia' => $data['referencia'],
            'observaciones' => $data['observaciones'],
            'items' =>  $data['items']
        ];

        return $this->repository->create($datos);
    }
}