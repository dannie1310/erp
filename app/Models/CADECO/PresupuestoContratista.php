<?php

namespace App\Models\CADECO;

use App\Models\CADECO\Contratos\AsignacionSubcontratoPartidas;
use App\Models\CADECO\Contratos\PresupuestoContratistaEliminado;
use App\Models\IGH\Usuario;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

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

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function getUsdFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcUSD),4);
    }

    public function getEuroFormatAttribute()
    {
        return '$ ' . number_format(abs($this->TcEuro),4);
    }
}
