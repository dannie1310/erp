<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Estimaciones\ItemSolicitudAutorizacionAvanceEliminada;

class ItemSolicitudAutorizacionAvance extends Item
{
    public function itemEliminado()
    {
        return $this->belongsTo(ItemSolicitudAutorizacionAvanceEliminada::class, 'id_item','id_item');
    }

    public function itemSubcontrato()
    {
        return $this->belongsTo(ItemSubcontrato::class, 'item_antecedente', 'id_concepto')->where('id_transaccion','=', $this->id_antecedente);
    }

    public function validarCantidadesPartidas()
    {
        $cantidad_estimada_anterior =((float) $this->itemSubcontrato->cantidad_total_estimada) - ($this->original != [] ? $this->original['cantidad'] : 0);
        if( number_format($this->itemSubcontrato->cantidad,2,".","") < number_format($cantidad_estimada_anterior + $this->cantidad,2,".",""))
        {
            abort(400, 'La cantidad de la partida "'.$this->contrato->descripcion. '": '.($cantidad_estimada_anterior + $this->cantidad).' sobrepasa la cantidad del subcontrato: '. $this->itemSubcontrato->cantidad);
        }
    }
}
