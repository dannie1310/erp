<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 01/10/2019
 * Time: 07:04 PM
 */

namespace App\Repositories\CADECO\Finanzas\LayoutPago;

use App\Models\CADECO\Finanzas\LayoutPago;
use App\Facades\Context;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\Transaccion;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\CADECO\Obra;
use DateTime;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param LayoutPago $model
     */
    public function __construct(LayoutPago $model)
    {
        $this->model = $model;
    }

    public function validarLayout($layout)
    {
        $registros= array();
        $hash_file =$this->model->validarArchivo($layout);

        foreach ($this->getCSVData($layout) as $key => $pago) {
            $documento = Documento::query()->has('remesa')->where("IDTransaccionCDC", "=", $pago['id_transaccion'])->first();
            $transaccion = "";
            $pago_a_generar = "";
            $cuenta_encontrada = true;
            $cuentas = [];
            $id_cuenta = "";
            $tipo_cuenta_moneda = '';
            $cta_cargo = Cuenta::query()->where('numero', $pago['cuenta_cargo'])->where('id_tipo_cuentas_obra', '=', 1)->first();
            $factura = Factura::query()->where('id_transaccion', '=', $pago['id_transaccion'])->first();
            $factura = Factura::find($pago['id_transaccion']);
            $solicitud = Solicitud::query()->where('id_transaccion', '=', $pago['id_transaccion'])->first();
            $solicitud = Solicitud::find($pago['id_transaccion']);
            $saldo_documento_format = 0;
            $referencia_documento = null;
            if ($factura == null && $solicitud != null) // Solicitud
            {
                $transaccion = $solicitud;
                $pago_a_generar = $documento ? $this->datosPago($documento->IDTipoDocumento) : '';
                $saldo_documento_format = $solicitud->monto_format;
                $saldo_documento = $solicitud->monto;
                $referencia_documento = 'S/P '.$solicitud->numero_folio_format;
            }

            if ($solicitud == null && $factura != null) // Factura
            {
                $transaccion = $factura;
                $pago_a_generar = $documento ? $this->datosPago($documento->IDTipoDocumento) : '';
                $saldo_documento_format = $factura->saldo_format;
                $saldo_documento = $factura->saldo;
                $referencia_documento = $factura->referencia;
            }

            if ($cta_cargo == null) {
                $tipo_cuenta_moneda =  $this->validarTipoCambio($pago['tipo_cambio'], $transaccion ? $transaccion->moneda->id_moneda : '', '');
                $ctas_cargo = Obra::find(Context::getIdObra())->cuentasPagadorasObra;
                $cuenta_encontrada = false;
                foreach ($ctas_cargo as $cta_cargo) {
                    $cuentas[] = array('id' => $cta_cargo->id_cuenta,
                        'numero' => $cta_cargo->numero,
                        'abreviatura' => $cta_cargo->abreviatura,
                        'nombre' => $cta_cargo->empresa->razon_social,
                        'id_empresa' => $cta_cargo->empresa->id_empresa,
                        'tipo_cuenta' => $cta_cargo->id_moneda
                    );
                }
                if(sizeof($ctas_cargo) == 1){
                    $id_cuenta = $ctas_cargo[0]->id_cuenta;
                }
            } else {
                $tipo_cuenta_moneda =  $this->validarTipoCambio($pago['tipo_cambio'],$transaccion != null ? $transaccion->moneda->id_moneda : '', $cta_cargo->id_moneda);
                $id_cuenta = $cta_cargo->id_cuenta;
                $cuentas = array('id' => $cta_cargo->id_cuenta,
                    'numero' => $cta_cargo->numero,
                    'abreviatura' => $cta_cargo->abreviatura,
                    'nombre' => $cta_cargo->empresa->razon_social,
                    'id_empresa' => $cta_cargo->empresa->id_empresa,
                    'tipo_cuenta' => $cta_cargo->id_moneda
                );
            }

            $registros[] = array(
                'datos_completos_correctos' => $pago['estado'],
                'id_documento' => $documento ? $documento->IDDocumento : null,
                'id_transaccion' => $transaccion ? $transaccion->id_transaccion : null,
                'folio_transaccion' => $transaccion ? $transaccion->numero_folio : null,
                'fecha_documento' => $pago['fecha_factura'],
                'referencia_documento' => $referencia_documento,
                'monto_factura' => $transaccion ? (float)$transaccion->monto : null,
                'monto_documento_format' => $transaccion ? $transaccion->monto_format : null,
                'saldo_documento_format' => $saldo_documento_format,
                'saldo_documento' => $saldo_documento,
                'moneda_documento' => $transaccion ? $transaccion->moneda ? $transaccion->moneda->nombre : null : null,
                'id_moneda' => $transaccion ? $transaccion->id_moneda : null,
                'cuenta_encontrada' => $cuenta_encontrada,
                'id_cuenta_cargo' => $id_cuenta,
                'cuenta_cargo' => $cuentas,
                'fecha_pago' => $this->validarFecha($pago['fecha_pago']) == true ? $pago['fecha_pago'] : '',
                'referencia_pago' => $pago['referencia_pago'],
                'tipo_cambio' => $pago['tipo_cambio'],
                'bandera_TC' => $tipo_cuenta_moneda,
                'monto_pagado' => $pago['monto_pagado'],
                'validar_monto' => $this->validarMontos($transaccion ? $transaccion->monto : 0, $pago['monto_pagado']),
                'id_transaccion_tipo' => $documento ? $documento->tipoDocumento->TipoDocumento : null,
                'pago_a_generar' => $pago_a_generar ? $pago_a_generar['pago_a_generar'] : "",
                'aplicacion_manual' => $pago_a_generar ? $pago_a_generar['aplicacion_manual'] : "",
                'estado' => $transaccion ? $this->validarEstatusPago($transaccion, $documento) : ['id' => 0, 'estado' => 0, 'descripcion' => 'No encontrada'],
                'beneficiario' => $transaccion ? $transaccion->tipo_transaccion == 72 && $transaccion->opciones == 1 ? $transaccion->fondo->nombre : $transaccion->empresa->razon_social  : null,
                'referencia_docto' => $documento ? $documento->Referencia : null,
                'origen_docto' => $documento ? $documento->origenDocumento->OrigenDocumento : null,
                'fecha_limite' =>  date('d-m-Y')
            );
        }

        return array(
            'data' => $registros,
            'resumen' => $this->resumenLayout($registros)
        );
    }

    public function getCSVData($file)
    {
        try{
            $myfile = fopen($file, "r") or die("Unable to open file!");
            $content = array();
            $titulos = 0;
            while(!feof($myfile)) {
                $linea = explode(",",fgets($myfile));

                if($titulos > 0) {

                    if (count($linea) > 1 && count($linea) <= 11) {
                        $content[] = array(
                            "id_transaccion" => (int)str_replace(" ", "", $linea[0]),
                            "fecha_factura" => $linea[1],
                            "referencia_factura" => $linea[2],
                            "monto_factura" => $linea[4],
                            "moneda_factura" => $linea[5],
                            "cuenta_cargo" => $linea[6],
                            "fecha_pago" => $linea[7],
                            "referencia_pago" => $linea[8],
                            "tipo_cambio" => $linea[9],
                            "monto_pagado" => $this->getAmount(str_replace("\r\n", "", $linea[10])),
                            "estado" => 1
                        );
                    }else{
                        $content[] = array(
                            "id_transaccion" => (int)str_replace(" ", "", $linea[0]),
                            "fecha_factura" => $linea[1],
                            "referencia_factura" => $linea[2],
                            "monto_factura" => $linea[4],
                            "moneda_factura" => $linea[5],
                            "cuenta_cargo" => $linea[6],
                            "fecha_pago" => $linea[7],
                            "referencia_pago" => $linea[8],
                            "tipo_cambio" => $linea[9],
                            "monto_pagado" => $this->getAmount(str_replace("\r\n", "", $linea[10])),
                            "estado" => 0
                        );
                    }
                }
                $titulos++;
            }
            fclose($myfile);
            return $content;
        }catch (\Exception $e){
            throw New \Exception('Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    public function getAmount($money)
    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
        /*$onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);*/

        return (float) str_replace(',', '.', $cleanString);
    }

    public function resumenLayout($data)
    {
        $pagables = 0;
        $monto_pagar = 0;

        foreach ($data as $dato){
            $dato['monto_pagado'];
            if( $dato['datos_completos_correctos'] == 1) {
                if ($dato['estado']['descripcion'] == 'Pagable' || $dato['estado']['descripcion'] == 'Pagable N/A') {
                    $pagables++;
                    $monto_pagar += $dato['monto_pagado'];
                }
            }
        }
        return array(
            'pagables' => $pagables,
            'monto_a_pagar' => $monto_pagar,
            'nombre' => explode('.',"AAAAAA")[0]
        );
    }

    public function datosPago($data)
    {
        $aplicacion_manual = false;
        switch ((int)$data){
            case 9:
            case 11:
                $pago_a_generar = 'Pago';
                break;
            case 12:
                $pago_a_generar = 'Pago Varios';
                break;
            case 15:
                $pago_a_generar = 'Pago a Cuenta';
                break;
            default:
                $aplicacion_manual = true;
                $pago_a_generar = 'Pago a Cuenta (Requiere Aplicación Manual)';
                break;
        }
        return array(
            'pago_a_generar' => $pago_a_generar,
            'aplicacion_manual' => $aplicacion_manual
        );
    }

    public function validarEstatusPago($transaccion, $documento)
    {
        $registrado = $this->validarRegistroPrevio($transaccion->id_transaccion);
        if($registrado == null) {
                if ($transaccion->tipo_transaccion == 65) {
                    if($transaccion->saldo==0 || $transaccion->estado == 2){
                        return array(
                            'id' => 0, 'estado' => 2, 'descripcion' => 'Pagada'
                        );
                    }elseif($transaccion->saldo>0 && $transaccion->estado == 1){
                        if ($documento != null){
                            return array(
                                'id' => 0, 'estado' => 1, 'descripcion' => 'Pagable'
                            );
                        }else{
                            return array(
                                'id' => 0, 'estado' => 1, 'descripcion' => 'Pagable N/A'
                            );
                        }
                    }
                }
                if ($transaccion->tipo_transaccion == 72) {
                    if ($transaccion->estado == 2) {
                        return array(
                            'id' => 0, 'estado' => 2, 'descripcion' => 'Pagada'
                        );
                    }
                }

                return array(
                    'id' => 0, 'estado' => 1, 'descripcion' => 'Pagable'
                );
        }
        return $registrado;
    }

    public function validarMontos($monto_factura, $monto_pago)
    {
        if($monto_pago == 0.0 || ($monto_factura+1 <= $monto_pago || $monto_factura-1 >= $monto_pago))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function validarRegistroPrevio($transaccion)
    {
        $partida = LayoutPagoPartida::query()->where('id_transaccion', $transaccion)->first();
        if($partida != null)
        {
            if($partida->layoutPago()->where('estado', '!=', '-1')->first() != null) {
                return array(
                    'id' => 0, 'estado' => -1, 'descripcion' => 'Registrado Previamente'
                );
            }
        }
    }

    public function create(array $data)
    {
        return $this->model->registar($data);
    }

    public  function validarFecha($fecha)
    {

        $fecha_correcta = DateTime::createFromFormat('d/m/Y', $fecha);
        if($fecha_correcta != false)
        {
            if(strcmp($fecha_correcta->format('d/m/Y'), $fecha) == 0 && date('d/m/Y') >=  $fecha) {
                return true;
            }
        }

        $fecha_correcta =DateTime::createFromFormat('d-m-Y', $fecha);
        if($fecha_correcta != false)
        {
            if(strcmp($fecha_correcta->format('d-m-Y'), $fecha) == 0 && date('d-m-Y') >=  $fecha) {
                return true;
            }
        }

        $fecha_correcta =DateTime::createFromFormat('Y-m-d', $fecha);
        if($fecha_correcta != false)
        {
            if(strcmp($fecha_correcta->format('Y-m-d'), $fecha) == 0 && date('Y-m-d') >=  $fecha) {
                return true;
            }
        }

        $fecha_correcta = DateTime::createFromFormat('Y/m/d', $fecha);
        if($fecha_correcta != false)
        {
            if(strcmp($fecha_correcta->format('Y/m/d'), $fecha) == 0 && date('Y/m/d') >=  $fecha) {
                return true;
            }
        }

        return false;
    }

    public function validarTipoCambio($tipo_cambio, $moneda_transaccion, $moneda_cuenta)
    {
        if(($tipo_cambio == '' && $moneda_cuenta == '') || $moneda_cuenta == '')// Tipo cambio vacio y cuenta no encontrada
        {
            return 2;
        }

        if($moneda_transaccion =! "")
        {
            if (($tipo_cambio == 1 && $moneda_cuenta == 1 && $moneda_transaccion == 1) || ($tipo_cambio == 1 && $moneda_cuenta == $moneda_transaccion)) //Tipo cambio y todo en pesos
            {
                return 1;
            }
        }

        return 0;
    }
}