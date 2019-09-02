<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 30/05/2019
 * Time: 05:29 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Banco;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Repositories\CADECO\Finanzas\CuentaBancariaEmpresa\Repository;

class CuentaBancariaEmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaBancariaEmpresaService constructor.
     * @param CuentaBancariaEmpresa $model
     */
    public function __construct(CuentaBancariaEmpresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        $cuentas = $this->repository;

        if(isset($data['id_empresa'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();
            foreach ($empresa as $e){
                $cuentas = $cuentas->whereOr([['empresas.id_empresa', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['id_banco'])){
            $bancos = Banco::query()->where([['razon_social', 'LIKE', '%'.$data['id_banco'].'%']])->get();
            foreach ($bancos as $e){
                $cuentas = $cuentas->whereOr([['id_banco', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['cuenta_clabe']))
        {
            $cuentas = $cuentas->where([['cuenta_clabe', 'LIKE', '%'.$data['cuenta_clabe'].'%']]);
        }

//        if($data['sort'] == 'empresa__tipo_empresa'){
//            $empresa = Empresa::query()->orderBy('tipo_empresa',$data['order'])->get();
//            foreach ($empresa as $e){
//                $cuentas = $cuentas->whereOr([['id_empresa', '=', $e->id_empresa]]);
//            }
//        }

        return $cuentas->withoutGlobalScopes()->paginate_especial($data);
    }

    public function show($id)
    {
        return $this->repository->withoutGlobalScopes()->show($id);
    }
}