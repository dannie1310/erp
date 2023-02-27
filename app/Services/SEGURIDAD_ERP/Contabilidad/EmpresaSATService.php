<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:30 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\Cuenta;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\EmpresaSATRepository;
use App\Services\CTPQ\CuentaService;

class EmpresaSATService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param EmpresaSAT $model
     */
    public function __construct(EmpresaSAT $model)
    {
        $this->repository = new EmpresaSATRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function buscaPorRFC($rfc)
    {
        $this->repository->where([["rfc","=",$rfc]]);
        return $this->repository->first();
    }

    public function cargaCuentas($id_empresa_sat, $alias_bdd, $cuentas)
    {
        $cuentasService = new CuentaService(new Cuenta());
        /*$cuentas_no_validas = [];
        foreach ($cuentas as $cuenta)
        {
            if(!$cuentasService->validaCuenta($alias_bdd, $cuentas))
            {
                $cuentas_no_validas[] = $cuenta;
            }
        }
        if(count($cuentas_no_validas)>0){
            abort(500,"Las cuentas que se intentan cargar no corresponden a la empresa contpaq seleccionada.");
        }*/
        return $this->repository->cargarCuentas($id_empresa_sat, $cuentas);
    }
}
