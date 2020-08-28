<?php


namespace App\Models\CADECO\Compras;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraPartidaEliminada extends Model
{

    protected $connection = 'cadeco';
    protected $table = 'Compras.ordenes_compra_partidas_eliminadas';
    protected $primaryKey = 'id';
    
    protected $fillable = [
      'id_orden_compra_eliminada',
      'id_item',
      'id_transaccion',
      'id_antecedente',
      'id_material',
      'unidad',
      'cantidad',
      'anticipo',
      'descuento',
      'precio_material',
      'item_antecedente',
      'precio_unitario',
      'importe',
      'saldo',
      'elimino',
      'id_moneda',
    ];

}
