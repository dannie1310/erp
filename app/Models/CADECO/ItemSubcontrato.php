<?php


namespace App\Models\CADECO;


class ItemSubcontrato extends Item
{
    public function subcontrato(){
        return $this->belongsTo(Subcontrato::class, 'id_transaccion', 'id_transaccion');
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_concepto', 'id_concepto');
    }

    public function getEstimadoAnteriorAttribute($id)
    {
        return Item::where('item_antecedente', '=', $this->id_concepto)->where("id_transaccion", '<', $id)->where("id_antecedente", '=', $this->id_transaccion)
            ->where('id_concepto', '!=', null)->get()->sum('cantidad');
    }

    public function getAncestrosAttribute(){

        $list=array();
        $size = strlen($this->contrato->nivel)/4;
        $first=4;

        for($i=0; $i<$size-1;$i++){
            $nivel=substr($this->contrato->nivel,0,$first);
            $result= $this->contrato->where('id_transaccion','=',$this->subcontrato->id_antecedente)->where('id_concepto','<', $this->id_concepto)->where('nivel','LIKE',$nivel)->get();
            array_push($list,[$result[0]->descripcion, $result[0]->nivel]);
            $first+=4;
        }
        return $list;
    }

    public function getEstimacionPartidaAttribute($id){
        return EstimacionPartida::query()->where('id_antecedente', '=',$this->id_transaccion)->where('item_antecedente', '=', $this->id_concepto)
            ->where('id_transaccion','=', $id)->first();
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$ ' . number_format($this->precio_unitario,2,'.',',');
    }

    public function getCantidadFormatAttribute()
    {
        return '$ ' . number_format($this->cantidad,2,'.',',');
    }
}
