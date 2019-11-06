<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:33 p. m.
 */


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\SolicitudCompraPartida;
use League\Fractal\TransformerAbstract;

class SolicitudCompraPartidaTransformer extends TransformerAbstract
{
 public function transform(SolicitudCompraPartida $model)
 {
     return [
         'id' =>$model->getKey(),
         'id_transaccion' => $model->id_transaccion,
         'id_material' => $model->id_material,
         'unidad' => $model->unidad,
         'cantidad' => $model->cantidad,
         'id_concepto'=> $model->id_concepto,
         'id_almacen' =>$model->id_almacen
     ];

 }
}
