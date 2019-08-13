<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 07/08/19
 * Time: 06:00 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Banco;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgBanco;
use App\Repositories\Repository;

class BancoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * BancoService constructor
     *
     * @param Banco $model
     */

    public function __construct(Banco $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if(isset($data['razon_social'])){
            return $this->repository->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
        }else{
            return $this->repository->paginate();
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        if(!empty($data['id_ctg_banco'])) {

            $banco_Arr =CtgBanco::query()->where('id', '=', $data['id_ctg_banco'])->get()->toArray();
            $datos = [
                'id_ctg_bancos' => $data['id_ctg_banco'],
                'razon_social' => $banco_Arr[0]['razon_social'],
            ];
            $banco = Banco::query()->create($datos);
            return $banco;

        }
    }

    public function index()
    {
        return $this->repository->all();
    }
}
