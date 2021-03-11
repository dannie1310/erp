<?php


namespace App\Repositories\SEGURIDAD_ERP\Finanzas;


use App\Models\CADECO\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI as Model;

class SolicitudRecepcionCFDIRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }

    public function getEmpresa(ProveedorSAT $empresa)
    {
        $empresaSAO= Empresa::where("rfc","=",$empresa->rfc)->whereIn("tipo_empresa",[1,2,3])->first();
        if(!$empresaSAO){
            $empresaSAO = Empresa::create([
                "razon_social"=>$empresa->razon_social
                , "rfc"=>$empresa->rfc
                , "tipo"=>3
            ]);
        }
        return $empresaSAO;
    }

}
