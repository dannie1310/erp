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
        return $this->belongsTo(Usuario::class, 'idusuario','registra');
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

    /**
     * Métodos
     */
    public function cancelar($motivo)
    {
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

            //$this->enviar_correo($factura);

            DB::connection('repseg')->commit();
            return $factura;
        }catch (\Exception $e) {
            DB::connection('repseg')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function enviar_correo($factura)
    {
        $notificaciones =  GrlNotificacion::activo()->seccion(1)->proyecto($factura->idproyecto)->get();
        $correos = array();
        $correosCC = array();
        $correosCCO =array();
        foreach ($notificaciones as $notificacion)
        {
            if($notificacion['tipo'] == 'TO')
            {
                $correos[] = $notificacion['cuenta'];
            }
            if($notificacion['tipo'] == 'CC')
            {
                $correosCC[] = $notificacion['cuenta'];
            }
            if($notificacion['tipo'] == 'CCO')
            {
                $correosCCO[] = $notificacion['cuenta'];
            }
        }
        $body = $this->cuerpoCorreo($factura, 'Registrada', 'registrado una nueva');
        $email = EmailRegister::create([
            'remitente' => 'seguimiento@hermesconstruccion.com.mx|Finanzas',
            'destinatarios' => implode(';', $correos),
            'asunto' => 'Factura Registrada ('.$factura->proyecto->proyecto.').',
            'cc' => implode(';', $correosCC),
            'cco' => implode(';', $correosCCO),
            'status' => 0,
            'fecha' => now()->format('Y-m-d'),
            'hora' => now()->format('H:i:s'),
            'descripcion' => 'seginfo - Finanzas - SAOERP',
            'body' => utf8_encode($body)
        ]);
    }

    private function cuerpoCorreo($factura, $subject, $leyenda)
    {
        $body = '
            <style>
                #cuerpo{
                    margin:0;
                    padding:10px;
                    font-family:Arial, Helvetica, sans-serif;
                    font-size:12px;
                    border:solid 1px #ccc;
                    width:600px;
                    max-width:600px;
                    }
                #header{
                    background-color:#efefef;
                    padding:10px;
                    }
                .campo{
                    background-color:#888888;
                    color:#ffffff;
                    font-weight:300;
                    padding:5px;
                    font-size:12px;
                    text-align:right;
                    }
                .valor{
                    background-color:#efefef;
                    padding:5px;
                    font-size:12px;
                    }
                .leyenda{
                    font-size:10px;
                    }
            </style>
            <div id="cuerpo">
                <div id="header" align="center">
                    <img src="http://seguimiento.grupohi.mx/assets/img/logo4.fw.png"><br><br>
                    <h3>Factura ' . $subject . '</h3>
                </div>
                <br>
                <span style="font-weight:bold">Se le notifica que ' . auth()->user()->nombre . ' ' . auth()->user()->apaterno . ' ' . auth()->user()->amaterno . ' ha ' . $leyenda . ' factura</span>
                <br><br>
                <table cellpadding="0" cellspacing="0" style="width:100%;">
                <tr>
                    <td class="campo" valign="top">FECHA</td><td class="valor" valign="top">' . $factura->fecha . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">REFERENCIA</td><td class="valor" valign="top">' . $factura->numero . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">PROYECTO</td><td class="valor" valign="top">' . utf8_encode($factura->proyecto->proyecto) . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">AREA</td><td class="valor" valign="top">' . utf8_decode($factura->proyecto->tipo->proyecto_tipo) . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">EMPRESA</td><td class="valor" valign="top">' . utf8_decode($factura->empresa->empresa) . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">CLIENTE</td><td class="valor" valign="top">' . utf8_decode($factura->cliente->cliente) . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">PERIODO</td><td class="valor" valign="top">' . $factura->fi_cubre . ' al ' . $factura->ff_cubre . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">TOTAL DE FACTURA</td><td class="valor" align="center" valign="top">' . number_format($factura->importe, 2) . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">MONEDA</td><td class="valor" valign="top">' . $factura->moneda->moneda . '</td>
                </tr>
                <tr>
                    <td class="campo" valign="top">DESCRIPCION</td><td class="valor" valign="top">' . utf8_encode($factura->descripcion) . '</td>
                </tr>
                <tr>
                    <td class="campo" style="background-color:#FFF; color:#444; font-size:10px;" colspan="2">CONCEPTOS</td>
                </tr>';

        foreach ($factura->conceptos as $concepto) {
            $body .= '<tr><td class="campo" valign="top">' . $concepto->tipoIngreso->tipo_ingreso . '</td><td class="valor" align="center" valign="top">' . number_format($concepto->importe, 2) . '</td></tr>';
        }

        $body .= '</table>
                    <br>
                    <i><strong>
                    <span class="leyenda">
                    Mensaje enviado automaticamente desde el módulo de registro de ingresos SAOERP
                    <br>SAO - Grupo Hermes Infraestructura.
                    </span>
                    </strong></i>
                </div>';
        return $body;
    }

    public function getToNotificacionIngreso()
    {
        $notificaciones = GrlNotificacion::activo()->seccion(1)->proyecto($this->idproyecto)->where('tipo', 'TO')->get();
        $correos = array();
        foreach ($notificaciones as $notificacion)
        {
            $correos[] = $notificacion['cuenta'];
        }
        return $correos;
    }

    public function getCCNotificacionIngreso()
    {
        $notificaciones =  GrlNotificacion::activo()->seccion(1)->proyecto($this->idproyecto)->where('tipo', 'CC')->get();
        $correos = array();
        foreach ($notificaciones as $notificacion)
        {
            $correos[] = $notificacion['cuenta'];
        }
        return $correos;
    }

    public function getCCONotificacionIngreso()
    {
        $notificaciones = GrlNotificacion::activo()->seccion(1)->proyecto($this->idproyecto)->where('tipo', 'CCO')->get();
        $correos = array();
        foreach ($notificaciones as $notificacion)
        {
            $correos[] = $notificacion['cuenta'];
        }
        return $correos;
    }
}
