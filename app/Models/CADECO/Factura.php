<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:09 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Item;
use App\Models\CADECO\Cambio;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Subcontrato;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\ItemOrdenCompra;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\CADECO\ItemEntradaAlmacen;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Finanzas\FacturaEliminada;
use App\Models\CADECO\Finanzas\TransaccionRubro;
use App\Models\CADECO\Finanzas\ComplementoFactura;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Http\Transformers\CADECO\Contrato\EstimacionTransformer;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;

class Factura extends Transaccion
{
    public const TIPO_ANTECEDENTE = 67;
    public const OPCION_ANTECEDENTE = 0;
    public const TIPO = 65;
    public const NOMBRE = "Factura";
    public const ICONO = "fa fa-file-invoice";
    protected $fillable = [
        'fecha',
        "id_empresa",
        "id_moneda",
        "vencimiento",
        'monto',
        "saldo",
        "referencia",
        "observaciones",
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 65)
                /*->where('estado', '!=', -2)*/;
        });
    }

    public function getRubroAttribute()
    {
        if ($this->transaccion_rubro) {
            return $this->transaccion_rubro->rubro->descripcion;
        }
    }

    public function transaccion_rubro()
    {
        return $this->hasOne(TransaccionRubro::class, "id_transaccion", "id_transaccion");
    }

    public function complemento()
    {
        return $this->hasOne(ComplementoFactura::class, "id_transaccion", "id_transaccion");
    }

    public function contra_recibo()
    {
        return $this->belongsTo(ContraRecibo::class, 'id_antecedente', 'id_transaccion');
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'id_transaccion', 'IDTransaccionCDC');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function ordenesPago()
    {
        return $this->hasMany(OrdenPago::class, 'id_referente', 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function items()
    {
        return $this->hasMany(ItemFactura::class, 'id_transaccion', 'id_transaccion');
    }

    public function pagos()
    {
        return $this->hasManyThrough(PagoFactura::class, OrdenPago::class, 'id_referente', 'numero_folio', 'id_transaccion', 'id_transaccion');
    }

    public function facturaRepositorio()
    {
        return $this->hasOne(FacturaRepositorio::class, 'id_transaccion', 'id_transaccion')
            ->where('rfc_emisor', '=',$this->empresa->rfc)
            ->where('id_proyecto', '=', Proyecto::query()->where('base_datos', '=', Context::getDatabase())
                ->first()->getKey());
    }

    public function facturasRepositorioLiberar()
    {
        return $this->hasMany(FacturaRepositorio::class, 'id_transaccion', 'id_transaccion')
            ->where('id_proyecto', '=', Proyecto::query()->where('base_datos', '=', Context::getDatabase())
                ->first()->getKey());
    }

    public function poliza()
    {
        return $this->belongsTo(Poliza::class, 'id_transaccion', 'id_transaccion_sao');
    }

    public function polizas(){
        return $this->hasMany(Poliza::class, 'id_transaccion_sao', 'id_transaccion');
    }

    public function tipoCambioFecha(){
        return $this->hasMany(Cambio::class, 'fecha', 'fecha');
    }

    public function subcontrato(){
        return $this->hasMany(Subcontrato::class, 'id_empresa', 'id_empresa')
                ->whereIn('estado', [-1,0,1]);
    }

    public function estimacion(){
        return $this->hasMany(Estimacion::class, 'id_empresa', 'id_empresa')
                ->whereIn('estado', [-1,1]);
    }

    private function registrarCR($data)
    {
        $cr = ContraRecibo::create($data["cr"]);
        if (!$cr) {
            abort(400, "Hubo un error al registrar el contrarecibo");
        }
        return $cr;
    }

    /*public function transaccionesRevisadas()
    {
        return $this->hasManyThrough(Transaccion::class,FacturaPartida::class,"id_transaccion","id_transaccion","id_transaccion","id_antecedente")
            ->distinct();
    }*/

    public function prepolizaActiva(){
        return $this->polizas()->orderBy('estatus', 'DESC')->first();
    }

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = Factura::NOMBRE;
        $datos["tipo_numero"] = Factura::TIPO;
        $datos["icono"] = Factura::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getTransaccionesRevisadasAttribute()
    {
        /*NO SE USA RELACIÓN ELOQUENT PORQUE HAY CONFLICTOS CON LA SOBREESCRITURA DEL CAMPO id_transaccion*/
        $transacciones_arr = [];
        $transacciones = null;
        foreach ($this->items as $item){
            if($item->antecedente){
                $transacciones_arr[] = $item->antecedente;
            }
        }

        if(count($transacciones_arr)>0){
            $transacciones =  collect($transacciones_arr)->unique();
        }

        return $transacciones;
    }

    private function registrarComplemento($factura)
    {
        $complemento = $factura->complemento()->create(["id_transaccion" => $factura->id_transaccion]);
        if (!$complemento) {
            abort(400, "Hubo un error al registrar el complemento");
        }
    }

    private function registrarRubro($factura, $data)
    {
        $transaccion_rubro = $factura->transaccion_rubro()->create($data["rubro"]);
        if (!$transaccion_rubro) {
            abort(400, "Hubo un error al registrar el complemento");
        }
    }

    public function validarEstado()
    {
        if($this->estado == 1)
        {
            throw New \Exception("No se puede eliminar la factura debido a que ya se encuentra Revisada");
        }

        if($this->estado == 2)
        {
            throw New \Exception("No se puede eliminar la factura debido a que ya se encuentra Pagada");
        }
    }

    private function registrarCFDRepositorio($factura, $data)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=",$data["uuid"])->first();
        if($factura_repositorio){
            $factura_repositorio->id_transaccion = $factura->id_transaccion;
            $factura_repositorio->save();

        } else {
            if($data){
                $factura_repositorio = $factura->facturaRepositorio()->create($data);
                if (!$factura_repositorio) {
                    abort(400, "Hubo un error al registrar el CFD en el repositorio");
                }
            }
        }
    }

    public function registrar($data)
    {
        $factura = null;
        try {
            DB::connection('cadeco')->beginTransaction();
            $cr = $this->registrarCR($data);
            $factura = $cr->facturas()->create($data["factura"]);
            $this->registrarComplemento($factura);
            $this->registrarRubro($factura, $data);
            $this->registrarCFDRepositorio($factura, $data["factura_repositorio"]);
            if($data["nc_repositorio"]){
                $this->registrarCFDRepositorio($factura, $data["nc_repositorio"]);
            }
            DB::connection('cadeco')->commit();
            return $factura;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function eliminarFactura($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $contrarecibo = $this->contra_recibo;
            $id_factura = $this->id_transaccion;
            $this->delete();
            $contrarecibo->delete();
            $factura_eliminada = FacturaEliminada::find($id_factura);
            $factura_eliminada->motivo_elimino = $motivo;
            $factura_eliminada->save();
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function desvinculaFacturaRepositorio()
    {
        if ($this->facturasRepositorioLiberar) {
            foreach ($this->facturasRepositorioLiberar as $cfd_repositorio){
                $cfd_repositorio->id_transaccion = null;
                $cfd_repositorio->id_proyecto = null;
                $cfd_repositorio->id_obra = null;
                $cfd_repositorio->usuario_asocio = null;
                $cfd_repositorio->fecha_hora_asociacion = null;
                $cfd_repositorio->save();
            }
        }
    }

    public function validarEliminacion()
    {
        if ($this->poliza) {
            if ($this->poliza->estatus != -3) {
                throw New \Exception("La factura se encuentra asociada a la Prepoliza: #" . $this->poliza->id_int_poliza);
            };
        }

    }

    public function generaOrdenPago($data)
    {
        try {
            // TODO: Obtener el monto de los pagos relacionados a la factura para determinar si se debe actualizar el estado
            DB::connection('cadeco')->beginTransaction();
            $cuenta_cargo = Cuenta::find($data["id_cuenta_cargo"]);
            $saldo_esperado = $this->saldo - ($data["monto_pagado_transaccion"]);
            $saldo_esperado_cuenta = $cuenta_cargo->saldo_real - ($data["monto_pagado"]);

            $datos = [
                'id_antecedente' => $this->id_antecedente,
                'id_referente' => $this->id_transaccion,
                'monto' => -1 * abs($data["monto_pagado_transaccion"]),
                'tipo_cambio' => $data["tipo_cambio"],
                'fecha' => $data["fecha_pago"],
                'id_empresa' => $this->id_empresa,
                'id_moneda' => $this->id_moneda,
            ];
            $ordenPago = OrdenPago::create($datos);
            $pago = $ordenPago->generaPago($data);

            $this->validaSaldos($saldo_esperado, $saldo_esperado_cuenta, $pago);
            DB::connection('cadeco')->commit();
            return $pago;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }

    }

    public function scopePendientePago($query)
    {
        return $query->where('estado', '=', 1)
            ->where('saldo', '>', 0.99);
    }

    public function scopeConDocumento($query)
    {
        return $query->has('documento');
    }

    public function getAutorizadoAttribute()
    {
        $pagar = $this->monto * $this->tipo_cambio;
        return '$ ' . number_format($pagar, 2);
    }

    public function getEstadoStringAttribute()
    {
        $estado = "";
        if ($this->estado == 0) {
            $estado = 'Registrada';
        } elseif ($this->estado == 1 && abs($this->ordenesPago->sum('monto')) < 1) {
            $estado = 'Revisada';
        } elseif ($this->estado == 1 && abs($this->monto + $this->ordenesPago->sum('monto')) > 1) {
            $estado = 'Saldo Pendiente ';
        } elseif ($this->estado == 2) {
            $estado = 'Pagada';
        }
        return $estado;
    }

    public function getACuentaFormatAttribute()
    {
        return '$ ' . number_format(abs($this->ordenesPago->sum('monto')), 2, ".", ",");
    }

    public function getSaldoFormatAttribute()
    {
        return '$ ' . number_format(abs($this->saldo), 2, ".", ",");
    }

    public function getTipoTransaccionStringAttribute()
    {
        if ($this->opciones == 0) {
            $tipo = 'Factura';
        }
        if ($this->opciones == 1) {
            $tipo = 'Gastos Varios';
        }
        if ($this->opciones == 65537) {
            $tipo = 'Materiales / Servicios';
        }
        return $tipo;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        #FACTURA
        $factura = $this;
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        if($this->transacciones_revisadas){
            foreach ($this->transacciones_revisadas as $transaccion_revisada) {
                if ($transaccion_revisada) {
                    if ($transaccion_revisada->tipo_transaccion == 52) {
                        $estimacion = Estimacion::find($transaccion_revisada->id_transaccion);
                        if($estimacion){
                            foreach ($estimacion->relaciones as $relacion) {
                                if ($relacion["tipo_numero"] != 65) {
                                    $relaciones[$i] = $relacion;
                                    $relaciones[$i]["consulta"] = 0;
                                    $i++;
                                }
                            }
                        }
                    } else if ($transaccion_revisada->tipo_transaccion == 51) {
                        $subcontrato = Subcontrato::find($transaccion_revisada->id_transaccion);
                        if($subcontrato){
                            foreach ($subcontrato->relaciones as $relacion) {
                                if ($relacion["tipo_numero"] != 65) {
                                    $relaciones[$i] = $relacion;
                                    $relaciones[$i]["consulta"] = 0;
                                    $i++;
                                }
                            }
                        }
                    } else if ($transaccion_revisada->tipo_transaccion == 33 && $transaccion_revisada->opciones == 1) {
                        $entrada = EntradaMaterial::find($transaccion_revisada->id_transaccion);
                        foreach ($entrada->relaciones as $relacion) {
                            if ($relacion["tipo_numero"] != 65) {
                                $relaciones[$i] = $relacion;
                                $relaciones[$i]["consulta"] = 0;
                                $i++;
                            }
                        }
                    } else if ($transaccion_revisada->tipo_transaccion == 19 && $transaccion_revisada->opciones == 1) {
                        $orden_compra = OrdenCompra::find($transaccion_revisada->id_transaccion);
                        foreach ($orden_compra->relaciones as $relacion) {
                            if ($relacion["tipo_numero"] != 65) {
                                $relaciones[$i] = $relacion;
                                $relaciones[$i]["consulta"] = 0;
                                $i++;
                            }
                        }
                    }
                }

            }

        } else {
            #POLIZA DE FACTURA
            if ($factura->poliza) {
                $relaciones[$i] = $factura->poliza->datos_para_relacion;
                $i++;
            }
            #PAGO DE FACTURA
            foreach ($factura->ordenesPago as $orden_pago) {
                if ($orden_pago->pago) {
                    $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                    $i++;
                    #POLIZA DE PAGO DE FACTURA
                    if ($orden_pago->pago->poliza) {
                        $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                        $i++;
                    }
                }
            }
        }

        $orden1 = array_column($relaciones, 'orden');

        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    private function validaSaldos($saldo_esperado, $saldo_esperado_cuenta, $pago)
    {
        $this->refresh();
        $pago->load("cuenta");
        if (abs($saldo_esperado_cuenta - $pago->cuenta->saldo_real) > 1) {
            abort(400, 'Hubo un error durante la actualización del saldo de la cuenta por el pago de la factura.');
        }
        if (abs($saldo_esperado - $this->saldo) > 1) {
            abort(400, 'Hubo un error durante la actualización del saldo de la factura');
        }
    }

    public function disminuyeSaldo(Transaccion $pago)
    {
        $this->saldo = number_format($this->saldo - ($pago->orden_pago->monto * -1), 2, ".", "");
        $this->save();
        if ($this->saldo < 1) {
            $this->actualizaEstadoPagada();
        }
    }

    public function actualizaEstadoPagada()
    {
        $this->estado = 2;
        $this->save();
    }

    public function getFactorIvaAttribute()
    {
        if (($this->monto - $this->impuesto) > 0) {
            return $this->monto / ($this->monto - $this->impuesto);
        } else {
            return 1;
        }
    }

    public function getFondoGarantiaAttribute(){
        $antecedente = $this->partidas->pluck('id_antecedente');
        if($estimaciones = Estimacion::whereIn('id_transaccion', $antecedente)->get()){
            $fondo = 0;
            foreach($estimaciones as $estimacion){
                $fondo += $estimacion->retencion_fondo_garantia_orden_pago;
            }
            return $fondo;
        }

        return null;
    }

    public function getFondoGarantiaFormatAttribute(){
        return '$ ' . number_format($this->fondo_garantia, 2);
    }

    public function getRetencionesSubcontratoAttribute(){
        $antecedente = $this->partidas->pluck('id_antecedente');
        if($estimaciones = Estimacion::whereIn('id_transaccion', $antecedente)->get()){
            $retencion = 0;
            foreach($estimaciones as $estimacion){
                $retencion += $estimacion->suma_retenciones;
            }
            return $retencion;
        }

        return null;
    }

    public function getRetencionesSubcontratoFormatAttribute(){
        return '$ ' . number_format($this->retenciones_subcontrato, 2);
    }

    public function getDevolucionesSubcontratoAttribute(){
        $antecedente = $this->partidas->pluck('id_antecedente');
        if($estimaciones = Estimacion::whereIn('id_transaccion', $antecedente)->get()){
            $liberaciones = 0;
            foreach($estimaciones as $estimacion){
                $liberaciones += $estimacion->suma_liberaciones;
            }
            return $liberaciones;
        }

        return null;
    }

    public function getDevolucionesSubcontratoFormatAttribute(){
        return '$ ' . number_format($this->devoluciones_subcontrato, 2);
    }

    public function getObservacionesFormatAttribute(){
        return preg_replace( "/\r|\n/", "", $this->observaciones );
    }

    public function revertir()
    {
        DB::connection('cadeco')->update("EXEC [dbo].[sp_revertir_transaccion] {$this->id_transaccion}");
    }

    public function validarPrepoliza(){
        if(!$this->polizas && $this->estado > 0 ){
            DB::connection('cadeco')->update("[Contabilidad].[generaPolizaFactura] {$this->id_transaccion}");
            return $this->find($this->id_transaccion);
        }else if($this->polizas && $this->estado > 0){
            $diferente = false;
            foreach($this->polizas->pluck('estatus') as $estatus){
                if($estatus != -3){
                    $diferente = true;
                }
            }
            if(!$diferente){
                DB::connection('cadeco')->update("[Contabilidad].[generaPolizaFactura] {$this->id_transaccion}");
                return $this->find($this->id_transaccion);
            }
        }
        return $this;
    }

    public function getDocumentos(){
        $pendientes = $this->getItemsPendientes();
        $anticipos = $this->getItemsAnticipos();
        $subcontratos = $this->getItemsSubcontratos();
        $renta = $this->getItemsRentas();
        $lista = $this->getitemsLista();
        $descuentos = $this->getItemsDescuentos();
        return [
            'pendientes' => $pendientes,
            'anticipos' => $anticipos,
            'subcontratos' => $subcontratos,
            'renta' => $renta,
            'lista' => $lista,
            'descuentos' => $descuentos,
        ];
    }

    public function getItemsSubcontratos(){
        $items_subc = DB::connection('cadeco')->select(DB::raw("
        SELECT
        [transacciones].[id_transaccion] as id_item,
        [transacciones].[tipo_transaccion] as tipo_transaccion
      , [Subcontratos].[id_transaccion]  as id_transaccion_antecedente
      , [transacciones].[id_moneda] 	
      , CASE [transacciones].[tipo_transaccion] 
           WHEN 52 THEN 'EST #' + CAST([transacciones].[numero_folio] AS VARCHAR(10)) 
           WHEN 51 THEN 'SUB #' + CAST([transacciones].[numero_folio] AS VARCHAR(10)) 
        END transaccion
      , CASE WHEN [transacciones].[tipo_transaccion] = 51 THEN [transacciones].[referencia]
           ELSE [Subcontratos].[referencia]
        END subcontrato	
      , CONVERT(VARCHAR(10), [transacciones].[cumplimiento], 105)  AS desde
      , CONVERT(VARCHAR(10), [transacciones].[vencimiento], 105)   AS hasta
      , CASE WHEN [transacciones].[tipo_transaccion] = 52 THEN (
      ( 
          CONVERT(VARCHAR(100), CAST(( select sum(importe) from items where id_transaccion = [transacciones].[id_transaccion] ) - [transacciones].[autorizado] AS MONEY), 1)
      ) )
              ELSE CONVERT(VARCHAR(100), CAST([transacciones].[anticipo_saldo] AS MONEY), 1)
          END monto
      , CASE WHEN [transacciones].[tipo_transaccion] = 52 THEN (
      ( 
          ( select sum(importe) from items where id_transaccion = [transacciones].[id_transaccion] ) - [transacciones].[autorizado] 
      ) )
              ELSE [transacciones].[anticipo_saldo] 
          END monto_sf    
      FROM
          [dbo].[transacciones]
      INNER JOIN [dbo].[transacciones] AS Subcontratos
              ON [transacciones].[id_antecedente] = [subcontratos].[id_transaccion]
      WHERE
          [transacciones].[tipo_transaccion] IN ( 51, 52 )
          AND (([transacciones].[estado] IN ( -1, 1 ) AND transacciones.tipo_transaccion = 52) OR
      ([transacciones].[estado] IN ( -1, 0, 1 ) AND transacciones.tipo_transaccion = 51)) 
          AND [transacciones].[id_obra] = ".Context::getIdObra()." 
          AND [transacciones].[id_empresa] = ".$this->id_empresa." 
          AND  (
              SELECT 
                  CASE WHEN [transacciones].[tipo_transaccion] = 52 THEN ( ABS(( [transacciones].[saldo] - [transacciones].[impuesto] ) - [transacciones].[autorizado]) )
                  ELSE [transacciones].[anticipo_saldo]
                  END MontoOriginal
               ) >0 
          AND  (
              SELECT 
                  CASE WHEN [transacciones].[tipo_transaccion] = 52 THEN ( [transacciones].[autorizado] )
                  else 0
                  END MontoAutorizado
               ) = 0     
      ORDER BY
          [transacciones].[tipo_transaccion] DESC
      , [transacciones].[numero_folio]
        "));
        $resp_items = json_decode(json_encode($items_subc), true);
        $items = [];
        foreach($resp_items as $item){
            $subcTransformer=  new SubcontratoTransformer();
            $estmTransformer=  new EstimacionTransformer();
            if($item['tipo_transaccion'] == 51){
                $subc = Subcontrato::find($item['id_item']);
                $items[] = $subcTransformer->transform($subc);
            }
            if($item['tipo_transaccion'] == 52){
                $estim = Estimacion::find($item['id_item']);
                $items[] = $estmTransformer->transform($estim);
            }
        }
        
        return $items;
    }

    public function getItemsPendientes(){
        $items_pend = DB::connection('cadeco')->select(DB::raw("SELECT
                    r.[id_transaccion] 
                    ,i.unidad
                    , r.id_item
                    , r.[referencia] as remision
                    , CONVERT(VARCHAR(10), r.[fecha], 105) AS fecha
                    , r.[descripcion] AS insumo
                    , r.[cantidad] AS cantidad
                    , CONVERT(VARCHAR(100), CAST(r.precio_unitario AS MONEY), 1) AS precio
                    , CONVERT(VARCHAR(100), CAST(r.[cantidad] * r.[precio_unitario] AS MONEY), 1) AS monto_original
                    , r.[cantidad] * r.[precio_unitario] as monto_original_sf
                    , r.id_moneda
                    , r.anticipo
                    , ((r.[cantidad] * r.[precio_unitario]) - r.anticipo) as monto_sf
                    , CONVERT(VARCHAR(100), CAST((r.[cantidad] * r.[precio_unitario]) - r.anticipo AS MONEY), 1) AS monto
                FROM
                    [dbo].[RemisionesPorFacturar] r
                    join items i on  i.id_item = r.id_item 
                WHERE
                r.[id_obra] = ".Context::getIdObra()."  AND
                r.[tipo_transaccion] = 33 AND
                r.[id_empresa] = ".$this->id_empresa." AND
                r.[opciones] = 1 AND
                r.[estado] = 0
                ORDER BY
                r.[referencia] 
                    , r.[descripcion]"));

        $resp_items = json_decode(json_encode($items_pend), true);
        $item_final = [];
        foreach($resp_items as $item){$item['seleccionado'] = false; $item_final[] = $item;}
        return $item_final;

    }

    public function getItemsAnticipos(){
        $items_ant = DB::connection('cadeco')->select(DB::raw("SELECT
                    [id_item], 'false' as seleccionado
                    , [id_transaccion]
                    , CASE [opciones]
                        WHEN 8 THEN 'O/R #' + CAST([numero_folio] AS VARCHAR(10)) 
                        WHEN 1 THEN 'O/C #' + CAST([numero_folio] AS VARCHAR(10)) 
                    END  transaccion
                    , CONVERT(VARCHAR(10), [fecha], 105) AS fecha
                    , CASE [opciones]
                        WHEN 8 THEN 'Anticipo ' + CAST([anticipo] AS VARCHAR(10)) + '% Sobre '
                            + CAST([numero] AS VARCHAR(10)) + 'Tipo' + [descripcion] + ' ( '
                            + CAST([cantidad] AS VARCHAR(MAX)) + ' ' + [unidad] + ' @'
                            + CAST([precio_unitario] AS VARCHAR(MAX)) + ' )' 
                        WHEN 1 THEN 'Anticipo ' + CAST([anticipo] AS VARCHAR(10)) + '% de '
                            + [descripcion] + ' ( '
                            + CAST([cantidad] AS VARCHAR(MAX)) + ' ' + [unidad] + ' @'
                            + CAST([precio_unitario] AS VARCHAR(MAX)) + ' )'  
                        END  'descripcion_item'
                    , CONVERT(VARCHAR(100), CAST([anticipo_total] - [anticipo_facturado] AS MONEY), 1) AS 'anticipo'
                    , [anticipo_total] - [anticipo_facturado] as anticipo_sf
                    , id_moneda
                FROM
                        [AnticiposPorFacturar]
                WHERE
                    [anticipo_total] - [anticipo_facturado] > 0
                    AND [id_obra] = ".Context::getIdObra()."
                    AND [id_empresa] = ".$this->id_empresa."
                ORDER BY
                    [descripcion]"));
        
        $resp_items = json_decode(json_encode($items_ant), true);
        $item_final = [];
        foreach($resp_items as $item){$item['seleccionado'] = false; $item_final[] = $item;}
        return $item_final;
        
    }

    public function getItemsRentas(){
        $items_rentas = DB::connection('cadeco')->select(DB::raw("SELECT
                    [RentasPorFacturar].[id_item], 'false' as seleccionado
                , [RentasPorFacturar].[descripcion] AS equipo  
                , [RentasPorFacturar].[referencia] AS numero_serie
                , [RentasPorFacturar].[monto_total]-([RentasPorFacturar].[monto_pagado] + [RentasPorFacturar].[monto_facturado]) AS importe_total_sf
                , CONVERT(VARCHAR(100), CAST([RentasPorFacturar].[monto_total]-([RentasPorFacturar].[monto_pagado] + [RentasPorFacturar].[monto_facturado]) AS MONEY), 1) AS importe_total
                , ([RentasPorFacturar].[monto_total]-([RentasPorFacturar].[monto_pagado] + [RentasPorFacturar].[monto_facturado]))/[RentasPorFacturar].[precio_unitario] AS rentas
                , [RentasPorFacturar].[unidad]
                , t.id_moneda
                FROM
                    [dbo].[RentasPorFacturar] join items as i on(i.id_item = [RentasPorFacturar].id_item) 
            JOIN transacciones as t on(t.id_transaccion = i.id_transaccion)
                WHERE
                    [RentasPorFacturar].[monto_total]-([RentasPorFacturar].[monto_pagado] + [RentasPorFacturar].[monto_facturado]) > 0
                    AND ([RentasPorFacturar].[monto_total]-([RentasPorFacturar].[monto_pagado] + [RentasPorFacturar].[monto_facturado]))/[RentasPorFacturar].[precio_unitario] > 0
                    AND [RentasPorFacturar].[id_obra] = ".Context::getIdObra()."
                    AND [RentasPorFacturar].[id_empresa] =  ".$this->id_empresa."
                ORDER BY
                    [RentasPorFacturar].[descripcion]
                ,   [RentasPorFacturar].[referencia]"));

        $resp_items = json_decode(json_encode($items_rentas), true);
        $item_final = [];
        foreach($resp_items as $item){$item['seleccionado'] = false; $item_final[] = $item;}
        return $item_final;
    }

    public function getitemsLista(){
        $items_lista = DB::connection('cadeco')->select(DB::raw("SELECT
                [id_transaccion] as id_item, 'false' as seleccionado
                , CASE [tipo_transaccion]
                    WHEN 99 THEN 'L/R: ' + CAST([referencia] AS VARCHAR(MAX))
                    WHEN 102 THEN 'PRE'
                    END referencia
                , [numero_folio] AS folio
                , CONVERT(VARCHAR(10), [fecha], 105) AS fecha
                , CONVERT(VARCHAR(100), CAST(saldo AS MONEY), 1) AS importe_total
                , saldo AS importe_total_sf
                , 1 as id_moneda
            FROM
                [dbo].[transacciones]
            WHERE
                [tipo_transaccion] IN ( 99, 102 )
                AND [estado] != 2 
                AND [id_obra] = ".Context::getIdObra()." 
                AND [id_empresa] = ".$this->id_empresa." "));
        
        $resp_items = json_decode(json_encode($items_lista), true);
        $item_final = [];
        foreach($resp_items as $item){$item['seleccionado'] = false; $item_final[] = $item;}
        return $item_final;
    }

    public function getItemsDescuentos(){
        $items_desc = DB::connection('cadeco')->select(DB::raw("SELECT
                id_vario as id_item, 'false' as seleccionado, 0 as monto_revision,
                descripcion as concepto,
                case mascara when 12288 then 'Recargo' when 8192 then 'Descuento' end naturaleza,
                mascara
            FROM 
                [varios]
            WHERE 
                [mascara] IN (12288,8192) AND 
                id_obra = ".Context::getIdObra()." "));
        
        $resp_items = json_decode(json_encode($items_desc), true);
        $item_final = [];
        foreach($resp_items as $item){$item['seleccionado'] = false; $item_final[] = $item;}
        return $item_final;
    }

    public function storeRevision($data){
        try {
            DB::connection('cadeco')->beginTransaction();
            foreach($data['pendientes'] as $pendiente){
                $item_entrada = ItemEntradaAlmacen::find($pendiente['id_item']);
                $item = Item::create([
                    "id_transaccion" => $this->id_transaccion,
                    "id_antecedente" => $pendiente['id_transaccion'],
                    "item_antecedente" => $pendiente['id_item'],
                    "importe" => $pendiente['monto_sf'],
                    "saldo" => 0,
                    "id_material" => $item_entrada->id_material,
                    "numero" => 0,
                    "cantidad" => $item_entrada->cantidad,
                    "unidad" =>  $item_entrada->unidad,
                    "precio_unitario" => $item_entrada->precio_unitario,
                    "anticipo" => $item_entrada->anticipo,
                ]);

                $item_entrada->estado = 1;
                $item_entrada->save();
            }

            foreach($data['anticipos'] as $anticipo){
                $item_oc = ItemOrdenCompra::find($anticipo['id_item']);
                $item = Item::create([
                    "id_transaccion" => $this->id_transaccion,
                    "id_antecedente" => $anticipo['id_transaccion'],
                    "item_antecedente" => $anticipo['id_item'],
                    "importe" => $anticipo['anticipo_sf'],
                    "id_material" => $item_sao->id_material,
                    "numero" => 2,
                    "cantidad" => 0,
                ]);
            }

            foreach($data['subcontratos'] as $subcontrato){
                $item = Item::create([
                    "id_transaccion" => $this->id_transaccion,
                    "id_antecedente" => $subcontrato['id'],
                    "importe" => $subcontrato['monto_revision'],
                    "saldo" => $subcontrato['monto_revision'],
                    "numero" => 1,
                ]);
                if($subcontrato['tipo_transaccion'] == 52){
                    $estimacion_ = Estimacion::find($subcontrato['id']);
                    $estimacion_->estado = 2;
                    $estimacion_->autorizado = $subcontrato['monto_revision'];
                }
                if($subcontrato['tipo_transaccion'] == 52){
                    $subcont = Subcontrato::find($subcontrato['id']);
                    $subcont->estado = 1;
                    $subcont->anticipo_saldo = 0;
                }
                
                $resp = DB::connection('cadeco')->select(DB::raw("DECLARE @RC int
                EXECUTE @RC = [sp_aplicar_anticipos] 
                @id_item = $item->id_item
                SELECT	'res' = @RC "));

                if($resp->rs != 0){
                    abort(403, 'Error al aplicar anticipos.');
                }

            }

            foreach($data['renta'] as $renta){
                $item_renta = ItemEntradaMaquinaria::find($renta['id_item']);
                $item = Item::create([
                    "id_transaccion" => $this->id_transaccion,
                    "id_antecedente" => 0,
                    "item_antecedente" =>  $renta['id_item'],
                    "id_material" => $item_renta->id_material,
                    "cantidad" => $renta['cantidad'],
                    "importe" => $item_renta->precio_unitario * $renta['cantidad'],
                    "saldo" => 0,
                    "numero" => 3,
                    "precio_unitario" => $item_renta->precio_unitario,
                ]);

            }

            foreach($data['lista'] as $lista){
                $item_trns = Transaccion::find($lista['id_item']);
                $item = Item::create([
                    "id_transaccion" => $this->id_transaccion,
                    "id_antecedente" => $lista['id_item'],
                    "importe" => $item_trns->monto,
                    "saldo" => $item_trns->saldo,
                    "numero" => 4,
                ]);
                if($item_trns->tipo_transacicon == 99){
                    $item_trns->estado = 2;
                    $item_trns->save();
                }
                if($item_trns->tipo_transacicon == 102){
                    if($item_trns->saldo - $lista['importe'] == 0){
                        $item_trns->estado = 2;
                    }else{
                        $item_trns->estado = 1;
                    }
                    $item_trns->saldo = $item_trns->saldo- $lista['importe'];
                    $item_trns->save();
                }
            }

            foreach($data['descuentos'] as $descuento){
                $reg_varios = Vario::create([
                    "descripcion" => $descuento['concepto'],
                    "mascara" => $descuento['mascara'],
                    "cuenta_contable"=>1,
                    "id_obra"=> Context::getIdObra(),
                    "para_estimacion"=>1
                ]);
                
                $numero = $descuento['mascara'] == 12288? 5 : 6;
                $item = Item::create([
                    "id_transaccion" => $this->id_transaccion,
                    "item_antecedente" => $reg_varios->id_vario,
                    "importe" => $descuento['monto_revision'],
                    "saldo" => $descuento['monto_revision'],
                    "numero" => $numero,
                ]);

            }

            $this->complemento->iva = $data['resumen']['iva_subtotal'];
            $this->complemento->ieps = $data['resumen']['ieps'];
            $this->complemento->imp_hosp = $data['resumen']['imp_hospedaje'];
            $this->complemento->ret_iva_4 = $data['resumen']['ret_iva_4'];
            $this->complemento->ret_iva_10 = $data['resumen']['ret_iva_23'];
            $this->complemento->ret_isr_10 = $data['resumen']['ret_isr_10'];
            $this->complemento->ret_iva_6 = $data['resumen']['ret_iva_6'];
            $this->complemento->save();

            $impuestos = $this->impuesto + $data['resumen']['ieps'] + $data['resumen']['imp_hospedaje'];
            $impuestos_retenidos = $this->impuesto + $data['resumen']['ret_iva_4'] + $data['resumen']['ret_iva_6'] + $data['resumen']['ret_iva_23'] + $data['resumen']['ret_isr_10'];
            $tipo_cambio = 1;
            $this->id_moneda == 2? $tipo_cambio = $data['resumen']['tipo_cambio']['usd']:'';
            $this->id_moneda == 3? $tipo_cambio = $data['resumen']['tipo_cambio']['eur']:'';
            $this->estado = 1;
            $this->impuesto = $impuestos;
            $this->impuesto_retenido = $impuestos_retenidos;
            $this->diferencia = $data['diferencia'];
            $this->tipo_cambio = $tipo_cambio;
            $this->observaciones = $data['factura']['observaciones'];
            $this->TcUSD = $data['resumen']['tipo_cambio']['usd'];
            $this->TcEuro = $data['resumen']['tipo_cambio']['eur'];
            $this->retencionIVA_2_3 = $data['resumen']['ret_iva_23'];
            $this->save();

            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
        
    }

}
