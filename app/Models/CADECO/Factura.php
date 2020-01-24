<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:09 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Finanzas\ComplementoFactura;
use App\Models\CADECO\Finanzas\FacturaEliminada;
use App\Models\CADECO\Finanzas\TransaccionRubro;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use Illuminate\Support\Facades\DB;
use App\Facades\Context;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\SEGURIDAD_ERP\Proyecto;

class Factura extends Transaccion
{
    public const TIPO_ANTECEDENTE = 67;
    public const OPCION_ANTECEDENTE = 0;
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
                ->where('estado', '!=', -2);
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
        return $this->hasOne(FacturaRepositorio::class, 'id_transaccion', 'id_transaccion');
    }

    public function facturaRepositorioLiberar()
    {
        return $this->hasOne(FacturaRepositorio::class, 'id_transaccion', 'id_transaccion')->where('id_proyecto', '=', Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey());
    }

    public function poliza()
    {
        return $this->belongsTo(Poliza::class, 'id_transaccion', 'id_transaccion_sao');
    }

    private function registrarCR($data)
    {
        $cr = ContraRecibo::create($data["cr"]);
        if (!$cr) {
            abort(400, "Hubo un error al registrar el contrarecibo");
        }
        return $cr;
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

    private function registrarFacturaRepositorio($factura, $data)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=",$data["factura_repositorio"]["uuid"])->first();
        if($factura_repositorio){
            $factura_repositorio->id_transaccion = $factura->id_transaccion;
            $factura_repositorio->save();

        } else {
            $factura_repositorio = $factura->facturaRepositorio()->create($data["factura_repositorio"]);
            if (!$factura_repositorio) {
                abort(400, "Hubo un error al registrar la factura en el repositorio");
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
            $this->registrarFacturaRepositorio($factura, $data);
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
        if ($this->facturaRepositorioLiberar) {
            $this->facturaRepositorioLiberar->id_transaccion = null;
            $this->facturaRepositorioLiberar->id_proyecto = null;
            $this->facturaRepositorioLiberar->id_obra = null;
            $this->facturaRepositorioLiberar->usuario_asocio = null;
            $this->facturaRepositorioLiberar->fecha_hora_asociacion = null;
            $this->facturaRepositorioLiberar->save();
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
}
