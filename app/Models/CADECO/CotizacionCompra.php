<?php


namespace App\Models\CADECO;


use App\CSV\CotizacionLayout;
use App\Models\CADECO\Compras\AsignacionProveedoresPartida;
use App\Models\CADECO\Compras\CotizacionComplemento;
use App\Models\CADECO\Compras\CotizacionComplementoPartida;
use DateTime;
use DateTimeZone;
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
            ->where('opciones','=', 1)->where('estado', '!=', 2);
        });
    }

    public function cotizaciones() {
        return $this->hasMany(Cotizacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(CotizacionComplemento::class, 'id_transaccion', 'id_transaccion');
    }

    public function descargaLayout($id)
    {
        return Excel::download(new CotizacionLayout(CotizacionCompra::find($id)), 'LayoutCotizacion.xlsx');
    }

    public function asignacionPartida()
    {
        return $this->hasOne(AsignacionProveedoresPartida::class, 'id_transaccion_cotizacion', 'id_transaccion');
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

    public function validarAsignacion($motivo)
    {
        if($this->asignacionPartida)
        {
            throw New \Exception('No se puede '. $motivo.' la cotización '. $this->numero_folio_format .' debido a que ya han sido asignados algunos materiales');
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
                foreach($data['partidas'] as $partida)
                {
                    $item = Cotizacion::where('id_material', '=', $partida['material']['id'])->where('id_transaccion', '=', $this->id_transaccion);
                    $item->update([
                        'precio_unitario' => ($data['enable'][$i]) ? $data['precio'][$i] : 0,
                        'descuento' => ($data['enable'][$i] !== false) ? ($data['descuento_cot'] + $data['descuento'][$i] - (($data['descuento_cot'] * $data['descuento'][$i]) / 100)) : 0,
                        'no_cotizado' => (!$data['enable'][$i]) ? 1 : 0,
                        'id_moneda' => ($data['enable'][$i]) ? $data['moneda'][$i] : null
                    ]);

                    if($item->first()->partida)
                    {
                        CotizacionComplementoPartida::where('id_transaccion', '=', $this->id_transaccion)
                        ->where('id_material', '=', $partida['material']['id'])->update([
                            'descuento_partida' => ($data['enable'][$i]) ? $data['descuento'][$i] : 0,
                            'observaciones' => ($data['enable'][$i]) ? $partida['observacion'] : null,
                            'estatus' => ($data['enable'][$i]) ? 3 : 1
                        ]);
                    }
                    else{
                        CotizacionComplementoPartida::create([
                            'id_transaccion' => $this->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'descuento_partida' => ($data['enable'][$i]) ? $data['descuento'][$i] : 0,
                            'observaciones' => ($data['enable'][$i]) ? $partida['observacion'] : null,
                            'estatus' => ($data['enable'][$i]) ? 3 : 1
                        ]);
                    }
                $i ++;
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
                'tc_usd' => $moneda[0]->cambioIgh->tipo_cambio,
                'tc_eur' => $moneda[1]->cambioIgh->tipo_cambio,
                'anticipo' => $data['anticipo'],
                'importe' => $data['importe'],
                'timestamp_registro' => $fecha->format("Y-m-d")
            ]);
            $x = 0;
            $conteo = array();
            foreach($data['partidas'] as $partida)
            {
                if($x < count($data['precio']))
                {
                    if($x < count($data['enable']))
                    {
                        #------- dbo.cotizaciones

                        $cotizaciones = $cotizacion->cotizaciones()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'cantidad' => ($solicitud->estado == 1) ? $partida['cantidad'] : $partida['cantidad_original_num'],
                            'precio_unitario' => ($data['enable'][$x] !== false) ? $data['precio'][$x] : 0,
                            'descuento' => ($data['enable'][$x] !== false) ? ($data['descuento_cot'] + $data['descuento'][$x] - (($data['descuento_cot'] * $data['descuento'][$x]) / 100)) : 0,
                            'anticipo' => ($data['enable'][$x] !== false) ? $data['anticipo'] : 0,
                            'no_cotizado' => ($data['enable'][$x] !== false) ? 0 : 1,
                            'disponibles' => ($data['enable'][$x] !== false) ? 1 : 0,
                            'id_moneda' => ($data['enable'][$x] !== false) ? $data['moneda'][$x] : null
                        ]);
                        #------- Compras.cotizacion_partidas_complemento

                        $cotizaciones->partida()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'descuento_partida' => ($data['enable'][$x] !== false) ? $data['descuento'][$x] : 0,
                            'observaciones' => ($data['enable'][$x] !== false) ? $data['observaciones'][$x] : null,
                            'estatus' => ($data['enable'][$x] !== false) ? 3 : 1
                        ]);
                    }else
                   {
                       #------- dbo.cotizaciones

                       $cotizaciones = $cotizacion->cotizaciones()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'cantidad' => ($solicitud->estado == 1) ? $partida['cantidad'] : $partida['cantidad_original_num'],
                            'precio_unitario' => $data['precio'][$x],
                            'descuento' => ($data['descuento_cot'] + $data['descuento'][$x] - (($data['descuento_cot'] * $data['descuento'][$x]) / 100)),
                            'anticipo' => $data['anticipo'],
                            'no_cotizado' => 0,
                            'disponibles' => 1,
                            'id_moneda' => $data['moneda'][$x]
                       ]);
                       #------- Compras.cotizacion_partidas_complemento

                       $cotizaciones->partida()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'descuento_partida' => $data['descuento'][$x],
                            'observaciones' => $data['observaciones'][$x],
                            'estatus' => 3
                       ]);
                    }
                }
                else
                {
                        #------- dbo.cotizaciones

                        $cotizaciones = $cotizacion->cotizaciones()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'cantidad' => ($solicitud->estado == 1) ? $partida['solicitado_cantidad'] : $partida['cantidad_original'],
                            'precio_unitario' => 0,
                            'descuento' => 0,
                            'anticipo' => 0,
                            'disponibles' => 0,
                            'no_cotizado' => 1,
                            'id_moneda' => null
                        ]);
                        #------- Compras.cotizacion_partidas_complemento
                        
                        $cotizaciones->partida()->create([
                            'id_transaccion' => $cotizacion->id_transaccion,
                            'id_material' => $partida['material']['id'],
                            'descuento_partida' => 0,
                            'observaciones' => null,
                            'estatus' => 1
                        ]);
                }
                $x ++;
            }

            DB::connection('cadeco')->commit();
            return $cotizacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function scopeConEmpresa(){
        return $this->whereNotNull('id_empresa');
    }
}