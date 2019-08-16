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

        if(isset($data['numero_folio'])){
            $pagos = $pagos->where([['numero_folio','LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['id_empresa'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();

                   foreach ($empresa as $e){
                       $pagos= $pagos->where([['id_empresa', '=', $e->id_empresa]]);
                   }

        }

        if(isset($data['id_cuenta'])){
            $cuenta = Cuenta::query()->where([['id_cuenta', 'LIKE', '%'.$data['id_cuenta'].'%']])->get();

            foreach ($cuenta as $e){
                $pagos= $pagos->where([['id_cuenta', '=', $e->id_cuenta]]);
            }
        }

        if(isset($data['observaciones'])){
            $pagos = $pagos->where([['observaciones','LIKE', '%'.$data['observaciones'].'%']]);
        }

        if(isset($data['id_moneda'])){
            $moneda = Moneda::query()->where([['nombre', 'LIKE', '%'.$data['id_moneda'].'%']])->get();

            foreach ($moneda as $e){
                $pagos= $pagos->where([['id_moneda', '=', $e->id_moneda]]);
            }
        }


        return $pagos->paginate($data);
    }

}
