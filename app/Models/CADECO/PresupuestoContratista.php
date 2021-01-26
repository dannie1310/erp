<?php

namespace App\Models\CADECO;

use App\CSV\PresupuestoLayout;
use App\Facades\Context;
use App\Models\CADECO\Contratos\AsignacionSubcontratoPartidas;
use App\Models\CADECO\Contratos\PresupuestoContratistaEliminado;
use App\Models\CADECO\Subcontratos\AsignacionContratistaPartida;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;
use App\Models\IGH\Usuario;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PresupuestoContratista extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;
    public const OPCION_ANTECEDENTE = 1026;
    public const TIPO = 50;
    public const OPCION = 0;
    public const NOMBRE = "Presupuesto";
    public const ICONO = "fa fa-comments-dollar";


    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'id_empresa',
        'id_sucursal',
        'fecha',
        'monto',
        'impuesto',
        'anticipo',
        'observaciones',
        'PorcentajeDescuento',
        'TcUSD',
        'TcEuro',
        'TcLibra',
        'DiasCredito',
        'DiasVigencia',
        'tipo_transaccion',
        'estado',
        'id_moneda'
    ];

    public $searchable = [
        'fecha',
        'numero_folio',
        'empresa.razon_social',
        'contratoProyectado.referencia',
        'contratoProyectado.numero_folio'
    ];


    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 50)->whereHas('contratoProyectado');
        });
    }

    /**
     * Relaciones
     */
    public function contratoProyectado()
    {
        return $this->belongsTo(ContratoProyectado::class, 'id_antecedente', 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(PresupuestoContratistaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    /*public function asignacion()
    {
        return $this->hasOne(AsignacionContratistaPartida::class, 'id_transaccion');
    }*/

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function partidasAsignaciones()
    {
        return $this->hasMany(AsignacionContratistaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Atributos
     */

    public function getSubcontratosAttribute(){
        $subcontratos_arr = [];
        $subcontratos=[];
        $partidas_asignaciones = $this->partidasAsignaciones;
        foreach($partidas_asignaciones as $partida_asignacion){
            $subcontratos_arr[] =$partida_asignacion->asignacion->subcontrato;

        }

        if(count($subcontratos_arr)>0){
            $subcontratos =  collect($subcontratos_arr)->unique();
        }

        return $subcontratos;

    }

    public function getUsdFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcUSD),4);
    }

    public function getEuroFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcEuro),4);
    }

    public function getLibraFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcLibra),4);
    }

    public function getDolarAttribute()
    {
        return $this->tc_usd ? $this->tc_usd : Cambio::where('id_moneda','=', 2)->orderByDesc('fecha')->first()->cambio;
    }

    public function getEuroAttribute()
    {
        return $this->tc_euro ? $this->tc_euro : Cambio::where('id_moneda','=', 3)->orderByDesc('fecha')->first()->cambio;
    }

    public function getLibraAttribute()
    {
        return $this->tc_libra ? $this->tc_libra : Cambio::where('id_moneda','=', 4)->orderByDesc('fecha')->first()->cambio;
    }

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = PresupuestoContratista::NOMBRE;
        $datos["tipo_numero"] = PresupuestoContratista::TIPO;
        $datos["icono"] = PresupuestoContratista::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getFechaGuionFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d-m-Y");
    }

    public function getSumaSubtotalPartidasAttribute()
    {
        $suma = 0;
        foreach ($this->partidas as $partida) {
            $suma += $partida->precio_sin_descuento;
        }
        return $suma;
    }

    public function getDescuentoAttribute()
    {
        return $this->suma_subtotal_partidas * $this->PorcentajeDescuento/100;
    }

    public function getSubtotalConDescuentoAttribute()
    {
        return $this->suma_subtotal_partidas - $this->descuento;
    }

    public function getIvaConDescuentoAttribute()
    {
        return $this->subtotal_con_descuento * 0.16;
    }

    public function getTotalConDescuentoAttribute()
    {
        return $this->subtotal_con_descuento + $this->iva_con_descuento;
    }

    public function getIvaPartidasAttribute()
    {
        return $this->suma_subtotal_partidas * 0.16;
    }

    public function getTotalPartidasAttribute()
    {
        return $this->suma_subtotal_partidas + $this->iva_Partidas;
    }

    public function getContratosAttribute()
    {
        $partidas = $this->partidas;
        $niveles = [];
        foreach($partidas as $partida){
            $nivel = $partida->concepto->nivel;
            $niveles[] = $partida->concepto->nivel;
            for($i = 1; $i < strlen($nivel)/4; $i++)
            {
                $niveles[] = substr($nivel, 0, 4*$i);
            }
        }
        $niveles = array_unique($niveles);

        $conceptos = Contrato::whereIn("nivel",$niveles)->where("id_transaccion",$this->id_antecedente)->orderBy("nivel")->get();
        return $conceptos;
    }

    public function getConDescuentoPartidasAttribute()
    {
        $cantidad = $this->partidas()->where("PorcentajeDescuento",">",0)->count();
        return $cantidad>0?true:false;
    }

    public function getConMonedaExtranjeraAttribute()
    {
        $id_moneda = Obra::find(Context::getIdObra())->id_moneda;
        $cantidad = $this->partidas()->where("IdMoneda","<>",$id_moneda)->count();
        return $cantidad>0?true:false;
    }

    public function getConObservacionesPartidasAttribute()
    {
        $cantidad = $this->partidas()->where("Observaciones","<>","")->count();
        return $cantidad>0?true:false;
    }

    public function getMontoFormatAttribute()
    {
        return "$ ".number_format($this->monto,2,".",",");
    }

    public function getPorcentajeAnticipoFormatAttribute()
    {
        return number_format($this->anticipo,2,".",","). " %";
    }

    public function getPorcentajeDescuentoFormatAttribute()
    {
        return number_format($this->PorcentajeDescuento,2,".",","). " %";
    }

    public function getMonedaConversionAttribute()
    {
        return Obra::find(Context::getIdObra())->moneda->nombre;
    }

    public function  getSubtotalMcAntesDescuentoGlobalFormatAttribute()
    {
        return "$ ".number_format($this->subtotal_mc_antes_descuento_global,2,".",",");
    }

    public function getSubtotalMcAntesDescuentoGlobalAttribute()
    {
        $suma = 0;
        foreach ($this->partidas as $partida) {
            $suma += $partida->total_despues_descuento_partida_mc;
        }
        return $suma;
    }

    public function getSubtotalesPorMonedaAttribute()
    {
        $subtotales = [];
        $salida = [];
        foreach ($this->partidas as $partida) {
            $monedas [] = $partida->IdMoneda;
            if(key_exists($partida->IdMoneda, $subtotales)){
                $subtotales[$partida->IdMoneda] += $partida->importe_moneda_original ;
            } else {
                $subtotales = [$partida->IdMoneda=>$partida->importe_moneda_original];
            }

        }
        foreach($subtotales as $k=>$v){
            $salida[] = [
                "moneda"=>Moneda::find($k)->nombre,
                "subtotal_format"=>"$ ".number_format($v,2)
            ];
        }
        return $salida;
    }

    public function getColspanAttribute()
    {
        //dd($this->con_observaciones_partidas , $this->con_moneda_extranjera);

        if($this->con_moneda_extranjera && $this->con_descuento_partidas){
            $colspan = 13;
        } else if(!$this->con_descuento_partidas && !$this->con_moneda_extranjera){
            $colspan = 7;
        }
        else if($this->con_descuento_partidas && !$this->con_moneda_extranjera){
            $colspan = 10;
        }
        else if(!$this->con_descuento_partidas && $this->con_moneda_extranjera){
            $colspan = 10;
        }
        return $colspan;
    }

    /**
     * Métodos
     */
    public function datosPartidas()
    {
        $items = array();
        foreach($this->partidas as $partida)
        {
            $items[] = array(
                'id_concepto' => $partida->id_concepto,
                'precio_unitario' => $partida->precio_unitario_convert,
                'no_cotizado' => $partida->no_cotizado,
                'PorcentajeDescuento' => $partida->PorcentajeDescuento,
                'IdMoneda' => $partida->IdMoneda,
                'Observaciones' => $partida->Observaciones
            );

        }
        return $items;
    }

    public function precioConversion($precio, $id_moneda)
    {
        switch($id_moneda)
        {
            case(1):
                return ($precio);
            break;
            case(2):
                return ($precio * $this->dolar);
            break;
            case(3):
                return ($precio * $this->euro);
            break;
            case(4):
                return ($precio * $this->libra);
            break;
        }
    }

    public function validarAsignacion($motivo)
    {
        if($this->asignacion)
        {
            throw New \Exception('No se puede '. $motivo.' el presupuesto '. $this->numero_folio_format .' debido a que ya han sido asignados algunos materiales');
        }
    }

    public function eliminarPresupuesto($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->delete();
            $eliminar = PresupuestoContratistaEliminado::find($this->id_transaccion);
            $eliminar->motivo_elimino = $motivo;
            $eliminar->save();
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function crear($data)
    {
        try
        {
            DB::connection('cadeco')->beginTransaction();
            $contrato = ContratoProyectado::find($data['id_contrato']);
            $fecha = new DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            if(!$data['pendiente'])
            {
                $presupuesto = $this->create([
                    'id_antecedente' => $data['id_contrato'],
                    'fecha' => $fecha->format("Y-m-d"),
                    'id_empresa' => $data['id_proveedor'],
                    'id_sucursal' => $data['id_sucursal'],
                    'monto' => $data['subtotal'],
                    'impuesto' => $data['impuesto'],
                    'anticipo' => $data['anticipo'],
                    'observaciones' => $data['observacion'],
                    'PorcentajeDescuento' => $data['descuento_cot'],
                    'TcUSD' => $data['tc_usd'],
                    'TcEuro' => $data['tc_eur'],
                    'TcLibra' => $data['tc_libra'],
                    'DiasCredito' => $data['credito'],
                    'DiasVigencia' => $data['vigencia']
                ]);

                foreach($data['partidas'] as $t => $partida)
                {
                    $presupuesto->partidas()->create([
                        'id_transaccion' => $presupuesto->id_transaccion,
                        'id_concepto' => $partida['id_concepto'],
                        'precio_unitario' => ($data['enable'][$t]) ? $this->precioConversion($data['precio'][$t], $data['moneda'][$t]) : null,
                        'no_cotizado' => ($data['enable'][$t]) ? 0 :1,
                        'PorcentajeDescuento' => ($data['enable'][$t]) ? $data['descuento'][$t] : null,
                        'IdMoneda' => $data['moneda'][$t],
                        'Observaciones' => ($data['enable'][$t] && array_key_exists($t,$data['observaciones'] )) ? $data['observaciones'][$t] : ''
                    ]);
                }
            }else
            {
                $presupuesto = $this->create([
                    'id_antecedente' => $data['id_contrato'],
                    'fecha' => $fecha->format("Y-m-d"),
                    'id_empresa' => $data['id_proveedor'],
                    'id_sucursal' => $data['id_sucursal'],
                    'monto' => 0,
                    'impuesto' => 0,
                    'anticipo' => 0,
                    'observaciones' => $data['observacion'],
                    'PorcentajeDescuento' => null,
                    'TcUSD' => null,
                    'TcEuro' => null,
                    'TcLibra' => null,
                    'DiasCredito' => null,
                    'DiasVigencia' => null,
                    'estado' => 0
                ]);

                $t = 0;
                foreach($data['partidas'] as $partida)
                {
                    $presupuesto->partidas()->create([
                        'id_transaccion' => $presupuesto->id_transaccion,
                        'id_concepto' => $partida['id_concepto'],
                        'precio_unitario' => 0,
                        'no_cotizado' => 1,
                        'PorcentajeDescuento' => null,
                        'IdMoneda' => null,
                        'Observaciones' => null
                    ]);
                    $t ++;
                }
            }
            DB::connection('cadeco')->commit();
                return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }
    }

    public function actualizar($data)
    {
        try
        {
            DB::connection('cadeco')->beginTransaction();

                $fecha =New DateTime($data['fecha']);
                $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
                $this->update([
                    'fecha' => $fecha->format("Y-m-d"),
                    'monto' => $data['subtotal'],
                    'impuesto' => $data['impuesto'],
                    'anticipo' => $data['anticipo'],
                    'observaciones' => $data['observaciones'],
                    'PorcentajeDescuento' => $data['descuento_cot'],
                    'TcUSD' => $data['tcUsd'],
                    'TcEuro' => $data['tdEuro'],
                    'TcLibra' => $data['tcLibra'],
                    'DiasCredito' => $data['credito'],
                    'DiasVigencia' => $data['vigencia']
                ]);;
                $x = 0;
                foreach($data['partidas'] as $partida)
                {
                    $precio = 0;
                    $item = PresupuestoContratistaPartida::where('id_transaccion', '=', $partida['id'])->where('id_concepto', '=', $partida['concepto']['id_concepto']);
                    if($data['moneda'][$x] > 1)
                    {
                        switch ((int)$data['moneda'][$x]){
                            case 2:
                                $precio = $data['precio'][$x] * $data['tcUsd'];
                            break;
                            case 3:
                                $precio = $data['precio'][$x] * $data['tdEuro'];
                            break;
                            case 4:
                                $precio = $data['precio'][$x] * $data['tcLibra'];
                            break;
                        }

                    }
                    else{
                        $precio = $data['precio'][$x];
                    }

                    $item->update([
                        'precio_unitario' => ($data['enable'][$x]) ? $precio : null,
                        'no_cotizado' => ($data['enable'][$x]) ? 0 : 1,
                        'PorcentajeDescuento' => ($data['enable'][$x]) ? $data['descuento'][$x] : null,
                        'IdMoneda' => $data['moneda'][$x],
                        'Observaciones' => $partida['observaciones']
                    ]);
                    $x++;
                }

            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function descargaLayout($id)
    {
        $find = $this::find($id);
        return Excel::download(new PresupuestoLayout($find), str_replace('/', '-',$find->contratoProyectado->referencia).'.xlsx');
    }

    public function datosComparativos()
    {
        $partidas = [];
        $presupuestos = [];
        $precios = [];

        foreach ($this->contratoProyectado->conceptos()->orderBy('descripcion', 'asc')->get() as $key => $item) {
            if (array_key_exists($item->id_concepto, $partidas)) {
                $partidas[$item->id_concepto]['cantidad_presupuestada'] = $partidas[$item->id_concepto]['cantidad_presupuestada'] + $item->cantidad_presupuestada;
                $partidas[$item->id_concepto]['cantidad_original'] = $partidas[$item->id_concepto]['cantidad_original'] + $item->cantidad_original;
            } else {
                $partidas[$item->id_concepto]['concepto'] = $item->descripcion;
                $partidas[$item->id_concepto]['unidad'] = $item->unidad;
                $partidas[$item->id_concepto]['cantidad_presupuestada'] = $item->cantidad_presupuestada;
                $partidas[$item->id_concepto]['cantidad_original'] = $item->cantidad_original;
                $partidas[$item->id_concepto]['observaciones'] = $item->observaciones ? $item->observaciones : '';
            }
        }
        foreach ($this->contratoProyectado->presupuestos()->orderBy('id_transaccion', 'desc')->get() as $cont => $presupuesto) {
            $presupuestos[$cont]['id_transaccion'] = $presupuesto->id_transaccion;
            $presupuestos[$cont]['empresa'] = $presupuesto->empresa->razon_social;
            $presupuestos[$cont]['fecha'] = $presupuesto->fecha_format;
            $presupuestos[$cont]['vigencia'] = $presupuesto->DiasVigencia ? $presupuesto->DiasVigencia : '-';
            $presupuestos[$cont]['anticipo'] = $presupuesto->anticipo && $presupuesto->anticipo > 0 ? $presupuesto->anticipo : '-';
            $presupuestos[$cont]['dias_credito'] = $presupuesto->DiasCredito ? $presupuesto->DiasCredito : '-';
            $presupuestos[$cont]['descuento_global'] = $presupuesto->descuento ? $presupuesto->descuento : '-';
            $presupuestos[$cont]['suma_subtotal_partidas'] = $presupuesto->suma_subtotal_partidas;
            $presupuestos[$cont]['iva_partidas'] = $presupuesto->iva_partidas;
            $presupuestos[$cont]['total_partidas'] = $presupuesto->total_partidas;
            $presupuestos[$cont]['tipo_moneda'] = $presupuesto->moneda ? $presupuesto->moneda->nombre : '';
            $presupuestos[$cont]['observaciones'] = $presupuesto->observaciones ? $presupuesto->observaciones : '';
            foreach ($presupuesto->partidas as $p) {
                if (key_exists($p->id_concepto, $precios)) {
                    if ($p->precio_sin_descuento > 0 && $precios[$p->id_concepto] > $p->precio_sin_descuento)
                        $precios[$p->id_concepto] = (float)$p->precio_sin_descuento;
                } else {
                    if ($p->precio_unitario > 0) {
                        $precios[$p->id_concepto] = (float)$p->precio_sin_descuento;
                    }
                }
                if (array_key_exists($p->id_concepto, $partidas)) {
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['id_transaccion'] = $presupuesto->id_transaccion;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['precio_unitario'] = $p->precio_unitario;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['precio_unitario_c'] = $p->precio_unitario_convert;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['precio_total_moneda'] = $p->precio_sin_descuento;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['precio_total'] = $p->precio_unitario_convert * $partidas[$p->id_concepto]['cantidad_presupuestada'];
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['tipo_cambio_descripcion'] = $p->moneda ? $p->moneda->abreviatura : '';
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['descuento_partida'] = $p->PorcentajeDescuento ? $p->PorcentajeDescuento : 0;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['observaciones'] = $p->observaciones ? $p->observaciones : '';
                }
            }
        }
        //dd($presupuestos, $partidas, $precios);
        return [
            'presupuestos' => $presupuestos,
            'partidas' => $partidas,
            'precios_menores' => $precios
        ];
    }

    public function sumaPrecioPartidaMoneda($tipo_moneda)
    {
        $suma = 0;
        foreach ($this->partidas as $partida) {
            if ($tipo_moneda == $partida->IdMoneda) {
                $suma += $partida->precio_compuesto_total;
            }
        }
        return $suma;
    }

    public function calcular_ki($precio, $precio_menor)
    {
        return $precio_menor == 0 ? ($precio - $precio_menor) : ($precio - $precio_menor) / $precio_menor;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        #CONTRATO PROYECTADO
        $relaciones[$i] = $this->contratoProyectado->datos_para_relacion;
        $i++;

        #PRESUPUESTOS
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        #SUBCONTRATOS
        $subcontratos = $this->subcontratos;
        foreach($subcontratos as $subcontrato)
        {
            $relaciones[$i] = $subcontrato->datos_para_relacion;
            $i++;
            #POLIZA DE SUBCONTRATO
            if($subcontrato->poliza){
                $relaciones[$i] = $subcontrato->poliza->datos_para_relacion;
                $i++;
            }
            #FACTURA DE SUBCONTRATO
            foreach ($subcontrato->facturas as $factura){
                $relaciones[$i] = $factura->datos_para_relacion;
                $i++;
                #POLIZA DE FACTURA DE SUBCONTRATO
                if($factura->poliza){
                    $relaciones[$i] = $factura->poliza->datos_para_relacion;
                    $i++;
                }
                #PAGO DE FACTURA DE SUBCONTRATO
                foreach ($factura->ordenesPago as $orden_pago){
                    if($orden_pago->pago){
                        $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                        $i++;
                        #POLIZA DE PAGO DE FACTURA DE OC
                        if($orden_pago->pago->poliza){
                            $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                            $i++;
                        }
                    }
                }
            }
            #ESTIMACION
            foreach ($subcontrato->estimaciones as $estimacion){
                $relaciones[$i] = $estimacion->datos_para_relacion;
                $i++;

                #FACTURA DE ESTIMACIÓN
                foreach ($estimacion->facturas as $factura){
                    $relaciones[$i] = $factura->datos_para_relacion;
                    $i++;

                    #POLIZA DE FACTURA DE ESTIMACIÓN
                    if($factura->poliza){
                        $relaciones[$i] = $factura->poliza->datos_para_relacion;
                        $i++;
                    }

                    #PAGO DE FACTURA DE ESTIMACIÓN
                    foreach ($factura->ordenesPago as $orden_pago){
                        if($orden_pago->pago){
                            $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                            $i++;
                            #POLIZA DE PAGO DE FACTURA DE ESTIMACIÓN
                            if($orden_pago->pago->poliza){
                                $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                                $i++;
                            }
                        }
                    }
                }
            }

            #SOLICITUD DE CAMBIO A SUBCONTRATO
            foreach ($subcontrato->solicitudesCambio as $solicitud_cambio){
                $relaciones[$i] = $solicitud_cambio->datos_para_relacion;
                $i++;
            }
        }
        $orden1 = array_column($relaciones, 'orden');
        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }
}
