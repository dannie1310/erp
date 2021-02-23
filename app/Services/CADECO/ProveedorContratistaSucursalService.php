<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 08/08/19
 * Time: 06:00 PM
 */

namespace App\Services\CADECO;


use App\Repositories\Repository;
use App\Models\CADECO\ProveedorContratistaSucursal;

class ProveedorContratistaSucursalService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SucursalService constructor
     *
     * @param ProveedorContratistaSucursal $model
     */

    public function __construct(ProveedorContratistaSucursal $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data){

        if(!empty($data['id'])){

            return $this->repository->where([['id_empresa','=',$data['id']]])->paginate();
        }else{
            return $this->repository->paginate();
        }

    }

    public function show($id){
        return $this->repository->show($id);
    }
    
    public function storeProveedorSucursal(array $data)
    {
        $central='N';
        if(isset($data["checkCentral"]) && $data["checkCentral"]==true){
            $central='S';
        }
        $sucursal = $this->repository->create($data);
        return $sucursal;
    }

    public function store(array $data)
    {
        $central='N';
        if(isset($data["checkCentral"]) && $data["checkCentral"]==true){
            $central='S';
        }

        // $datos = [
        //     'id_empresa'=> $data['id'],
        //     'descripcion' => $data['descripcion'],
        //     'direccion' => $data['direccion'],
        //     'ciudad' => $data['ciudad'],
        //     'codigo_postal' => $data['codigo_postal'],
        //     'estado' => $data['estado'],
        //     'telefono'=> $data['voz'],
        //     'fax' => $data['fax'],
        //     'contacto'=>$data['contacto'],
        //     'casa_central'=>$central,

        // ];
        $sucursal = $this->repository->create($data);
        return $sucursal;
    }

    public function updateProveedorSucursal(array $data, $id)
    {
        $central='N';
        if(isset($data["checkCentral"]) && $data["checkCentral"]==true){
            $central='S';
        }

        // $datos = [
        //     'id_sucursal'=> $id,
        //     'descripcion' => $data['descripcion'],
        //     'direccion' => $data['direccion'],
        //     'ciudad' => $data['ciudad'],
        //     'codigo_postal' => $data['codigo_postal'],
        //     'estado' => $data['estado'],
        //     'telefono'=> $data['telefono'],
        //     'fax'=> $data['fax'],
        //     'contacto'=>$data['contacto'],
        //     'casa_central'=>$central,
        // ];

        return $this->repository->update($data, $id);
    }

    public function update(array $data, $id)
    {
        $central='N';
        if(isset($data["checkCentral"]) && $data["checkCentral"]==true){
            $central='S';
        }

        // $datos = [
        //     'id_sucursal'=> $id,
        //     'descripcion' => $data['descripcion'],
        //     'direccion' => $data['direccion'],
        //     'ciudad' => $data['ciudad'],
        //     'codigo_postal' => $data['codigo_postal'],
        //     'estado' => $data['estado'],
        //     'telefono'=> $data['telefono'],
        //     'fax'=> $data['fax'],
        //     'contacto'=>$data['contacto'],
        //     'casa_central'=>$central,
        // ];

        return $this->repository->update($data, $id);
    }
    
    public function delete($data, $id)
    {
        return $this->repository->delete($data, $id);
    }
}