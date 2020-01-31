<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 30/01/2020
 * Time: 12:40 PM
 */

namespace App\Services\CADECO\SubcontratosEstimaciones;


use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Repositories\CADECO\SubcontratosEstimaciones\Descuento\Repository;

class DescuentoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * VentaServiceService constructor.
     * @param Venta $model
     */
    public function __construct(Descuento $model)
    {
        $this->repository = new Repository($model);
    }

    public function find($id)
    {
        return $this->repository->show($id);
    }

    public function list($id)
    {  
        return $this->repository->list($id);
    }

    public function store(array $data)
    {
        $duplicado = $this->repository->duplicado($data['id_transaccion'], $data['id_material']);
        if($duplicado > 0) abort(403, 'Material agregado previamente.');
        return $this->repository->create($data);
    }

    public function updateList($data){
        $id_transaccion = '';
        foreach($data as $descuento){
            $id_transaccion = $descuento['id_transaccion'];
            $desc = $this->repository->show($descuento['id']);
            if($descuento['cantidad'] == 0){
                $desc->delete();
            }else{
                $desc->update([
                    'cantidad' => $descuento['cantidad'],
                    'precio' => $descuento['precio'],
                ]);
            }
        }
        return $this->list($id_transaccion);
    }
}