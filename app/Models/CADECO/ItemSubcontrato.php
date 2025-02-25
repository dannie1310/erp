<?php


namespace App\Models\CADECO;

use App\Models\CADECO\Contrato;
use App\Models\CADECO\Subcontratos\SubcontratoPartidaEliminada;
use App\Models\CADECO\SubcontratosCM\ItemSubcontratoOriginal;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ItemSubcontrato extends Item
{
    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'item_antecedente',
        'id_concepto',
        'id_material',
        'unidad',
        'cantidad',
        'cantidad_material',
        'cantidad_mano_obra',
        'importe',
        'saldo',
        'anticipo',
        'descuento',
        'precio_unitario',
        'precio_material',
        'precio_mano_obra',
        'cantidad_original1',
        'precio_original1',
        'id_asignacion',
        'id_sm_partida',
    ];
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

    public function presupuesto()
    {
        return $this->belongsTo(PresupuestoContratista::class,"id_antecedente","id_transaccion");
    }

    public function partidaEstimacion()
    {
        return $this->belongsTo(ItemEstimacion::class, 'id_transaccion', 'id_antecedente');
    }

    public function partidaSubcontratoEliminada()
    {
        return $this->belongsTo(SubcontratoPartidaEliminada::class, 'id_item');
    }

    public function valoresOriginales()
    {
        return $this->hasMany(ItemSubcontratoOriginal::class, "id_item", "id_item");
    }

    public function getCantidadTotalEstimadaAttribute()
    {
        return ItemEstimacion::where('item_antecedente', '=', $this->id_concepto)->where("id_antecedente", '=', $this->id_transaccion)->sum('cantidad');
    }

    public function getImporteTotalEstimadoAttribute()
    {
        return ItemEstimacion::where('item_antecedente', '=', $this->id_concepto)->where("id_antecedente", '=', $this->id_transaccion)->sum('importe');
    }

    public function getPrecioUnitarioMasDescuentoAttribute()
    {
        return ($this->precio_unitario * $this->descuento) / 90 + $this->precio_unitario;
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

    public function getAncestrosSinContextoAttribute($id_antecedente,$base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $lista = array();
        for($i = 1; $i < strlen($this->nivel)/4; $i++)
        {
            $nivel = substr($this->nivel, 0, 4*$i);
            $result = Contrato::withoutGlobalScopes()->where('id_transaccion', '=', $id_antecedente)->where('nivel', '=', $nivel)->first();
            if($result) {
                array_push($lista, [$result->descripcion, $result->nivel, $result->clave, $i]);
            }
        }
        return $lista;
    }

    public function getEstimacionPartidaAttribute($id){
        return EstimacionPartida::where('id_antecedente', '=',$this->id_transaccion)->where('item_antecedente', '=', $this->id_concepto)
            ->where('id_transaccion','=', $id)->first();
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$' . number_format($this->precio_unitario, 3, '.', ',');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad, 2, '.', ',');
    }

    /**
     * Función para obtener cifras utilizadas en estimaciones (creación, edición, consulta)
     * @return array
     */
    public function partidasEstimadas($id_estimacion, $id_contrato, $contrato)
    {
        $estimacion = ItemEstimacion::where('id_transaccion', '=', $id_estimacion)
            ->where('id_antecedente', $this->id_transaccion)
            ->where('item_antecedente', $this->id_concepto)->first();

        $precio_unitario = $estimacion ? $estimacion->precio_unitario : $this->precio_unitario;
        $cantidad_estimada_total = $this->cantidad_total_estimada ? $this->cantidad_total_estimada : 0;
        $cantidad_estimado_anterior = $estimacion ? $cantidad_estimada_total - $estimacion->cantidad : $cantidad_estimada_total;
        $porcentaje_avance = $this->cantidad > 0 ? $cantidad_estimado_anterior / $this->cantidad : 0;
        $porcentaje_estimado = 0;
        $estimacion && $this->cantidad > 0 ? $porcentaje_estimado = $estimacion->cantidad / $this->cantidad : '';
        $path = null;
        $path_corta = null;
        $id_destino = null;
        if ($estimacion) {
            $con = Concepto::where('id_concepto', $estimacion->id_concepto)->first();
            $path_corta = $con->path_corta;
            $path = $con->path;
            $id_destino = $estimacion->id_concepto;
        } else {
            $destino = Destino::where('id_transaccion', '=', $id_contrato)->where('id_concepto_contrato', '=', $contrato->id_concepto)->first();
            $path_corta = $destino->concepto ? $destino->concepto->path_corta : $destino->concepto_sgv->path_corta_proveedor;
            $path =  $destino->concepto ? $destino->concepto->path : $destino->concepto_sgv->path_sgv;
            $id_destino = $destino->id_concepto;
        }
        return array(
            'id' => $this->id_item,
            'id_concepto' => $this->id_concepto,
            'estimado' => $estimacion ? true : false,
            'unidad' => $contrato->unidad,
            'clave' => $contrato->clave,
            'descripcion_concepto' => $contrato->descripcion,
            'cantidad_subcontrato' => $this->cantidad,
            'precio_unitario_subcontrato' => $this->precio_unitario,
            'importe_subcontrato' => $this->cantidad * $this->precio_unitario,
            'precio_unitario_subcontrato_format' => $this->precio_unitario_format,
            'id_item_estimacion' => $estimacion ? $estimacion->id_item : 0,
            'cantidad_estimacion' => $estimacion ? number_format($estimacion->cantidad, 3, '.', '') : 0,
            'porcentaje_avance' => number_format((($porcentaje_avance) * 100), 3, '.', ''),
            'cantidad_estimada_total' => $cantidad_estimada_total ? $cantidad_estimada_total : 0,
            'cantidad_estimada_anterior' => $cantidad_estimado_anterior,
            'importe_estimado_anterior' => ($cantidad_estimado_anterior * $precio_unitario),
            'importe_acumulado' => ($cantidad_estimada_total ? $cantidad_estimada_total : 0) * $precio_unitario,
            'cantidad_por_estimar' => $this->cantidad - $cantidad_estimado_anterior,
            'importe_por_estimar' => (($this->cantidad - $cantidad_estimado_anterior) * $precio_unitario),
            'porcentaje_estimado' => number_format((($porcentaje_estimado) * 100), 3, '.', ''),
            'importe_estimacion' => $estimacion ? number_format($estimacion->importe, 3, '.', '') : 0,
            'destino_path' => $path_corta,
            'destino_path_larga' => $path,
            'id_destino' => $id_destino,
            'cantidad_addendum' => "",
            'precio_modificado' => 0,
        );
    }

    /**
     * Función para obtener cifras utilizadas en estimaciones (creación, edición, consulta)
     * @return array
     */
    public function proveedorPartidasEstimadas($id_estimacion, $id_contrato, $contrato, $id_obra)
    {
       $estimacion = ItemEstimacion::where('id_transaccion', '=', $id_estimacion)
           ->where('id_antecedente', $this->id_transaccion)
           ->where('item_antecedente', $this->id_concepto)->first();

        $precio_unitario = $estimacion ? $estimacion->precio_unitario : $this->precio_unitario;
        $cantidad_estimada_total = $this->cantidad_total_estimada ? $this->cantidad_total_estimada : 0;
        $cantidad_estimado_anterior = $estimacion ?  $cantidad_estimada_total - $estimacion->cantidad : $cantidad_estimada_total;
        $porcentaje_avance = $this->cantidad > 0? $cantidad_estimado_anterior / $this->cantidad:0;
        $porcentaje_estimado = 0;
        $estimacion && $this->cantidad > 0 ? $porcentaje_estimado = $estimacion->cantidad  / $this->cantidad:'';
        $destino = Destino::where('id_transaccion', '=', $id_contrato)->where('id_concepto_contrato', '=', $contrato->id_concepto)->first();

        return array(
            'id' => $this->id_item,
            'id_concepto' => $this->id_concepto,
            'unidad' => $contrato->unidad,
            'clave' => $contrato->clave,
            'descripcion_concepto' => $contrato->descripcion,
            'cantidad_subcontrato' => $this->cantidad,
            'precio_unitario_subcontrato' => $this->precio_unitario,
            'importe_subcontrato' => $this->cantidad * $this->precio_unitario,
            'precio_unitario_subcontrato_format' => $this->precio_unitario_format,
            'id_item_estimacion' =>  $estimacion ? $estimacion->id_item : 0,
            'cantidad_estimacion' => $estimacion ? number_format($estimacion->cantidad, 3, '.', '') : 0,
            'porcentaje_avance' => (float) number_format((($porcentaje_avance) * 100), 3, '.', ''),
            'cantidad_estimada_total' => $cantidad_estimada_total ? $cantidad_estimada_total : 0,
            'cantidad_estimada_anterior' => $cantidad_estimado_anterior,
            'importe_estimado_anterior' => ($cantidad_estimado_anterior * $precio_unitario),
            'importe_acumulado' => ($cantidad_estimada_total ? $cantidad_estimada_total : 0) * $precio_unitario,
            'cantidad_por_estimar' => $this->cantidad - $cantidad_estimado_anterior,
            'importe_por_estimar' => (($this->cantidad - $cantidad_estimado_anterior) * $precio_unitario),
            'porcentaje_estimado' => (float) number_format((($porcentaje_estimado) * 100), 3, '.', ''),
            'importe_estimacion' => $estimacion ? number_format($estimacion->importe, 3, '.', '') : 0,
            'destino_path' => $destino->concepto_sgv->path_corta_proveedor,
            'destino_path_larga' => $destino->concepto_sgv->getPathSgv($id_obra),
            'id_destino' => $destino->id_concepto,
            'cantidad_addendum' => "",
            'precio_modificado' => 0,
        );
    }

    public function partidasFormatoEstimacion($id_estimacion, $contrato)
    {
        $estimacion = ItemEstimacion::where('id_transaccion', '=', $id_estimacion)
            ->where('id_antecedente', $this->id_transaccion)
            ->where('item_antecedente', $this->id_concepto)->first();

        $acumulado_anterior = $this->acumulado_anterior->where('id_transaccion', '<', $id_estimacion);
       // $contrato = $this->contrato()->where('id_transaccion', '=', $this->subcontrato->id_antecedente)->first();

        $cantidad_estimacion = $estimacion ? $estimacion->cantidad : 0;
        $importe_estimacion =  $estimacion ? $estimacion->importe : 0;

        return array(
            'id' => $this->id_item,
            'id_concepto' => $this->id_concepto,
            'unidad' => $contrato ? $contrato->unidad : '',
            'clave' => $contrato ? $contrato->clave : '',
            'descripcion_concepto' => $contrato ? $contrato->descripcion : '',
            'cantidad_subcontrato' => $this->cantidad,
            'precio_unitario_subcontrato' => $this->precio_unitario,
            'importe_subcontrato' => ($this->cantidad * $this->precio_unitario),
            'cantidad_acumulado_anterior' => $acumulado_anterior->sum('cantidad'),
            'importe_acumulado_anterior' => $acumulado_anterior->sum('importe'),
            'cantidad_estimacion' => $cantidad_estimacion,
            'importe_estimacion' => $importe_estimacion,
            'cantidad_addendum' => '',
            'importe_addendum' => 0,
            'cantidad_acumulado_a_esta_estimacion' => $acumulado_anterior->sum('cantidad') + $cantidad_estimacion,
            'importe_acumulado_a_esta_estimacion' => $acumulado_anterior->sum('importe') + $importe_estimacion,
            'cantidad_porEstimar' => $this->cantidad - ($acumulado_anterior->sum('cantidad') + $cantidad_estimacion),
            'importe_porEstimar' => ($this->cantidad * $this->precio_unitario) - ($acumulado_anterior->sum('importe') + $importe_estimacion)
        );
    }

    public function getImporteTotalAttribute()
    {
        return  '$' . number_format($this->cantidad * $this->precio_unitario,2,'.',',');
    }

    public function getAcumuladoAnteriorAttribute()
    {
        return Item::where('id_antecedente', $this->id_transaccion)->where('item_antecedente', $this->id_concepto);
    }

    public function getCantidadEstimadaFormatAttribute(){
        return number_format($this->cantidad_total_estimada,3, ".",",");
    }

    public function getImporteEstimadoFormatAttribute(){
        return "$" . number_format($this->importe_total_estimado,3, ".",",");
    }

    public function getCantidadSaldoFormatAttribute(){
        return number_format($this->cantidad-$this->cantidad_total_estimada,3, ".",",");
    }

    public function getImporteSaldoFormatAttribute(){
        return "$" . number_format(($this->cantidad*$this->precio_unitario)-$this->importe_total_estimado,3, ".",",");
    }

    public function getConceptoPathAttribute()
    {
        try{
            return $this->destino->concepto->path;
        } catch(\Exception $e){
            return null;
        }
    }

    public function getConceptoPathCortaAttribute()
    {
        try{
            return $this->destino->concepto->path_corta;
        } catch(\Exception $e){
            return null;
        }

    }

    public function getCantidadOriginal($id_solicitud)
    {
        return $this->valoresOriginales()->where("id_solicitud","=",$id_solicitud)->first()->cantidad;
    }

    public function getCantidadOriginalFormat($id_solicitud)
    {
        return number_format($this->getCantidadOriginal($id_solicitud),2,".",",");
    }

    public function getPrecioUnitarioOriginal($id_solicitud)
    {
        return $this->valoresOriginales()->where("id_solicitud","=",$id_solicitud)->first()->precio_unitario;
    }

    public function getImporteOriginal($id_solicitud)
    {
        return $this->getCantidadOriginal($id_solicitud) * $this->getPrecioUnitarioOriginal($id_solicitud);
    }

    public function getImporteOriginalFormat($id_solicitud)
    {
        return "$".number_format($this->getImporteOriginal($id_solicitud),2,".",",");
    }

    /**
     * Función para obtener cifras utilizadas en estimaciones (creación, edición, consulta)
     * @return array
     */
    public function partidasEstimadasSinContexto($id_estimacion, $id_contrato, $contrato, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $estimacion = ItemEstimacion::withoutGlobalScopes()->where('id_transaccion', '=', $id_estimacion)
            ->where('id_antecedente', $this->id_transaccion)
            ->where('item_antecedente', $this->id_concepto)->first();
        $precio_unitario = $estimacion ? $estimacion->precio_unitario : $this->precio_unitario;
        $cantidad_estimada_total = $this->cantidad_total_estimada ? $this->cantidad_total_estimada : 0;
        $cantidad_estimado_anterior = $estimacion ?  $cantidad_estimada_total - $estimacion->cantidad : $cantidad_estimada_total;
        $porcentaje_avance = $this->cantidad > 0? $cantidad_estimado_anterior / $this->cantidad:0;
        $porcentaje_estimado = 0;
        $estimacion && $this->cantidad > 0 ? $porcentaje_estimado = $estimacion->cantidad  / $this->cantidad:'';
        $destino = Destino::where('id_transaccion', '=', $id_contrato)->where('id_concepto_contrato', '=', $contrato->id_concepto)->first();
        return array(
            'id' => $this->id_item,
            'id_concepto' => $this->id_concepto,
            'unidad' => $contrato->unidad,
            'clave' => $contrato->clave,
            'descripcion_concepto' => $contrato->descripcion,
            'cantidad_subcontrato' => $this->cantidad,
            'precio_unitario_subcontrato' => $this->precio_unitario,
            'importe_subcontrato' => $this->cantidad * $this->precio_unitario,
            'precio_unitario_subcontrato_format' => $this->precio_unitario_format,
            'id_item_estimacion' =>  $estimacion ? $estimacion->id_item : 0,
            'cantidad_estimacion' => $estimacion ? number_format($estimacion->cantidad, 2, '.', '') : 0,
            'porcentaje_avance' => (float) number_format((($porcentaje_avance) * 100), 2, '.', ''),
            'cantidad_estimada_total' => (float) number_format($cantidad_estimada_total ? $cantidad_estimada_total : 0,2,'.', ''),
            'cantidad_estimada_anterior' => (float) number_format($cantidad_estimado_anterior,2,'.', ''),
            'importe_estimado_anterior' => (float) number_format(($cantidad_estimado_anterior * $precio_unitario),2,'.', ''),
            'importe_acumulado' => (float) number_format((($cantidad_estimada_total ? $cantidad_estimada_total : 0) * $precio_unitario),2,'.', ''),
            'cantidad_por_estimar' => (float) number_format($this->cantidad -$cantidad_estimado_anterior,2,'.', ''),
            'importe_por_estimar' => (float) number_format((($this->cantidad - $cantidad_estimado_anterior) * $precio_unitario),2,'.', ''),
            'porcentaje_estimado' => (float) number_format((($porcentaje_estimado) * 100), 2, '.', ''),
            'importe_estimacion' => $estimacion ? number_format($estimacion->importe, 2, '.', '') : 0,
            'importe_estimacion_format' => $estimacion ? number_format($estimacion->importe, 2, '.', ',') : 0,
            'destino_path' => $destino->concepto_sgv->path_corta_proveedor,
            'destino_path_larga' => $destino->concepto_sgv->path_sgv,
            'id_destino' => $destino->id_concepto,
            'cantidad_addendum' => "",
            'precio_modificado' => 0,
        );
    }

    public function partidasAvanceSubcontrato($avance)
    {
        $avance = ItemAvanceSubcontrato::where('id_transaccion', '=', $avance->id_transaccion)->where('id_concepto', $this->id_concepto)->first();
        $precio_unitario = $avance ? $avance->precio_unitario : $this->precio_unitario;
        $cantidad_estimada_total = $this->cantidad_total_estimada ? $this->cantidad_total_estimada : 0;
        $cantidad_estimado_anterior = $avance ?  $cantidad_estimada_total - $avance->cantidad : $cantidad_estimada_total;
        $porcentaje_avance = $this->cantidad > 0? $cantidad_estimado_anterior / $this->cantidad:0;
        $porcentaje_estimado = 0;
        $avance && $this->cantidad > 0 ? $porcentaje_estimado = $avance->cantidad  / $this->cantidad:'';

        return array(
            'id' => $this->id_item,
            'id_concepto' => $this->id_concepto,
            'unidad' => $this->contrato->unidad,
            'clave' => $this->contrato->clave,
            'descripcion_concepto' => $this->contrato->descripcion,
            'cantidad_subcontrato' => $this->cantidad,
            'precio_unitario_subcontrato' => $this->precio_unitario,
            'importe_subcontrato' => $this->cantidad * $this->precio_unitario,
            'precio_unitario_subcontrato_format' => $this->precio_unitario_format,
            'id_item_avance' =>  $avance ? $avance->id_item : 0,
            'cantidad_avance' => $avance ? number_format($avance->cantidad, 2, '.', '') : 0,
        );
    }
}
