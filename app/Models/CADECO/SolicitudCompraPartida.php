<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 04:56 p. m.
 */


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\SolicitudPartidaComplemento;

class SolicitudCompraPartida extends Item
{
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_material',
        'unidad',
        'cantidad',
        'id_concepto',
        'id_almacen'
    ];

    public function complemento()
    {
        return $this->belongsTo(SolicitudPartidaComplemento::class, 'id_item', 'id_item');
    }

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'id_item', 'id_item');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }



}
