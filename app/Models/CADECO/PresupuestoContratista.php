<?php

namespace App\Models\CADECO;

use App\CSV\PresupuestoLayout;
use App\Models\CADECO\Contratos\AsignacionSubcontratoPartidas;
use App\Models\CADECO\Contratos\PresupuestoContratistaEliminado;
use App\Models\IGH\Usuario;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PresupuestoContratista extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;
    public const OPCION_ANTECEDENTE = 1026;


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
        'contratoProyectado.referencia'
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

    public function asignacion()
    {
        return $this->hasOne(AsignacionSubcontratoPartidas::class, 'id_transaccion');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    /**
     * Scopes
     */



    /**
     * Attributes
     */
    public function getUsdFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcUSD),4);
    }

    public function getEuroFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcEuro),4);
    }

    /**
     * Métodos
     */
    public function descargaLayout($id)
    {
        $find = $this::find($id);
        return Excel::download(new PresupuestoLayout($find), str_replace('/', '-',$find->contratoProyectado->referencia).'.xlsx');
    }

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

    public function precioConvercion($precio, $id_moneda, $monedas)
    {
        switch($id_moneda)
        {
            case(1):
                return ($precio * 1);
            break;
            case(2):
                return ($precio * $monedas[0]->cambioIgh->tipo_cambio);
            break;
            case(3):
                return ($precio * $monedas[1]->cambioIgh->tipo_cambio);
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
            $moneda = Moneda::get();
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
                    'TcUSD' => $moneda[0]->cambioIgh->tipo_cambio,
                    'TcEuro' => $moneda[1]->cambioIgh->tipo_cambio,
                    'DiasCredito' => $data['credito'],
                    'DiasVigencia' => $data['vigencia']
                ]);

                $t = 0;
                foreach($data['partidas'] as $partida)
                {
                    $precio_unitario = $this->precioConvercion($data['precio'][$t], $data['moneda'][$t], $moneda);
                    $presupuesto->partidas()->create([
                        'id_transaccion' => $presupuesto->id_transaccion,
                        'id_concepto' => $partida['id_concepto'],
                        'precio_unitario' => ($data['enable'][$t]) ? $precio_unitario : null,
                        'no_cotizado' => ($data['enable'][$t]) ? 0 :1,
                        'PorcentajeDescuento' => ($data['enable'][$t]) ? $data['descuento'][$t] : null,
                        'IdMoneda' => $data['moneda'][$t],
                        'Observaciones' => ($data['observaciones'][$t]) ? $data['observaciones'][$t] : ''
                    ]);
                    $t ++;
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
                    'DiasCredito' => null,
                    'DiasVigencia' => null
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
            abort(400, $e->getMessage());
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
                    'TcUSD' => $data['tipo_cambio'][2],
                    'TcEuro' => $data['tipo_cambio'][3],
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
                       $precio =  ($data['moneda'][$x] == 2) ? ($data['precio'][$x] * $data['tipo_cambio'][2]) : ($data['precio'][$x] * $data['tipo_cambio'][3]);
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

    public function datosComparativos()
    {
        $partidas = [];
        $presupuestos = [];
        $precios = [];

        foreach ($this->contratoProyectado->conceptos()->orderBy('descripcion','asc')->get()  as $key => $item) {
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
                    if($p->precio_unitario > 0 && $precios[$p->id_concepto] > $p->precio_unitario_convert)
                        $precios[$p->id_concepto] = (float) $p->precio_unitario_convert;
                } else {
                    if($p->precio_unitario > 0) {
                        $precios[$p->id_concepto] = (float) $p->precio_unitario_convert;
                    }
                }
                if (array_key_exists($p->id_concepto, $partidas)) {
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['id_transaccion'] = $presupuesto->id_transaccion;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['precio_unitario'] = $p->precio_unitario_convert;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['precio_total_moneda'] = $p->total_precio_moneda;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['precio_total'] = $p->precio_unitario_convert * $partidas[$p->id_concepto]['cantidad_presupuestada'];
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['tipo_cambio_descripcion'] = $p->moneda ? $p->moneda->abreviatura : '';
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['descuento_partida'] = $p->partida ? $p->partida->descuento_partida : 0;
                    $partidas[$p->id_concepto]['presupuestos'][$cont]['observaciones'] = $p->partida ? $p->partida->observaciones : '';
                }
            }
        }
        return [
            'presupuestos' => $presupuestos,
            'partidas' => $partidas,
            'precios_menores' => $precios
        ];
    }

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

    public function sumaSubtotalPartidas($tipo_moneda)
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
            if($tipo_moneda == $partida->IdMoneda)
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
            if($tipo_moneda == $partida->IdMoneda)
            {
                $suma += $partida->precio_compuesto_total;
            }
        }
        return $suma;
    }

    public function calcular_ki($precio, $precio_menor)
    {
        return $precio_menor == 0 ?  ($precio - $precio_menor) : ($precio - $precio_menor) / $precio_menor;
    }
}
