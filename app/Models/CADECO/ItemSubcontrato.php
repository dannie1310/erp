<?php


namespace App\Models\CADECO;


class ItemSubcontrato extends Item
{
    public function subcontrato()
    {
        return $this->belongsTo(Subcontrato::class, 'id_transaccion', 'id_transaccion');
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_concepto', 'id_concepto');
    }

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_concepto','id_concepto_contrato');
    }

    public function partidaEstimacion()
    {
        return $this->belongsTo(ItemEstimacion::class, 'id_transaccion', 'id_antecedente');
    }

    public function getCantidadTotalEstimadaAttribute()
    {
        return ItemEstimacion::where('item_antecedente', '=', $this->id_concepto)->where("id_antecedente", '=', $this->id_transaccion)->sum('cantidad');
    }

    public function getAncestrosAttribute()
    {
        $lista = array();
        for($i = 1; $i < strlen($this->nivel)/4; $i++)
        {
            $nivel = substr($this->nivel, 0, 4*$i);
            $result = Contrato::where('id_transaccion', '=', $this->subcontrato->id_antecedente)->where('nivel', '=', $nivel)->first();
            if($result) {
                array_push($lista, [$result->descripcion, $result->nivel, $result->clave, $i]);
            }
        }
        return $lista;
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$ ' . number_format($this->precio_unitario, 2, '.', ',');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad, 2, '.', ',');
    }

    /**
     * Función para obtener cifras utilizadas en estimaciones (creación, edición, consulta)
     * @return array
     */
    public function partidasEstimadas($id_estimacion, $id_contrato)
    {
       $estimacion = ItemEstimacion::where('id_transaccion', '=', $id_estimacion)
           ->where('id_antecedente', $this->id_transaccion)
           ->where('item_antecedente', $this->id_concepto)->first();

        $precio_unitario = $estimacion ? $estimacion->precio_unitario : $this->precio_unitario;
        $cantidad_estimado_anterior = $estimacion ?  $this->cantidad_total_estimada - $estimacion->cantidad : $this->cantidad_total_estimada;
        $contrato = $this->contrato()->where('id_transaccion', '=', $id_contrato)->first();
        $destino = $this->destino()->where('id_transaccion', '=', $id_contrato)->first();

        return array(
            'id' => $this->id_item,
            'id_concepto' => $this->id_concepto,
            'unidad' => $contrato->unidad,
            'clave' => $contrato->clave,
            'descripcion_concepto' => $contrato->descripcion,
            'cantidad_subcontrato' => $this->cantidad,
            'precio_unitario_subcontrato' => $this->precio_unitario,
            'cantidad_subcontrato_format' => $this->cantidad_format,
            'precio_unitario_subcontrato_format' => $this->precio_unitario_format,
            'id_item_estimacion' =>  $estimacion ? $estimacion->id_item : 0,
            'cantidad_estimacion' => $estimacion ? number_format($estimacion->cantidad, 2, '.', '') : 0,
            'precio_unitario_estimacion' => $estimacion ? $estimacion->precio_unitario : 0,
            'importe_estimacion' => $estimacion ? number_format($estimacion->importe, 2, '.', '') : 0,
            'cantidad_estimacion_format' => $estimacion ? $estimacion->cantidad_format: 0,
            'precio_unitario_estimacion_format' => $estimacion ? $estimacion->precio_unitario_format : 0,
            'porcentaje_avance' => (float) number_format((($cantidad_estimado_anterior / $this->cantidad) * 100), 3, '.', ''),
            'cantidad_estimada_total' => $this->cantidad_total_estimada,
            'cantidad_estimada_anterior' => $cantidad_estimado_anterior,
            'importe_estimado_anterior' => ($cantidad_estimado_anterior * $precio_unitario),
            'importe_acumulado' => $this->cantidad_total_estimada * $precio_unitario,
            'cantidad_por_estimar' => $this->cantidad -$cantidad_estimado_anterior,
            'importe_por_estimar' => ($this->cantidad - $cantidad_estimado_anterior) * $precio_unitario,
            'porcentaje_estimado' => (float) number_format(((($estimacion ? $estimacion->cantidad : 0) / $this->cantidad) * 100), 3, '.', ''),
            'destino_path' => $destino->ruta_destino,
            'id_destino' => $destino->id_concepto,
            'nivel' => strlen($contrato->nivel)/4
        );
    }

    private function importe_total($precio_unitario)
    {
        return  '$ ' . number_format($this->cantidad * $precio_unitario,2,'.',',');
    }
}
