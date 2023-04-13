<?php


namespace App\Models\CADECO;


use App\CSV\CotizacionLayout;
use App\Facades\Context;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Models\CADECO\Compras\CotizacionComplemento;
use App\Models\CADECO\Compras\CotizacionComplementoPartida;
use App\Models\CADECO\Compras\CotizacionEliminada;
use App\Models\CADECO\Compras\CotizacionPartidaEliminada;
use App\Models\CADECO\Compras\Exclusion;
use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use DateTime;
use DateTimeZone;
use Dingo\Blueprint\Annotation\Attributes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionCompra  extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;
    public const OPCION_ANTECEDENTE = 1;
    public const TIPO = 18;
    public const OPCION = 1;
    public const NOMBRE = "Cotización";
    public const ICONO = "fa fa-comment-dollar";

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'id_referente',
        'id_empresa',
        'id_sucursal',
        'id_moneda',
        'tipo_transaccion',
        'opciones',
        'numero_folio',
        'fecha',
        'cumplimiento',
        'vencimiento',
        'estado',
        'monto',
        'impuesto',
        'id_obra',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'porcentaje_anticipo_pactado'
    ];

    public $searchable = [
        'numero_folio',
        'observaciones',
        'fecha',
        'empresa.razon_social',
        'solicitud.numero_folio'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 18)
                ->where("estado",">",-1)
            ->where('opciones','=', 1)->whereHas('complemento');
        });
    }

    /**
     * Relaciones
     */
    public function partidas()
    {
        return $this->hasMany(CotizacionCompraPartida::class, 'id_transaccion', 'id_transaccion')->where("no_cotizado", "=", 0);
    }

    public function partidasEdicion()
    {
        return $this->hasMany(CotizacionCompraPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(CotizacionComplemento::class, 'id_transaccion', 'id_transaccion');
    }

    public function descargaLayout()
    {
        $folio = str_pad($this->numero_folio, 5, 0, 0);
        return Excel::download(new CotizacionLayout($this), '#'.$folio.'.xlsx');
    }

    public function asignacionPartida()
    {
        return $this->hasMany(AsignacionProveedorPartida::class, 'id_transaccion_cotizacion', 'id_transaccion');
    }

    public function asignacionesPartida()
    {
        return $this->hasMany(AsignacionProveedorPartida::class, 'id_transaccion_cotizacion', 'id_transaccion');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCompra::class, 'id_antecedente', 'id_transaccion');
    }

    public function solicitudComplemento()
    {
        return $this->belongsTo(SolicitudComplemento::class,'id_antecedente', 'id_transaccion');
    }

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(Transaccion::class, 'id_referente', 'id_transaccion')->where('id_antecedente', '=', $this->id_antecedente);
    }

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class,"id_referente", "id_transaccion");
    }

    public function invitacion()
    {
        return $this->belongsTo(Invitacion::class, "id_referente", "id");
    }

    public function exclusiones()
    {
        return $this->hasMany(Exclusion::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Scopes
     */
    public function scopeConEmpresa()
    {
        return $this->whereNotNull('id_empresa');
    }

    public function scopeAreasCompradorasAsignadas($query)
    {
        return $query->whereHas('solicitud', function ($q) {
            $q->areasCompradorasAsignadas();
        });
    }

    public function scopeSolicitudesAsignadasProveedores($query)
    {
        return $query->whereHas('solicitud', function ($q) {
            $q->areasCompradorasAsignadas();
        });
    }

    /**
     * Attributes
     */
    public function getSumaSubtotalPartidasAttribute()
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
            $suma += $partida->total_precio_moneda;
        }
        return $suma;
    }

    public function getSumaSubtotalPartidasComparativaAttribute()
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
            $suma += $partida->total_precio_moneda_comparativa;
        }
        return $suma;
    }

    public function getIVAPartidasAttribute()
    {
        return $this->suma_subtotal_partidas * $this->tasa_iva;
    }

    public function getTotalPartidasAttribute()
    {
        return $this->suma_subtotal_partidas + $this->iva_Partidas;
    }

    public function getDescuentoAttribute()
    {
        return $this->suma_subtotal_partidas * $this->complemento->descuento/100;
    }

    public function getDescuentoComparativaAttribute()
    {
        return $this->suma_subtotal_partidas_comparativa * $this->complemento->descuento/100;
    }

    public function getSubtotalConDescuentoAttribute()
    {
        return $this->suma_subtotal_partidas - $this->descuento;
    }

    public function getSubtotalConDescuentoComparativaAttribute()
    {
        return $this->suma_subtotal_partidas_comparativa - $this->descuento_comparativa;
    }

    public function getIVAConDescuentoAttribute()
    {
        return $this->subtotal_con_descuento * $this->tasa_iva;
    }

    public function getIVAConDescuentoComparativaAttribute()
    {
        return $this->subtotal_con_descuento_comparativa * $this->tasa_iva;
    }

    public function getTotalConDescuentoAttribute()
    {
        return $this->subtotal_con_descuento + $this->iva_con_descuento;
    }

    public function getTotalConDescuentoComparativaAttribute()
    {
        return $this->subtotal_con_descuento_comparativa + $this->iva_con_descuento_comparativa;
    }

    public function getAsignadaAttribute()
    {
        if($this->asignacionPartida->count()>0)
        {
            return true;
        } else {
            return false;
        }
    }

    public function getEstadoFormularioAttribute()
    {

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
        $datos["tipo"] = CotizacionCompra::NOMBRE;
        $datos["tipo_numero"] = CotizacionCompra::TIPO;
        $datos["icono"] = CotizacionCompra::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getTasaIvaAttribute()
    {
        if($this->subtotal != 0 && $this->impuesto != 0)
        {
            return $this->impuesto /$this->subtotal;
        }
        return 0;
    }

    public function getTasaIvaFormatAttribute()
    {
        return number_format($this->tasa_iva*100, 0, '.', '');
    }

    /**
     * Metodos
     */
    public function validarAsignacion($motivo)
    {
        if ($this->asignacionPartida->count()>0) {
            throw New \Exception('No se puede ' . $motivo . ' la cotización ' . $this->numero_folio_format . ' debido a que ya han sido asignados algunos materiales');
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
                'observaciones' => $data['observaciones'],
                'fecha' => $fecha->format("Y-m-d"),
                'monto' => $data['importe'],
                'impuesto' => $data['impuesto'],
                'porcentaje_anticipo_pactado' => $data['pago']
            ]);

            if($this->complemento)
            {
                $this->complemento->parcialidades = $data['pago'];
                $this->complemento->dias_credito = $data['credito'];
                $this->complemento->vigencia = $data['vigencia'];
                $this->complemento->plazo_entrega = $data['tiempo'];
                $this->complemento->descuento = $data['descuento_cot'];
                $this->complemento->anticipo = $data['anticipo'];
                $this->complemento->importe = $data['importe'];
                $this->complemento->tc_usd = $data['tc_usd'];
                $this->complemento->tc_eur = $data['tc_eur'];
                $this->complemento->tc_libra = $data['tc_libra'];
                $this->complemento->save();
            }
            else{
                $this->complemento()->create([
                    'id_transaccion' => $this->id_transaccion,
                    'parcialidades' => $data['pago'],
                    'dias_credito' => $data['credito'],
                    'vigencia' => $data['vigencia'],
                    'plazo_entrega' => $data['tiempo'],
                    'descuento' => $data['descuento_cot'],
                    'anticipo' => $data['anticipo'],
                    'importe' => $data['importe'],
                    'tc_usd' => $data['tc_usd'],
                    'tc_eur' => $data['tc_eur'],
                    'tc_libra' => $data['tc_libra'],
                    'timestamp_registro' => $fecha->format("Y-m-d")
                ]);
            }

            $i = 0;
            foreach($data['partidas'] as $key => $partida) {
                $item = CotizacionCompraPartida::where('id_material', '=', $partida['material']['id'])->where('id_transaccion', '=', $this->id_transaccion)->first();
                if ($item) {
                    CotizacionCompraPartida::where('id_material', '=', $partida['material']['id'])->where('id_transaccion', '=', $this->id_transaccion)->update([
                        'precio_unitario' => ($data['enable'][$i]) ? $data['precio'][$i] : 0,
                        'descuento' => ($data['enable'][$i] !== false) ? ($data['descuento_cot'] + $data['descuento'][$i] - (($data['descuento_cot'] * $data['descuento'][$i]) / 100)) : 0,
                        'no_cotizado' => (!$data['enable'][$i]) ? 1 : 0,
                        'id_moneda' => ($data['enable'][$i]) ? $data['moneda'][$i] : null
                    ]);
                    if ($item->partida) {
                        CotizacionComplementoPartida::where('id_material', '=', $partida['material']['id'])->where('id_transaccion', '=', $this->id_transaccion)->update([
                                'descuento_partida' => ($data['enable'][$i]) ? $data['descuento'][$i] : 0,
                                'observaciones' => ($data['enable'][$i] && $partida['observacion']) ? $partida['observacion'] : null,
                                'estatus' => ($data['enable'][$i]) ? 3 : 1
                            ]);
                    } else {
                        CotizacionComplementoPartida::create([
                            'id_transaccion' => $this->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'descuento_partida' => ($data['enable'][$i]) ? $data['descuento'][$i] : 0,
                            'observaciones' => ($data['enable'][$i] && $partida['observacion']) ? $partida['observacion'] : null,
                            'estatus' => ($data['enable'][$i]) ? 3 : 1
                        ]);
                    }
                } else {
                    if((is_null($data['enable'][$i]) || $data['enable'][$i] == true)) {
                        $cotizaciones = $this->partidas()->create([
                            'id_transaccion' => $this->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'cantidad' => $partida['cantidad'],
                            'precio_unitario' => $data['precio'][$i],
                            'descuento' => ($data['descuento_cot'] + $data['descuento'][$i] - (($data['descuento_cot'] * $data['descuento'][$i]) / 100)),
                            'anticipo' => $data['anticipo'],
                            'dias_credito' => $data['credito'],
                            'dias_entrega' => $data['tiempo'],
                            'no_cotizado' => 0,
                            'disponibles' => 1,
                            'id_moneda' => $data['moneda'][$i]
                        ]);

                        $cotizaciones = $cotizaciones->partida()->create([
                            'id_transaccion' => $this->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'descuento_partida' => $data['descuento'][$i],
                            'observaciones' => $data['observaciones'] ? $data['observaciones'][$i] : '',
                            'estatus' => 3
                        ]);
                    }
                }
                $i++;
            }
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function crear($data)
    {
        $empresa = ProveedorContratista::find($data["id_proveedor"]);

        if($empresa && strlen(str_replace(" ","", $empresa->rfc))>0){
            $empresa->rfcValidaBoletinados($empresa->rfc);
            $empresa->rfcValidaEfos($empresa->rfc);
        }
        try
        {
            DB::connection('cadeco')->beginTransaction();
            $moneda = Moneda::get();
            $solicitud = SolicitudCompra::find($data['id_solicitud']);
            $fecha =New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            if(!$data['pendiente'])
            {
                $cotizacion = $this->create([
                    'id_antecedente' => $data['id_solicitud'],
                    'id_empresa' => $data['id_proveedor'],
                    'id_sucursal' => ($data['sucursal']) ? $data['id_sucursal'] : null,
                    'observaciones' => $data['observacion'],
                    'estado' => 1,
                    'fecha' => $fecha->format("Y-m-d"),
                    'monto' => $data['importe'],
                    'impuesto' => $data['impuesto'],
                    'cumplimiento' => $fecha->format("Y-m-d"),
                    'vencimiento' => $fecha->format("Y-m-d"),
                    'porcentaje_anticipo_pactado' => $data['pago']
                ]);

                $cotizacion->complemento()->create([
                    'id_transaccion' => $cotizacion->id_transaccion,
                    'parcialidades' => $data['pago'],
                    'dias_credito' => $data['credito'],
                    'vigencia' => $data['vigencia'],
                    'plazo_entrega' => $data['tiempo'],
                    'descuento' => $data['descuento_cot'],
                    'tc_usd' => $data['tc_usd'],
                    'tc_eur' => $data['tc_eur'],
                    'tc_libra' => $data['tc_libra'],
                    'anticipo' => $data['anticipo'],
                    'importe' => $data['importe'],
                    'timestamp_registro' => $fecha->format("Y-m-d")
                ]);

                foreach($data['partidas'] as $key => $partida) {
                    if($data['enable'] == [] || !array_key_exists($key,$data['enable']) || (is_null($data['enable'][$key]) || $data['enable'][$key] == true)) {
                        $cotizaciones = $cotizacion->partidas()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'cantidad' => $partida['cantidad'],
                            'precio_unitario' => $data['precio'][$key],
                            'descuento' => ($data['descuento_cot'] + $partida['descuento'] - (($data['descuento_cot'] * $partida['descuento']) / 100)),
                            'anticipo' => $data['anticipo'],
                            'dias_credito' => $data['credito'],
                            'dias_entrega' => $data['tiempo'],
                            'no_cotizado' => 0,
                            'disponibles' => 1,
                            'id_moneda' => $data['moneda'][$key]
                        ]);

                        #------- Compras.cotizacion_partidas_complemento

                        $cotizaciones->partida()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'descuento_partida' => $partida['descuento'],
                            'observaciones' => array_key_exists($key,$data['observaciones']) ? $data['observaciones'][$key] : '',
                            'estatus' => 3
                        ]);
                    }
                }
                if($solicitud->validarCotizada())
                {
                    /**
                    * Cambiar estado de la solicitud a: Cotizada
                    */
                    $solicitud->complemento->setCambiarEstado(2, 3);
                }
            }
            else{
                $cotizacion = $this->create([
                    'id_antecedente' => $data['id_solicitud'],
                    'id_empresa' => $data['id_proveedor'],
                    'id_sucursal' => ($data['sucursal']) ? $data['id_sucursal'] : null,
                    'observaciones' => $data['observacion'],
                    'fecha' => $fecha->format("Y-m-d"),
                    'monto' => 0,
                    'estado' => 0,
                    'impuesto' => 0,
                    'cumplimiento' => $fecha->format("Y-m-d"),
                    'vencimiento' => $fecha->format("Y-m-d"),
                    'porcentaje_anticipo_pactado' => null
                ]);
                $cotizacion->complemento()->create([
                    'id_transaccion' => $cotizacion->id_transaccion,
                    'parcialidades' => 0,
                    'dias_credito' => 0,
                    'vigencia' => 0,
                    'plazo_entrega' => 0,
                    'descuento' => 0,
                    'tc_usd' => $moneda[1]->cambio->cambio,
                    'tc_eur' => $moneda[2]->cambio->cambio,
                    'tc_libra' => $moneda[3]->cambio->cambio,
                    'anticipo' => 0,
                    'importe' => 0,
                    'timestamp_registro' => $fecha->format("Y-m-d")
                ]);

                foreach($data['partidas'] as $partida) {
                    $cotizaciones = $cotizacion->partidas()->create([
                        'id_transaccion' => $cotizacion->id_transaccion,
                        'id_material' => $partida['material']['id'],
                        'cantidad' => $partida['cantidad'],
                        'id_moneda' => 1
                    ]);

                    #------- Compras.cotizacion_partidas_complemento

                    $cotizaciones->partida()->create([
                        'id_transaccion' => $cotizacion->id_transaccion,
                        'id_material' => $partida['material']['id'],
                        'descuento_partida' => 0,
                        'observaciones' => '',
                        'estatus' => 3
                    ]);
                }
            }
            DB::connection('cadeco')->commit();
            return $cotizacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }
    }

    /**
     * Eliminar cotización de compra
     * @param $motivo
     * @return $this
     */
    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validar();
            $this->delete();
            $this->revisarRespaldos($motivo);
            $this->cambioEstadoSolicitud();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    /**
     * Validar la cotización para poder realizar los cambios.
     */
    private function validar()
    {
        $mensaje = "";
        if($this->transaccionesRelacionadas()->count('id_transaccion') > 0)
        {
            foreach ($this->transaccionesRelacionadas()->get() as $antecedente)
            {
                $mensaje .= "-".$antecedente->tipo->Descripcion." #".$antecedente->numero_folio."\n";
            }
            abort(500, "Esta cotización de compra tiene la(s) siguiente(s) transaccion(es) relacionada(s): \n".$mensaje);
        }
        $this->validarAsignacion("eliminar");
    }

    /**
     * Elimina las partidas
     */
    public function eliminarPartidas()
    {
        foreach ($this->partidas()->get() as $item) {
            $item->delete();
        }
    }

    private function revisarRespaldos($motivo)
    {
        if (($cotizacion = CotizacionEliminada::where('id_transaccion', $this->id_transaccion)->first()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de la cotización de compra, no se respaldo la cotización correctamente.');
        } else {
            $cotizacion->motivo = $motivo;
            $cotizacion->save();
        }
        if (($item = CotizacionPartidaEliminada::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de la cotización de compra, no se respaldo los items correctamente.');
        }
    }

    public function sumaSubtotalPartidas($tipo_moneda)
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
            if($tipo_moneda == $partida->id_moneda)
            {
                $suma += $partida->total_precio_moneda;
            }
        }
        return $suma;
    }

    public function sumaPrecioPartidaMoneda($tipo_moneda)
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
            if($tipo_moneda == $partida->id_moneda)
            {
                $suma += $partida->precio_compuesto_total;
            }
        }
        return $suma;
    }

    /**
     * Revisar si es necesario cambiar el estado de la solicitud (dependiendo de sus cotizaciones)
     *
     */
    public function cambioEstadoSolicitud()
    {
        $this->refresh();
        if(!$this->solicitud->validarCotizada()){
            /**
             * Cambiar estado de la solicitud de: 'Cotizada' a:  'En proceso de cotización'
             */
            $this->solicitud->complemento->setCambiarEstado(3,2);

        }
        if($this->solicitud->cotizaciones->count() == 0)
        {
            /**
             * Cambiar estado de la solicitud de: 'En proceso de cotización' a: 'Pendiente de cotización'
             */
            $this->solicitud->complemento->setCambiarEstado(2,1);
        }
    }

    public function datosComparativos()
    {
        $partidas = [];
        $cotizaciones = [];
        $precios = [];
        $exclusiones = [];
        $importes = [];

        foreach ($this->solicitud->items as $key => $item) {
            if (array_key_exists($item->id_material, $partidas)) {
                $partidas[$item->id_material]['cantidad'] = $partidas[$item->id_material]['cantidad'] + $item->cantidad;
            } else {
                $partidas[$item->id_material]['material'] = $item->material->descripcion;
                $partidas[$item->id_material]['unidad'] = $item->unidad;
                $partidas[$item->id_material]['cantidad'] = $item->cantidad;
                $partidas[$item->id_material]['observaciones'] = $item->complemento ? $item->complemento->observaciones : '';
            }
        }

        foreach ($this->solicitud->cotizaciones as $cont => $cotizacion) {
            $cotizaciones[$cont]['id_transaccion'] = $cotizacion->id_transaccion;
            $cotizaciones[$cont]['empresa'] = $cotizacion->empresa->razon_social;
            $cotizaciones[$cont]['fecha'] = $cotizacion->fecha_format;
            $cotizaciones[$cont]['vigencia'] = $cotizacion->complemento ? $cotizacion->complemento->vigencia : '-';
            $cotizaciones[$cont]['anticipo'] = $cotizacion->complemento ? $cotizacion->complemento->anticipo : '-';
            $cotizaciones[$cont]['dias_credito'] = $cotizacion->complemento ? $cotizacion->complemento->dias_credito : '-';
            $cotizaciones[$cont]['plazo_entrega'] = $cotizacion->complemento ? $cotizacion->complemento->plazo_entrega : '-';
            $cotizaciones[$cont]['descuento_global'] = ($cotizacion->complemento && $cotizacion->complemento->descuento > 0) ? $cotizacion->complemento->descuento : '-';
            $cotizaciones[$cont]['suma_subtotal_partidas'] = $cotizacion->suma_subtotal_partidas;
            $cotizaciones[$cont]['iva_partidas'] = $cotizacion->iva_partidas;
            $cotizaciones[$cont]['total_partidas'] = $cotizacion->total_partidas;
            $cotizaciones[$cont]['tipo_moneda'] = $cotizacion->moneda ? $cotizacion->moneda->nombre : '';
            $cotizaciones[$cont]['observaciones'] = $cotizacion->observaciones;
            $cotizaciones[$cont]['tc_usd'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_usd ? $cotizacion->complemento->tc_usd :Cambio::where('id_moneda','=', 2)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $cotizaciones[$cont]['tc_eur'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_eur ? $cotizacion->complemento->tc_eur : Cambio::where('id_moneda','=', 3)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $cotizaciones[$cont]['tc_libra'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_libra ? $cotizacion->complemento->tc_libra : Cambio::where('id_moneda','=', 4)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $cotizaciones[$cont]['subtotal_peso'] = $cotizacion->sumaSubtotalPartidas(1) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(1), 2, '.', ',');
            $cotizaciones[$cont]['subtotal_dolar'] = $cotizacion->sumaSubtotalPartidas(2) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(2), 2, '.', ',');
            $cotizaciones[$cont]['subtotal_euro'] = $cotizacion->sumaSubtotalPartidas(3) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(3), 2, '.', ',');
            $cotizaciones[$cont]['subtotal_libra'] = $cotizacion->sumaSubtotalPartidas(4)== 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(4), 2, '.', ',');
            $cotizaciones[$cont]['suma_total_dolar'] = $cotizacion->sumaPrecioPartidaMoneda(2) == 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(2), 2, '.', ',');
            $cotizaciones[$cont]['suma_total_euro'] = $cotizacion->sumaPrecioPartidaMoneda(3) == 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(3), 2, '.', ',');
            $cotizaciones[$cont]['suma_total_libra'] = $cotizacion->sumaPrecioPartidaMoneda(4)== 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(4), 2, '.', ',');
            foreach ($cotizacion->partidas as $p) {
                if (key_exists($p->id_material, $precios)) {
                    if($p->precio_unitario_compuesto > 0 && $precios[$p->id_material] > $p->precio_unitario_compuesto)
                        $precios[$p->id_material] = (float) $p->precio_unitario_compuesto;
                        $importes[$p->id_material] =  $precios[$p->id_material] * $p->cantidad;
                } else {
                    if($p->precio_unitario_compuesto > 0) {
                        $precios[$p->id_material] = (float) $p->precio_unitario_compuesto;
                        $importes[$p->id_material] = $precios[$p->id_material]  * $p->cantidad;
                    }
                }
                if (array_key_exists($p->id_material, $partidas)) {
                    $partidas[$p->id_material]['cotizaciones'][$cont]['id_transaccion'] = $cotizacion->id_transaccion;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['cantidad'] = $p->cantidad;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_unitario'] = $p->precio_unitario;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['id_moneda'] = $p->id_moneda;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['cantidad_format'] = $p->cantidad_format;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_total_moneda'] = $p->total_precio_moneda;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_con_descuento'] = $p->precio_compuesto;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_total_compuesto'] = $p->precio_compuesto_total;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['precio_unitario_compuesto'] = $p->precio_unitario_compuesto;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['tipo_cambio_descripcion'] = $p->moneda ? $p->moneda->abreviatura : '';
                    $partidas[$p->id_material]['cotizaciones'][$cont]['descuento_partida'] = $p->partida ? $p->partida->descuento_partida : 0;
                    $partidas[$p->id_material]['cotizaciones'][$cont]['observaciones'] = $p->partida ? $p->partida->observaciones : '';
                }
            }
        }
        $cantidad = 0;
        foreach ($this->solicitud->cotizaciones as $cont => $cotizacion) {
            $cotizaciones[$cont]['ivg_partida'] = $this->calcular_ivg($importes, $cotizacion->partidas);
            $cotizaciones[$cont]['ivg'] = $this->ivg_format($importes, $cotizacion->partidas);
            $cotizaciones[$cont]['ivg_partida_porcentaje'] = $cotizacion->partidas->count() > 0 ? $cotizaciones[$cont]['ivg_partida']/ $cotizacion->partidas->count() : 0 ;
            $importe = 0;
            foreach($cotizacion->exclusiones as $exc => $exclusion){
                $t_cambio = 1;
                if($exclusion->id_moneda != 1){
                    $t_cambio = $exclusion->moneda->cambio->cambio;
                }
                $exclusiones[$cont][$exc] = $exclusion->toArray();
                $exclusiones[$cont][$exc]['moneda'] = $exclusion->moneda->nombre;
                $exclusiones[$cont][$exc]['t_cambio'] = $t_cambio;
                $importe += $exclusion->cantidad * $exclusion->precio_unitario * $t_cambio;
                $cantidad ++;
            }
            $exclusiones[$cont]['importe'] = $importe;
        }
        $exclusiones['cantidad'] = $cantidad;
        return [
            'cotizaciones' => $cotizaciones,
            'partidas' => $partidas,
            'precios_menores' => $precios,
            'exclusiones' => $exclusiones
        ];
    }

    public function calcular_ki($precio, $precio_menor)
    {
        return $precio_menor == 0 ?  ($precio - $precio_menor) : ($precio - $precio_menor) / $precio_menor;
    }

    private function calcular_ivg($importes, $partidas_cotizacion)
    {
        $suma_importes = 0;
        $suma_importes_bajos = 0;
        foreach($importes as $id_material => $importe)
        {
            $suma_importes_bajos += $importe;
        }
        foreach($partidas_cotizacion as $partida)
        {
            $suma_importes += $partida->precio_unitario_compuesto * $partida->cantidad;
        }

        return $suma_importes_bajos == 0 ?  ($suma_importes - $suma_importes_bajos) : ($suma_importes - $suma_importes_bajos) / $suma_importes_bajos;
        /*dd($precios, $suma_precios_bajos, $suma_precios);
        if ($partidas_cotizacion) {
            foreach ($partidas_cotizacion as $partida) {
                $ivg += $partida->precio_unitario > 0 ? $this->calcular_ki($partida->precio_unitario_compuesto, $precios[$partida->id_material]) : 0;
            }
            return $partidas_cotizacion->count() > 0 ? $ivg : -1;
        }

        return -1;*/
    }

    public function ki_format($precio, $precio_menor)
    {
        $ki = $this->calcular_ki($precio, $precio_menor);
        if($ki >0){
            return number_format($ki,3);
        }else
        {
            return "-";
        }
    }

    public function ivg_format($precios, $partidas_cotizacion)
    {
        $ivg = $this->calcular_ivg($precios, $partidas_cotizacion);
        if($ivg >0){
            return number_format($ivg,3);
        }else
        {
            return "-";
        }
    }

    public function getRelaciones()
    {
        $ordenes = $this->cotizaciones;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $salidas_arr = [];
        $transferencias_arr = [];
        $i = 0;

        #SOLICITUD
        $relaciones[$i] = $this->solicitud->datos_para_relacion;
        $i++;

        #COTIZACIONES
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        #ORDEN COMPRA
        $ordenes_compra = $this->ordenesCompra;
        foreach($ordenes_compra as $orden_compra)
        {
            $relaciones[$i] = $orden_compra->datos_para_relacion;
            $i++;
            #POLIZA DE OC
            if($orden_compra->poliza){
                $relaciones[$i] = $orden_compra->poliza->datos_para_relacion;
                $i++;
            }
            #FACTURA DE OC
            foreach ($orden_compra->facturas as $factura){
                $relaciones[$i] = $factura->datos_para_relacion;
                $i++;
                #POLIZA DE FACTURA DE OC
                if($factura->poliza){
                    $relaciones[$i] = $factura->poliza->datos_para_relacion;
                    $i++;
                }
                #PAGO DE FACTURA DE OC
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
            #ENTRADA DE MATERIAL
            foreach ($orden_compra->entradas_material as $entrada_almacen){
                $relaciones[$i] = $entrada_almacen->datos_para_relacion;
                $i++;

                #SALIDA DE MATERIAL
                foreach ($entrada_almacen->salidas as $salida){
                    $salidas_arr[] = $salida;
                }
                #TRANSFERENCIA DE MATERIAL
                foreach ($entrada_almacen->transferencias as $transferencia){
                    $transferencias_arr[] = $transferencia;
                }

                #FACTURA DE ENTRADA
                foreach ($entrada_almacen->facturas as $factura){
                    $relaciones[$i] = $factura->datos_para_relacion;
                    $i++;

                    #POLIZA DE FACTURA DE ENTRADA
                    if($factura->poliza){
                        $relaciones[$i] = $factura->poliza->datos_para_relacion;
                        $i++;
                    }

                    #PAGO DE FACTURA DE ENTRADA
                    foreach ($factura->ordenesPago as $orden_pago){
                        if($orden_pago->pago){
                            $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                            $i++;
                            #POLIZA DE PAGO DE FACTURA DE ENTRADA
                            if($orden_pago->pago->poliza){
                                $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        $salidas = collect($salidas_arr)->unique();
        foreach ($salidas as $salida){
            $relaciones[$i] = $salida->datos_para_relacion;
            $i++;
            #POLIZA DE SALIDA
            if($salida->poliza){
                $relaciones[$i] = $salida->poliza->datos_para_relacion;
                $i++;
            }
        }
        $transferencias = collect($transferencias_arr)->unique();
        foreach ($transferencias as $transferencia){
            $relaciones[$i] = $transferencia->datos_para_relacion;
            $i++;
            #POLIZA DE TRANSFERENCIA
            if($transferencia->poliza){
                $relaciones[$i] = $transferencia->poliza->datos_para_relacion;
                $i++;
            }
        }
        $orden1 = array_column($relaciones, 'orden');

        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function registrarPortalProveedor($data, $invitacion)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $invitacion->base_datos);
        if($invitacion->cotizacionGenerada){
            abort(500, "Esta cotización no puede ser registrada porque ya existe la cotización ".$invitacion->cotizacionGenerada->numero_folio_format." del proyecto ".$invitacion->descripcion_obra." asociada a esta invitación.");
        }

        try
        {
            DB::connection('cadeco')->beginTransaction();
            $solicitud = SolicitudCompra::withoutGlobalScopes()->find($data['id']);
            $fecha =New DateTime($data['fecha_cot']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            if(!$data['pendiente'])
            {
                $cotizacion = $this->create([
                    'id_antecedente' => $data['id'],
                    'id_referente'=>$invitacion->id,
                    'id_empresa' => $invitacion->id_proveedor_sao,
                    'id_obra' => $invitacion->id_obra,
                    'id_sucursal' => $invitacion->id_sucursal_sao,
                    'observaciones' => $data['observaciones_cot'],
                    'estado' => -1,
                    'opciones' => 10,
                    'fecha' => $fecha->format("Y-m-d"),
                    'monto' => $data['importe'],
                    'impuesto' => $data['impuesto'],
                    'cumplimiento' => $fecha->format("Y-m-d"),
                    'vencimiento' => $fecha->format("Y-m-d"),
                    'porcentaje_anticipo_pactado' => $data['pago']
                ]);

                $cotizacion->complemento()->create([
                    'id_transaccion' => $cotizacion->id_transaccion,
                    'parcialidades' => $data['pago'],
                    'dias_credito' => $data['credito'],
                    'vigencia' => $data['vigencia'],
                    'plazo_entrega' => $data['tiempo'],
                    'descuento' => $data['descuento_cot'],
                    'tc_usd' => $data['tc_usd'],
                    'tc_eur' => $data['tc_eur'],
                    'tc_libra' => $data['tc_libra'],
                    'anticipo' => $data['anticipo'],
                    'importe' => $data['importe'],
                    'timestamp_registro' => $fecha->format("Y-m-d")
                ]);

                foreach($data['partidas'] as $key => $partida) {
                    if($partida['enable'] == true) {
                        $cotizaciones = $cotizacion->partidas()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['id_material'],
                            'cantidad' => $partida['cantidad'],
                            'precio_unitario' => key_exists("precio_cotizacion", $partida) ? $partida['precio_cotizacion']:$partida["precio_unitario"],
                            'descuento' => ($data['descuento_cot'] + $partida['descuento'] - (($data['descuento_cot'] * $partida['descuento']) / 100)),
                            'anticipo' => $data['anticipo'],
                            'dias_credito' => $data['credito'],
                            'dias_entrega' => $data['tiempo'],
                            'no_cotizado' => !$partida['enable'],
                            'disponibles' => 1,
                            'id_moneda' =>  key_exists("moneda_seleccionada", $partida) ? $partida['moneda_seleccionada']:$partida["id_moneda"],
                        ]);
                        #------- Compras.cotizacion_partidas_complemento
                        $cotizaciones->partida()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['id_material'],
                            'descuento_partida' => $partida['descuento'],
                            'observaciones' => array_key_exists('observacion_partida', $partida) ? $partida['observacion_partida'] : '',
                            'estatus' => 3
                        ]);
                    }
                }
                if($solicitud->validarCotizada())
                {
                    /**
                     * Cambiar estado de la solicitud a: Cotizada
                     */
                    $solicitud->complemento->setCambiarEstado(2, 3);
                }
                $cotizacion->invitacion->update([
                    "estado"=>2
                ]);
            }
            else{
                $cotizacion = $this->create([
                    'id_antecedente' => $data['id'],
                    'id_referente'=>$invitacion->id,
                    'id_empresa' => $invitacion->id_proveedor_sao,
                    'id_obra' => $invitacion->id_obra,
                    'id_sucursal' => $invitacion->id_sucursal_sao,
                    'observaciones' => $data['observaciones_cot'],
                    'fecha' => $fecha->format("Y-m-d"),
                    'monto' => 0,
                    'estado' => -2,
                    'opciones' => 10,
                    'impuesto' => 0,
                    'cumplimiento' => $fecha->format("Y-m-d"),
                    'vencimiento' => $fecha->format("Y-m-d"),
                    'porcentaje_anticipo_pactado' => null
                ]);
                $cotizacion->complemento()->create([
                    'id_transaccion' => $cotizacion->id_transaccion,
                    'parcialidades' => 0,
                    'dias_credito' => 0,
                    'vigencia' => 0,
                    'plazo_entrega' => 0,
                    'descuento' => 0,
                    'tc_usd' => $data['tc_usd'],
                    'tc_eur' => $data['tc_eur'],
                    'tc_libra' => $data['tc_libra'],
                    'anticipo' => 0,
                    'importe' => 0,
                    'timestamp_registro' => $fecha->format("Y-m-d")
                ]);

                foreach($data['partidas'] as $partida) {
                    $cotizaciones = $cotizacion->partidas()->create([
                        'id_transaccion' => $cotizacion->id_transaccion,
                        'id_material' => $partida['id_material'],
                        'id_moneda' => key_exists("moneda_seleccionada", $partida) ? $partida['moneda_seleccionada']:$partida["id_moneda"],
                        'cantidad' => $partida['cantidad'],
                    ]);

                    #------- Compras.cotizacion_partidas_complemento

                    $cotizaciones->partida()->create([
                        'id_transaccion' => $cotizacion->id_transaccion,
                        'id_material' => $partida['id_material'],
                        'descuento_partida' => 0,
                        'observaciones' => '',
                        'estatus' => 3
                    ]);
                }
            }
            $invitacion->update([
                'id_cotizacion_generada' => $cotizacion->id_transaccion
            ]);

            foreach ($data['exclusiones'] as $exclusion)
            {
                Exclusion::create([
                    'id_transaccion' => $cotizacion->id_transaccion,
                    'descripcion' => $exclusion['descripcion'],
                    'unidad' => $exclusion['unidad'],
                    'cantidad' => $exclusion['cantidad'],
                    'precio_unitario' => $exclusion['precio_unitario'],
                    'id_moneda' => $exclusion['id_moneda'],
                ]);
            }

            DB::connection('cadeco')->commit();
            return $cotizacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }
    }

    public function editarPortalProveedor($data, $invitacion)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $invitacion->base_datos);
        if($invitacion->estado == 3 || $this->estado > 0){
            abort(500, "Esta cotización no puede ser editada porque ya ha sido enviada como respuesta a la invitación ".$invitacion->numero_folio_format.""
            );
        }

        try
        {
            DB::connection('cadeco')->beginTransaction();
            $fecha =New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $this->update([
                'observaciones' => $data['observaciones'],
                'fecha' => $fecha->format("Y-m-d"),
                'monto' => $data['importe'],
                'impuesto' => $data['impuesto'],
                'porcentaje_anticipo_pactado' => $data['pago'],
            ]);
            if($this->complemento)
            {
                $this->complemento->parcialidades = $data['pago'];
                $this->complemento->dias_credito = $data['credito'];
                $this->complemento->vigencia = $data['vigencia'];
                $this->complemento->plazo_entrega = $data['tiempo'];
                $this->complemento->descuento = $data['descuento'];
                $this->complemento->anticipo = $data['anticipo'];
                $this->complemento->importe = $data['importe'];
                $this->complemento->tc_usd = $data['tc_usd'];
                $this->complemento->tc_eur = $data['tc_eur'];
                $this->complemento->tc_libra = $data['tc_libra'];
                $this->complemento->save();
            }
            else{
                $this->complemento()->create([
                    'id_transaccion' => $this->id_transaccion,
                    'parcialidades' => $data['pago'],
                    'dias_credito' => $data['credito'],
                    'vigencia' => $data['vigencia'],
                    'plazo_entrega' => $data['tiempo'],
                    'descuento' => $data['descuento_cot'],
                    'anticipo' => $data['anticipo'],
                    'importe' => $data['importe'],
                    'tc_usd' => $data['tc_usd'],
                    'tc_eur' => $data['tc_eur'],
                    'tc_libra' => $data['tc_libra'],
                    'timestamp_registro' => $fecha->format("Y-m-d")
                ]);
            }

            foreach($data['partidas']['data'] as $key => $partida) {

                $item = null;
                $item = CotizacionCompraPartida::where('id_material', '=', $partida['material']['id'])->where('id_transaccion', '=', $this->id_transaccion)->first();
                if ($item) {
                    CotizacionCompraPartida::where('id_material', '=', $partida['material']['id'])->where('id_transaccion', '=', $this->id_transaccion)->update([
                        'precio_unitario' => $partida['enable'] ? $partida['precio_unitario'] : null,
                        'descuento' => $partida['enable']  ? ($data['descuento'] + $partida['descuento'] - (($data['descuento'] * $partida['descuento']) / 100)) : 0,
                        'no_cotizado' => !$partida['enable'] ,
                        'id_moneda' => $partida['enable'] ? $partida['id_moneda'] : 1
                    ]);

                    if ($item->partida) {
                        CotizacionComplementoPartida::where('id_material', '=', $partida['material']['id'])->where('id_transaccion', '=', $this->id_transaccion)->update([
                            'descuento_partida' => $partida['enable'] ? $partida['descuento'] : 0,
                            'observaciones' => ($partida['enable'] && $partida['observacion']) ? $partida['observacion'] : null,
                            'estatus' => $partida['enable'] ? 3 : 1
                        ]);
                    } else {
                        CotizacionComplementoPartida::create([
                            'id_transaccion' => $this->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'descuento_partida' => $partida['enable'] ? $partida['descuento'] : 0,
                            'observaciones' => ($partida['enable'] && $partida['observacion']) ? $partida['observacion'] : null,
                            'estatus' => $partida['enable'] ? 3 : 1
                        ]);
                    }
                }
            }

            $exclusiones_viejas = [];
            foreach ($data['exclusiones']['data'] as $exclusion)
            {
                if(array_key_exists('id',$exclusion))
                {
                    $exclusiones_viejas[$exclusion['id']] = $exclusion['id'];
                }
            }

            foreach ($this->exclusiones as $exc)
            {
                if(!array_key_exists($exc->getKey(),$exclusiones_viejas))
                {
                    $exc->delete();
                }
            }

            foreach ($data['exclusiones']['data'] as $exclusion)
            {
                if(!array_key_exists('id',$exclusion))
                {
                    Exclusion::create([
                       'id_transaccion' => $this->id_transaccion,
                        'descripcion' => $exclusion['descripcion'],
                        'unidad' => $exclusion['unidad'],
                        'cantidad' => $exclusion['cantidad'],
                        'precio_unitario' => $exclusion['precio_unitario'],
                        'id_moneda' => $exclusion['id_moneda'],
                    ]);
                }
            }

            if(key_exists('partidas', $data)){
                if(key_exists('data', $data['partidas'])){
                    $this->update([
                        'estado' => -1,
                    ]);
                    $this->invitacion->update([
                        "estado"=>2
                    ]);
                }
            }
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e);
        }
    }

    public function envia(){
        $this->update([
            'estado' => 1,
        ]);

        $this->invitacion->update([
            "estado"=>3,
            'fecha_hora_envio_cotizacion'=>date("Y-m-d H:i:s")
        ]);

        return $this->estado;
    }

    /**
     * Eliminar cotización de un proveedor
     * @param $motivo
     * @return $this
     */
    public function eliminarProveedor($motivo, $base)
    {
        if ($this->estado > 0 || $this->invitacion->estado == 3 ) {
            abort(500, "Esta cotización no puede ser eliminada porque ya ha sido enviada como respuesta a la invitación ".$this->invitacion->numero_folio_format.""
            );
        }
        try {
            DB::purge('cadeco');
            Config::set('database.connections.cadeco.database', $base);
            DB::connection('cadeco')->beginTransaction();
            $this->validar();
            $this->delete();
            $this->revisarRespaldos($motivo);
            $this->invitacion->update([
                "estado" => 1,
                "id_cotizacion_generada" => null
            ]);
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    /**
     * Elimina las exclusiones
     */
    public function eliminarExclusiones()
    {
        if($this->exclusiones)
        {
            foreach ($this->exclusiones as $exclusion) {
                $exclusion->delete();
            }
        }
    }
}
