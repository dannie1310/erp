<?php


namespace App\Models\CADECO;


class ItemAvanceSubcontrato extends Item
{

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->whereHas('avance');
        });
    }

    /**
     * Relaciones
     */
    public function avance()
    {
        return $this->belongsTo(AvanceSubcontrato::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Atributos
     */
    public function getAncestrosAttribute()
    {
        $lista = array();
        for($i = 1; $i < strlen($this->nivel)/4; $i++)
        {
            $nivel = substr($this->nivel, 0, 4*$i);
            $result = Contrato::where('id_transaccion', '=', $this->avance->subcontrato->id_antecedente)->where('nivel', '=', $nivel)->first();
            if($result) {
                array_push($lista, [$result->descripcion, $result->nivel, $result->clave, $i]);
            }
        }
        return $lista;
    }


    /**
     * Scope
     */

    /**
     * Métodos
     */
    public function partidasAvanceSubcontrato($id_avance, $contrato)
    {
        $avance = ItemAvanceSubcontrato::where('id_transaccion', '=', $id_avance)->where('id_concepto', $contrato->id_concepto)->first();
        $subcontrato = ItemSubcontrato::where('id_transaccion', '=', $avance->id_antecedente)->where('id_concepto', $contrato->id_concepto)->first();
        $precio_unitario = $avance ? $avance->precio_unitario : $this->precio_unitario;
        $cantidad_estimada_total = $this->cantidad_total_estimada ? $this->cantidad_total_estimada : 0;
        $cantidad_estimado_anterior = $avance ?  $cantidad_estimada_total - $avance->cantidad : $cantidad_estimada_total;
        $porcentaje_avance = $this->cantidad > 0? $cantidad_estimado_anterior / $this->cantidad:0;
        $porcentaje_estimado = 0;
        $avance && $this->cantidad > 0 ? $porcentaje_estimado = $avance->cantidad  / $this->cantidad:'';

        return array(
            'id' => $avance->id_item,
            'id_concepto' => $this->id_concepto,
            'unidad' => $contrato->unidad,
            'clave' => $contrato->clave,
            'cantidad_subcontrato_format' => $subcontrato->cantidad_format,
            'descripcion_concepto' => $contrato->descripcion,
            'cantidad_avance' => $this->cantidad_format,
            'precio_unitario_subcontrato' => $this->precio_unitario,
            'importe_subcontrato' => $this->cantidad * $this->precio_unitario,
            'precio_unitario_subcontrato_format' => $this->precio_unitario_format,
            'id_item_estimacion' =>  $avance ? $avance->id_item : 0,
            'cantidad_avance_format' => $avance ? number_format($avance->cantidad, 2, '.', '') : 0,
        );
    }
}
