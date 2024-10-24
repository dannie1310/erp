<?php

namespace App\Models\REPSEG;

use App\Facades\Context;
use App\Models\CORREOS\EmailRegister;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VwFinIngresoRegistrado extends Model
{
    protected $connection = 'repseg';
    protected $table = 'vw_fin_ingreso_registrado';
    protected $primaryKey = 'idingreso_registrado';

    /**
     * Relaciones
     */
    public function ingreso()
    {
        return $this->belongsTo(FinFacIngresoRegistrado::class, 'idingreso_registrado', 'idingreso_registrado');
    }

    /**
     * Scopes
     */

    /**
     * Attributes
     */
    public function getTotalFormatAttribute()
    {

        return "$" . number_format($this->total, 2, ".", ",");
    }

    /**
     * Métodos
     */
    public function buscarEmail()
    {
        return EmailRegister::whereRaw("asunto like 'Ingreso Registrado (PRUEBAS DESARROLLO-OPERACIÓN)'
         and body like '%".$this->cuenta_ingresa."%' and body like '%".$this->fecha."%' and body like '%".$this->total." ".$this->moneda."%'")->first();
    }

    public function registrarEmail()
    {
        try {
            DB::connection('correos')->beginTransaction();
            $email = EmailRegister::create([
                'remitente' => 'monitoreodb_hermes@grupohi.mx|Finanzas',
                'destinatarios' => $this->ingreso->getToSeparado(),
                'asunto' => 'Ingreso Registrado (' . $this->proyecto . '-' . $this->proyecto_tipo . ')',
                'cc' => $this->ingreso->getCCSeparado(),
                'cco' => $this->ingreso->getCCOSeparado(),
                'status' => 0,
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'descripcion' => 'seginfo - Finanzas',
                'body' => $this->cuerpoEmailIngreso()
            ]);
            DB::connection('correos')->commit();
            return $email;
        } catch (\Exception $e) {
            DB::connection('correos')->rollBack();
            abort(500, $e->getMessage());
            throw $e;
        }
    }

    public function cuerpoEmailIngreso()
    {
        $usuario = $this->ingreso->usuario;
        $factura= $this->ingreso->factura;
        $saldo = $factura->total_detalle - $this->total;
        return '<html>
                <head>
                    <style>
                        #cuerpo{
                            margin:0;
                            padding:10px;
                            font-family:Arial, Helvetica, sans-serif;
                            font-size:12px;
                            border:solid 1px #ccc;
                            width:100%;
                            background-color:#fff;
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
                </head>
                <body style="height:100%; width:100%;" bgcolor="#73C239">
                    <div id="cuerpo">
                        <div id="header" align="center">
                            <img src="http://seguimiento.grupohi.mx/assets/img/logo4.fw.png"><br><br>
                            <h3>Ingreso Registrado ('.$this->proyecto.'-'.$this->proyecto_tipo.')</h3>
                        </div>
                        <br>
                        <span style="font-weight:bold">Se le notifica que ' . $usuario->nombre . ' ' . $usuario->apaterno . ' ' . $usuario->amaterno . ' ha registrado un nuevo ingreso</span>
                        <br><br>
                        <table cellpadding="0" cellspacing="0" style="width:100%;">
                        <tr>
                            <td class="campo" valign="top" width="150px;">FECHA INGRESO</td>
                            <td class="valor" valign="top">' . $this->fecha . '</td>
                        </tr>
                        <tr>
                            <td class="campo" valign="top">ÁREA</td>
                            <td class="valor" valign="top">' . $this->proyecto_tipo . '</td>
                        </tr>
                        <tr>
                            <td class="campo" valign="top">PROYECTO</td>
                            <td class="valor" valign="top">' . $this->proyecto . '</td>
                        </tr>
                        <tr>
                            <td class="campo" valign="top">MONTO DEL INGRESO</td>
                            <td class="valor" valign="top">' . number_format($this->total, 2) . ' ' . $this->moneda . '</td>
                        </tr>
                        <tr>
                            <td class="campo" valign="top">CTA A LA QUE INGRESA</td>
                            <td class="valor" valign="top">' . $this->cuenta_ingresa . '</td>
                        </tr>
                        <tr>
                        <td class="campo" valign="top">EMPRESA</td>
                        <td class="valor" valign="top">' . $factura->empresa->empresa . '</td>
                    </tr>
                    <tr>
                        <td class="campo" valign="top">CLIENTE</td>
                        <td class="valor" valign="top">' . $factura->cliente->cliente . '</td>
                    </tr>
                    </table>
                    <br>
                    <b>Facturas:</b><br><br>
                    <table style="font-size:10px;" cellpadding="0" cellspacing="0" width="100%">
                        <tr style="background-color:#888; color:#fff;">
                            <td style="padding:4px;">FECHA</td>
                            <td style="padding:4px;">FACTURA</td>
                            <td style="padding:4px;">DESCRIPCION</td>
                            <td style="padding:4px;">PERIODO</td>
                            <td style="padding:4px;">TOTAL FACTURA</td>
                            <td style="padding:4px;">PAGADO</td>
                            <td style="padding:4px;">POR PAGAR</td>
                        </tr>
                        <tr style="background-color: "#fff">
                            <td>' . $factura->fecha . '</td>
                            <td>' . $factura->numero . '</td>
                            <td>' . $factura->descripcion . '</td>
                            <td>' . utf8_encode($factura->fecha_fi_format.' AL '.$factura->fecha_ff_format) . '</td>
                            <td align="right">' . $factura->total_sin_signo . ' ' . $this->moneda . '</td>
                            <td align="right">' . number_format($this->total, 2) . ' ' . $this->moneda . '</td>
                            <td align="right">' . number_format($saldo, 2) . ' ' . $this->moneda . '</td>
                        </tr>
                    </table>
                    <br>
                    <i><strong>
                        <span class="leyenda">
                        Mensaje enviado automáticamente desde el Módulo de Información Ejecutiva - SAOERP
                        <br>SAO.- Grupo Hermes Infraestructura
                        </span>
                    </strong></i>
                </div></body></html>';
    }
}
