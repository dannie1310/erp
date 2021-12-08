<?php

namespace App\Repositories\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgEspecialidad;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgGiro;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivoTipoEmpresa;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinada;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaPrestadora;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Views\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaVw;

class EmpresaBoletinadaRepository extends Repository implements RepositoryInterface
{
    protected $model_real;

    public function __construct(EmpresaBoletinada $model, EmpresaBoletinadaVw $model_vw)
    {
        parent::__construct($model_vw);
        $this->model = $model_vw;
        $this->model_real = $model;
    }

    public function store($data)
    {
        $model = $this->model_real->create($data);
        $model_vw = EmpresaBoletinadaVw::where("rfc","=",$model->rfc)->first();
        return $model_vw;
    }

    public function update(array $data, $id)
    {
        $item = $this->model_real->find($id);
        $item->update($data);
        return $item;
    }

    public function delete(array $data, $id)
    {
        $this->model_real->destroy($id);
    }

    public function validaPreexistencia($rfc)
    {
        $model_vw = EmpresaBoletinadaVw::where("rfc","=",$rfc)->first();
        if($model_vw){
            abort(500,"La empresa con RFC: ". $rfc . " ya esta boletinada por ".$model_vw->motivo_txt);
        }
    }
}
