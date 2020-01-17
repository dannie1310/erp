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

class ProveedorContratistaService
{
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
    
    // public function paginate($data)
    // {
    //     if(isset($data['razon_social'])){
    //         return $this->repository->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
    //     }else if(isset($data['rfc'])){
    //         return $this->repository->where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
    //     }else{
    //         return $this->repository->paginate();
    //     }
    // }

    public function paginate($data)
    {
        $proveedorContratista = $this->repository;

        if(isset($data['rfc']))
        {
            $proveedorContratista = $cliente->where([['rfc', 'LIKE', '%' . request('rfc') . '%']]);
        }
        if(isset($data['razon_social']))
        {
            $proveedorContratista = $cliente->where([['razon_social', 'LIKE', '%' . request('razon_social') . '%']]);
        }
        if(isset($data['efo']))
        {
            $proveedor = ProveedorContratista::whereHas('efo.estadoEfo', function ($a){
                return $a->where('descripcion', 'LIKE', '%'.request('efo').'%');
            })->pluck('id_empresa');

            $proveedorContratista->whereIn(['id_empresa',$proveedor]);
        }
        return $proveedorContratista->paginate($data);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id){
        return $this->repository->update($data, $id);
    }
    
    public function delete($data, $id)
    {
        $this->repository->delete($data, $id);
    }
}