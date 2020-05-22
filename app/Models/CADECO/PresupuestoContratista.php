<?php

namespace App\Models\CADECO;

use App\Models\CADECO\Contratos\AsignacionSubcontratoPartidas;
use App\Models\IGH\Usuario;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class PresupuestoContratista extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;

    protected $fillable = [
        'id_transaccion',
        'fecha',
        'monto',
        'impuesto',
        'anticipo',
        'observaciones',
        'PorcentajeDescuento',
        'TcUSD',
        'TcEuro',
        'DiasCredito',
        'DiasVigencia'
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

    public function validarAsignacion($motivo)
    {
        if($this->asignacion)
        {
            throw New \Exception('No se puede '. $motivo.' el presupuesto '. $this->numero_folio_format .' debido a que ya han sido asignados algunos materiales');
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
