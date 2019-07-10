<?php
/**
 * Created by PhpStorm.
 * User: Luis M.Valencia
 * Date: 01/07/2018
 * Time: 11:29 AM
 */

namespace App\Models\CADECO\Compras;

use App\Models\CADECO\Item;
use Ghi\Domain\Core\Models\Almacen;
use Ghi\Domain\Core\Models\Solicitud\SolicitudCompraPartida;
use Ghi\Domain\Core\Models\Concepto;
use Ghi\Domain\Core\Models\Entrega;




class OrdenCompraPartida extends Item
{
    protected static function  boot()
    {
        parent::boot();
//        self::addGlobalScope('tipo',function ($query){
//           return $query->where('tipo_transaccion','=',19);
//        });

    }
    protected $appends = ['cantidad_almacen', 'destino_string'];

    public function getCantidadAlmacenAttribute(){
        return (float)$this->hasMany(Item::class, 'item_antecedente', 'id_item')->sum('cantidad');
    }
    public function SolicitudPartida()
    {
        return $this->belongsTo(SolicitudCompraPartida::class, 'item_antecedente', 'id_item');
    }

    public  function ocPartidaComplemento(){
        return $this->hasOne(OrdenCompraPartidaComplemento::class, 'id_item');
    }


//    public function getDestinoStringAttribute()
//    {
//        $centro_costo = '';
//        $entrega = Entrega::find($this->item_antecedente);
//        if($entrega->id_almacen) {
//            $almacen = Almacen::find($entrega->id_almacen);
//            $centro_costo = $almacen->descripcion;
//        }
//        else if($entrega->id_concepto) {
//            $concepto = Concepto::find($entrega->id_concepto);
//            $padre = $concepto->padre();
//            $ancestro = !is_null($padre) ? $padre->padre() : null;
//            $centro_costo = (!is_null($ancestro) ? $ancestro->descripcion .' -> ' : '') . (!is_null($padre) ? $padre->descripcion .' -> ' : ' ') . $concepto->descripcion;
//        }
//        return $centro_costo;
//    }
}