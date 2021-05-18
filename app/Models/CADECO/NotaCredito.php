<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:09 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Cambio;
use App\Models\CADECO\Estimacion;
use Illuminate\Support\Facades\DB;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Finanzas\FacturaEliminada;
use App\Models\CADECO\Finanzas\TransaccionRubro;
use App\Models\CADECO\Finanzas\ComplementoFactura;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;

class NotaCredito extends Transaccion
{
    public const TIPO_ANTECEDENTE = 67;
    public const OPCION_ANTECEDENTE = 0;
    public const TIPO = 69;
    public const NOMBRE = "Nota de Crédito";
    public const ICONO = "fa fa-file-invoice";
    protected $fillable = [
        'fecha',
        "id_empresa",
        "id_moneda",
        'monto',
        "saldo",
        "referencia",
        "observaciones",
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 69);
        });
    }

    public function contraRecibo()
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

    public function facturaRepositorio()
    {
        return $this->hasOne(FacturaRepositorio::class, 'id_transaccion', 'id_transaccion')
            ->where('rfc_emisor', '=',$this->empresa->rfc)
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

    private function registrarCR($data)
    {
        $cr = ContraRecibo::create($data["cr"]);
        if (!$cr) {
            abort(400, "Hubo un error al registrar el contrarecibo");
        }
        return $cr;
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
                    abort(400, "Hubo un error al registrar el CFDI en el repositorio");
                }
            }
        }
    }

    public function registrar($data)
    {
        $nota_credito = null;
        try {
            DB::connection('cadeco')->beginTransaction();
            $cr = $this->registrarCR($data);
            $nota_credito = $cr->notasCredito()->create($data["nota_credito"]);

            $this->registrarCFDRepositorio($nota_credito, $data["nc_repositorio"]);
            DB::connection('cadeco')->commit();
            return $nota_credito;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
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

    public function scopeConDocumento($query)
    {
        return $query->has('documento');
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

}
