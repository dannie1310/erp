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
use App\Repositories\CADECO\Empresa\Repository;
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

    public function detalleUnificacion($data, $id){
        $empresa = $this->repository->show($id);
        $empresas_unificar = $this->repository->getDuplicados($empresa->rfc);
        $datos = [];
        $datos['id_empresa'] = $empresas_unificar[0]->id_empresa;
        $datos['tipo_empresa'] = $empresas_unificar[0]->tipo_empresa;
        $datos['tipo_empresa_actualizado'] = $empresas_unificar[0]->tipo_empresa > 3?'':$empresas_unificar[0]->tipo_empresa;
        $datos['tipo_empresa_format'] = $empresas_unificar[0]->tipo;
        $datos['rfc'] = $empresas_unificar[0]->rfc;
        $datos['razon_social'] = $empresas_unificar[0]->razon_social;
        $datos['empresas_unificadas'] = [];

        foreach($empresas_unificar as $key => $empresa_unificada){
            if($key == 0)continue;
            $datos['empresas_unificadas'][] = $this->detalleEmpresaUnificada($empresa_unificada->id_empresa);

        }
        return $datos;
    }

    public function detalleEmpresaUnificada($id_empresa){
        $empresa = $this->repository->show($id_empresa);
        $af_transacciones = $empresa->getTransaccionesAfectacion();
        $af_solicitudesCBE = $empresa->getSolicitudesCBEAfectacion();
        $af_cuentas = $empresa->getCuentaBancariaEmresaAfectacion();

        return [
            'id_empresa' => $empresa->id_empresa,
            'tipo_empresa' => $empresa->tipo_empresa,
            'tipo_empresa_format' => $empresa->tipo,
            'rfc' => $empresa->rfc,
            'razon_social' => $empresa->razon_social,
            'af_transacciones' => $af_transacciones,
            'af_solicitudesCBE' => $af_solicitudesCBE,
            'af_cuentas' => $af_cuentas,
        ];

    }

}
