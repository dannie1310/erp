<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Compras\ItemContratista;
use App\Repositories\Repository;

class ItemContratistaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MaterialService constructor.
     *
     * @param ItemContratista $model
     */
    public function __construct(ItemContratista $model)
    {
        $this->repository = new Repository($model);
    }

    public function update($data, $id)
    {
        $dato = ['id_item'=>$id,
                'id_empresa'=>$data['params']['data']['empresa_contratista'],
                'con_cargo'=>$data['params']['data']['opcion'],
        ];
        if($this->repository->show($id)){
            return $this->repository->update($dato,$id);
        }else{
            return $this->repository->create($dato);
        }
    }

    public function delete($data, $id)
    {
        $this->repository->delete($data, $id);
    }


}