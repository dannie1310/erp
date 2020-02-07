<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 30/01/2020
 * Time: 12:40 PM
 */

namespace App\Services\CADECO\SubcontratosEstimaciones;


use Illuminate\Support\Facades\DB;
use App\Models\CADECO\SalidaAlmacenPartida;
use App\Models\CADECO\Compras\ItemContratista;
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

    public function listItems($id, $data)
    { 
        $itemsContratista = ItemContratista::where('id_empresa', '=', $id)->where('con_cargo', '=', 1)->pluck('id_item');
        
        $salidas = SalidaAlmacenPartida::itemContratista()->with('material')->whereIn('id_item', $itemsContratista)
        ->select('id_material', DB::raw('sum(cantidad) as cantidad, (sum(importe) / sum(cantidad)) as precio_unitario '))
        ->groupBy('id_material')
        ->get();

        $lista = array();
        foreach($salidas as $salida){
            $disponible = $salida->cantidad;
            if($descuento = $this->repository->getDescuento($data['id_estimacion'], $salida->id_material)){
                $disponible -= $descuento->cantidad;
            }
            if($disponible > 0){
                $lista[] = [
                    'descripcion' => $salida->material->descripcion,
                    'id_material' => $salida->id_material,
                    'cantidad' => number_format($salida->cantidad, 2, '.', ''),
                    'cantidad_disponible' => number_format($disponible, 2, '.', ''),
                    'precio' => number_format($salida->precio_unitario, 2, '.', ''),
                    'cantidad_descontada' => $descuento?$descuento->cantidad:0,
                ];
            }
        }

        return $lista;
    }

    public function storeItem(array $data)
    {
        if($registrado = $this->repository->registrado_previamente($data['id_transaccion'], $data['id_material'])){
            $registrado->update([
                    'cantidad' => $registrado->cantidad + $data['cantidad'],
                    'precio' => $data['precio'],
                ]);
        }else{
            $this->repository->create($data);
        }
        return $this->list($data['id_transaccion']);
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