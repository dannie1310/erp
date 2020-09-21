<?php


namespace App\Models\CADECO;


use App\CSV\CotizacionLayout;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Models\CADECO\Compras\CotizacionComplemento;
use App\Models\CADECO\Compras\CotizacionComplementoPartida;
use App\Models\CADECO\Compras\CotizacionEliminada;
use App\Models\CADECO\Compras\CotizacionPartidaEliminada;
use App\Models\CADECO\Compras\SolicitudComplemento;
use DateTime;
use DateTimeZone;
use Dingo\Blueprint\Annotation\Attributes;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionCompra  extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;
    public const OPCION_ANTECEDENTE = 1;

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'id_empresa',
        'id_sucursal',
        'id_moneda',
        'tipo_transaccion',
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
        'empresa.razon_social'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 18)
            ->where('opciones','=', 1)->where('estado', '!=', 2)->whereHas('complemento');
        });
    }

    /**
     * Relaciones
     */
    public function partidas()
    {
        return $this->hasMany(CotizacionCompraPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(CotizacionComplemento::class, 'id_transaccion', 'id_transaccion');
    }

    public function descargaLayout($id)
    {
        $find = CotizacionCompra::find($id);
        $folio = str_pad($find->numero_folio, 5, 0, 0);
        return Excel::download(new CotizacionLayout($find), '#'.$folio.'.xlsx');
    }

    public function asignacionPartida()
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

    public function getIVAPartidasAttribute()
    {
        return $this->suma_subtotal_partidas * 0.16;
    }

    public function getTotalPartidasAttribute()
    {
        return $this->suma_subtotal_partidas + $this->iva_Partidas;
    }

    public function getDescuentoAttribute()
    {
        return $this->suma_subtotal_partidas * $this->complemento->descuento/100;
    }

    public function getSubtotalConDescuentoAttribute()
    {
        return $this->suma_subtotal_partidas - $this->descuento;
    }

    public function getIVAConDescuentoAttribute()
    {
        return $this->subtotal_con_descuento * 0.16;
    }

    public function getTotalConDescuentoAttribute()
    {
        return $this->subtotal_con_descuento + $this->iva_con_descuento;
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
                $this->complemento->tc_usd = $data['tipo_cambio'][2];
                $this->complemento->tc_eur = $data['tipo_cambio'][3];
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
                    'tc_usd' => $data['tipo_cambio'][2],
                    'tc_eur' => $data['tipo_cambio'][3],
                    'timestamp_registro' => $fecha->format("Y-m-d")
                ]);
            }

            $i = 0;
            foreach($data['partidas'] as $partida) {
                $item = CotizacionCompraPartida::where('id_material', '=', $partida['material']['id'])->where('id_transaccion', '=', $this->id_transaccion)->first();
                if ($item) {
                    $item->update([
                        'precio_unitario' => ($data['enable'][$i]) ? $data['precio'][$i] : 0,
                        'descuento' => ($data['enable'][$i] !== false) ? ($data['descuento_cot'] + $data['descuento'][$i] - (($data['descuento_cot'] * $data['descuento'][$i]) / 100)) : 0,
                        'no_cotizado' => (!$data['enable'][$i]) ? 1 : 0,
                        'id_moneda' => ($data['enable'][$i]) ? $data['moneda'][$i] : null
                    ]);
                    if ($item->partida) {
                        $item->partida->update([
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
                            'cantidad' => ($this->solicitud->estado == 1) ? $partida['cantidad'] : $partida['cantidad_original_num'],
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
                    'tc_usd' => $moneda[1]->cambio->cambio,
                    'tc_eur' => $moneda[2]->cambio->cambio,
                    'tc_libra' => $moneda[3]->cambio->cambio,
                    'anticipo' => $data['anticipo'],
                    'importe' => $data['importe'],
                    'timestamp_registro' => $fecha->format("Y-m-d")
                ]);

                foreach($data['partidas'] as $key => $partida) {
                    if($data['enable'] == [] || !array_key_exists($key,$data['enable']) || (is_null($data['enable'][$key]) || $data['enable'][$key] == true)) {
                        $cotizaciones = $cotizacion->partidas()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'cantidad' => ($solicitud->estado == 1) ? $partida['cantidad'] : $partida['cantidad_original_num'],
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
                            'observaciones' => $data['observaciones'] ? $data['observaciones'][$key] : '',
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
                        'cantidad' => ($solicitud->estado == 1) ? $partida['cantidad'] : $partida['cantidad_original_num'],
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
}
