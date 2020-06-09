<?php

/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 15/08/19
 * Time: 12:34 PM
 */
namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\Pago;
use App\Models\CADECO\Cuenta;
use App\Repositories\Repository;

class PagoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PagoServicevice constructor
     *
     * @param Pago $model
     */

    public function __construct(Pago $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data){
        $pagos = $this->repository;

        if(isset($data['numero_folio']))
        {
            $pagos = $pagos->where([['numero_folio','LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['destino']))
        {
            $pagos = $pagos->where([['destino','LIKE', '%'.$data['destino'].'%']]);
        }

        if(isset($data['numero_cuenta']))
        {
            $cuenta = Cuenta::query()->where([['numero', 'LIKE', '%'.$data['numero_cuenta'].'%']])->get();

            foreach ($cuenta as $e)
            {
                $pagos= $pagos->where([['id_cuenta', '=', $e->id_cuenta]]);
            }
        }

        if(isset($data['observaciones']))
        {
            $pagos = $pagos->where([['observaciones','LIKE', '%'.$data['observaciones'].'%']]);
        }

        if(isset($data['id_moneda']))
        {
            $moneda = Moneda::query()->where([['nombre', 'LIKE', '%'.$data['id_moneda'].'%']])->get();

            foreach ($moneda as $e)
            {
                $pagos= $pagos->where([['id_moneda', '=', $e->id_moneda]]);
            }
        }

        return $pagos->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data'][0]);
    }

}
