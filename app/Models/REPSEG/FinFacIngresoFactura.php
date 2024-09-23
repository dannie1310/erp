<?php


namespace App\Models\REPSEG;


use App\Models\CORREOS\EmailRegister;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDIEmitido;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinFacIngresoFactura extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_fac_ingreso_factura';
    protected $primaryKey = 'idfactura';
    public $timestamps = false;
    protected $fillable = [
        'numero',
        'fecha',
        'idproyecto',
        'idempresa',
        'idcliente',
        'descripcion',
        'fi_cubre',
        'ff_cubre',
        'idmoneda',
        'tipo_cambio',
        'importe',
        'registra',
        'timestamp',
        'fecha_cobro',
        'estado',
        'usuario_cancelo',
        'motivo_cancelacion',
        'fecha_cancelacion',
        'uuid'
    ];

    public $searchable = [
        'numero',
        'fecha',
        'proyecto.proyecto',
        'empresa.empresa',
        'cliente.cliente',
        'descripcion'
    ];

    /**
     * Relaciones
     */
    public function proyecto()
    {
        return $this->belongsTo(GrlProyecto::class, 'idproyecto', 'idproyecto');
    }

    public function empresa()
    {
        return $this->belongsTo(FinDimIngresoEmpresa::class, 'idempresa', 'idempresa');
    }

    public function cliente()
    {
        return $this->belongsTo(FinDimIngresoCliente::class, 'idcliente', 'idcliente');
    }

    public function moneda()
    {
        return $this->belongsTo(GrlMoneda::class, 'idmoneda', 'idmoneda');
    }

    public function conceptos()
    {
        return $this->hasMany(FinFacIngresoFacturaConcepto::class,  'idfactura','idfactura');
    }

    public function partidas()
    {
        return $this->hasMany(FinFacIngresoFacturaDetalle::class, 'idfactura', 'idfactura');
    }

    public function partidasSinTotales()
    {
        return $this->hasMany(FinFacIngresoFacturaDetalle::class, 'idfactura', 'idfactura')->whereNotIn('idpartida', [15,16,17]);
    }

    public function CFDI()
    {
        return $this->belongsTo(CFDIEmitido::class, 'idfactura','idfactura');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'registra','idusuario');
    }

    /**
     * Scopes
     */


    /**
     * Atributos
     */
    public function getNombreProyectoAttribute()
    {
        try{
            return $this->proyecto->proyecto;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getNombreEmpresaAttribute()
    {
        try{
            return $this->empresa->empresa;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getNombreMonedaAttribute()
    {
        try{
            return $this->moneda->moneda;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getNombreClienteAttribute()
    {
        try{
            return $this->cliente->cliente;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaFiFormatAttribute()
    {
        $date = date_create($this->fi_cubre);
        return date_format($date,"d/m/Y");
    }

    public function getFechaFfFormatAttribute()
    {
        $date = date_create($this->ff_cubre);
        return date_format($date,"d/m/Y");
    }

    public function getFechaCobroFormatAttribute()
    {
        $date = date_create($this->fecha_cobro);
        return date_format($date,"d/m/Y");
    }

    public function getFechaRegistroFormatAttribute()
    {
        $date = date_create($this->timestamp);
        return date_format($date,"d/m/Y");
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format($this->importe,2);
    }

    public function getEstadoDescripcionAttribute()
    {
        switch ($this->estado)
        {
            case 1:
                return 'Registrada';
                break;

            case 2:
                return 'Pagada';
                break;

            case 3:
                return 'Cancelada';
                break;
            default:
                return 'Desconocido';
                break;
        }
    }

    public function getEstadoColorAttribute()
    {
        switch ($this->estado) {
            case 1:
                return '#f39c12';
                break;

            case 2:
                return '#00a65a';
                break;

            case 3:
                return '#e50c25';
                break;
            default:
                return '#d2d6de';
                break;
        }
    }

    public function getSubtotalFormatAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 15)->first()->total_format;

        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getIvaFormatAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 16)->first()->total_format;

        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getTotalFormatAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 17)->first()->total_format;

        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getTotalAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 17)->first()->total_format;

        } catch (\Exception $exception) {
            return 0;
        }
    }

    public function getTotalSinSignoAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 17)->first()->total_sin_signo_format;

        } catch (\Exception $exception) {
            return 0;
        }
    }

    public function getTotalDetalleAttribute()
    {
        try {
            return $this->partidas()->where('idpartida', 17)->pluck('total')->first();

        } catch (\Exception $exception) {
            return 0;
        }
    }

    /**
     * Métodos
     */
    public function cancelar($motivo)
    {
        $this->validarCancelacion();
        try {
            DB::connection('repseg')->beginTransaction();
            $this->update([
                'estado' => 3,
                'usuario_cancelo' => auth()->id(),
                'motivo_cancelacion' => $motivo,
                'fecha_cancelacion' => date('Y-m-d H:i:s')
            ]);
            if($this->CFDI)
            {
                $this->CFDI->update([
                    'estado' => -1,
                    'cancelado' => -1
                ]);
            }
            DB::connection('repseg')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('repseg')->rollBack();
            throw $e;
        }
    }

    public function registrar($data)
    {
        try {
            DB::connection('repseg')->beginTransaction();
            $datos_factura = array_except($data, 'conceptos');
            $datos_factura = array_except($datos_factura, 'partidas');
            $datos_factura = array_except($datos_factura, 'importe_conceptos');
            $datos_factura = array_except($datos_factura, 'iva');
            $datos_factura = array_except($datos_factura, 'total');
            $datos_factura = array_except($datos_factura, 'importe_partidas_antes');
            $datos_factura = array_except($datos_factura, 'importe_partidas_despues');
            $fecha = new DateTime($data["fecha"]);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fi_cubre = new DateTime($data["fi_cubre"]);
            $fi_cubre->setTimezone(new DateTimeZone('America/Mexico_City'));
            $ff_cubre = new DateTime($data["ff_cubre"]);
            $ff_cubre->setTimezone(new DateTimeZone('America/Mexico_City'));
            $datos_factura['fecha'] = $fecha->format('Y-m-d');
            $datos_factura['fi_cubre'] = $fi_cubre->format('Y-m-d');
            $datos_factura['ff_cubre'] = $ff_cubre->format('Y-m-d');

            $factura = $this->create($datos_factura);

            foreach (array_only($data, 'conceptos')['conceptos'] as $concepto) {
                if(array_key_exists('clave_sat',$concepto))
                {
                    $clave_sat = FinDimClaveSatIngreso::where('idclave_sat', $concepto['clave_sat'])->where('idtipo_ingreso', $concepto['idconcepto'])->first();
                    if ($clave_sat == null) {
                        FinDimClaveSatIngreso::create([
                            'idclave_sat' => $concepto['clave_sat'],
                            'idtipo_ingreso' => $concepto['idconcepto']
                        ]);
                    }
                }
                $factura->conceptos()->create($concepto);
            }

            if (array_key_exists('partidas', $data)) {
                foreach (array_only($data, 'partidas')['partidas'] as $partida) {
                    $factura->partidas()->create($partida);
                }
            }
            if (array_key_exists('retencionesLocales', $data)) {
                if ($data['retencionesLocales'] != []) {
                    foreach (array_only($data, 'retencionesLocales')['retencionesLocales'] as $retencion) {
                        $factura->partidas()->create([
                            'idfactura' => $factura->idfactura,
                            'idpartida' => $retencion['id'],
                            'antes_iva' => $retencion['antes_iva'],
                            'total' => $retencion['total'],
                        ]);
                    }
                }
            }

            $factura->partidas()->create([
                'idfactura' => $factura->getKey(),
                'idpartida' => 15,
                'total' => $data['importe']
            ]);

            $factura->partidas()->create([
                'idfactura' => $factura->getKey(),
                'idpartida' => 16,
                'total' => $data['iva']
            ]);

            $factura->partidas()->create([
                'idfactura' => $factura->getKey(),
                'idpartida' => 17,
                'total' => $data['total']
            ]);

            DB::connection('repseg')->commit();
            return $factura;
        }catch (\Exception $e) {
            DB::connection('repseg')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function getToNotificacionIngreso()
    {
        return GrlNotificacion::activo()->seccion(1)->proyecto($this->idproyecto)->where('tipo', 'TO')->pluck('cuenta');
    }

    public function getCCNotificacionIngreso()
    {
        return GrlNotificacion::activo()->seccion(1)->proyecto($this->idproyecto)->where('tipo', 'CC')->pluck('cuenta');
    }

    public function getCCONotificacionIngreso()
    {
        return GrlNotificacion::activo()->seccion(1)->proyecto($this->idproyecto)->where('tipo', 'CCO')->pluck('cuenta');
    }

    public function validarCancelacion()
    {
        if($this->estado != 1)
        {
            abort(500, "La factura no se puede cancelar por que su estado cambio a ".$this->estado_descripcion);
        }
    }
}
