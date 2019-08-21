<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 07/02/19
 * Time: 05:00 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Banco;
use App\Models\CADECO\Empresa;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgBanco;
use App\Repositories\Repository;
use App\Models\CADECO\Obra;
use Illuminate\Support\Facades\DB;

class EmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EmpresaService constructor.
     *
     * @param Empresa $model
     */
    public function __construct(Empresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
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
        try {
            DB::connection('cadeco')->beginTransaction();

        $datos = [
            'tipo_empresa' => $data['tipo_empresa'],
            'razon_social' => $data['razon_social'],
            'UsuarioRegistro'=>$data['UsuarioRegistro'],
        ];

         $empresa = Empresa::query()->create($datos);

            DB::connection('cadeco')->commit();

            return $empresa ;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }

    }
}
