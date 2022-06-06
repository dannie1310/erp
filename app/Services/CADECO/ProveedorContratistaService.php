<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:30 PM
 */

namespace App\Services\CADECO;


use App\Facades\Context;
use App\Repositories\CADECO\ProveedorContratista\Repository;
use App\Models\CADECO\ProveedorContratista;
use App\Events\IncidenciaCI;
use App\Traits\EmpresaTrait;

class ProveedorContratistaService
{
    use EmpresaTrait;
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ProveedorContratistaService constructor.
     *
     * @param ProveedorContratista $model
     */
    public function __construct(ProveedorContratista $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $proveedorContratista = $this->repository;

        if(isset($data['razon_social'])){
            return $this->repository->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
        }else if(isset($data['rfc'])){
            return $this->repository->where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
        }else if(isset($data['efo']))
        {
            $proveedor = ProveedorContratista::whereHas('efo.estadoEfo', function ($a){
                return $a->where('descripcion', 'LIKE', '%'.request('efo').'%');
            })->pluck('id_empresa');
            $proveedorContratista->whereIn(['id_empresa',$proveedor]);
            return $proveedorContratista->paginate($data);
        }
        return $this->repository->paginate();
    }

    private function getValidacionLRFC($rfc, $razon_social, $tipo_incidencia)
    {
        try{
            $this->rfcValidaEfos($rfc);
        }catch (\Exception $e){
            event(new IncidenciaCI(
                ["id_tipo_incidencia"=>$tipo_incidencia,
                    "rfc"=>$rfc,
                    "empresa"=>$razon_social,
                    "mensaje"=>$e->getMessage(),
                ]
            ));
            abort(500,$e->getMessage());
        }

        try{
            $this->rfcValidaBoletinados($rfc);
        }catch (\Exception $e){
            event(new IncidenciaCI(
                ["id_tipo_incidencia"=>$tipo_incidencia,
                    "rfc"=>$rfc,
                    "empresa"=>$razon_social,
                    "mensaje"=>$e->getMessage(),
                ]
            ));
            abort(500,$e->getMessage());
        }
    }

    public function store(array $data)
    {
        if($data["es_nacional"]==0){
            $data['rfc'] = 'XEXX010101000';
            $data["emite_factura"]=0;
        }
        if($data["emite_factura"]==0 && $data["es_nacional"] ==1){
            $data['rfc_nuevo'] = 'XXXXXXXXXXXX';
        }
        if($data["emite_factura"] == 1){
            if($data['rfc'] == 'XXXXXXXXXXXX' || $data['rfc'] == 'XEXX010101000') abort(403, 'El RFC tiene formato inválido.');
            //$this->getValidacionLRFC($data["rfc"], $data["razon_social"],7);
        }
        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id){
        if($data["es_nacional"]==0){
            $data['rfc_nuevo'] = 'XEXX010101000';
            $data["emite_factura"]=0;
        }
        if($data["emite_factura"]==0 && $data["es_nacional"] ==1){
            $data['rfc_nuevo'] = 'XXXXXXXXXXXX';
        }
        $actual_rfc = $this->repository->getRFC($id);
        if($data["rfc_nuevo"] != $actual_rfc){
            $this->repository->validarRegistroXml($id);
        }

        if($data["emite_factura"] == 1)
        {
            if($data['rfc_nuevo'] == 'XXXXXXXXXXXX'||$data['rfc_nuevo'] == 'XEXX010101000') abort(403, 'El RFC tiene formato inválido.');
            $this->getValidacionLRFC($data["rfc_nuevo"], $data["razon_social"],17);
        }
        return $this->repository->update($data, $id);
    }

    public function delete($data, $id)
    {
        $this->repository->delete($data, $id);
    }
}
