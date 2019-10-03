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

        foreach ($this->getCSVData($layout) as $key => $pago) {
            $documento = Documento::query()->where("IDTransaccionCDC", "=", $pago['id_transaccion'])->first();
            if($documento != null && ($pago['fecha_pago'] || $pago['referencia_pago'] || $pago['monto_pagado'] || $pago['cuenta_cargo'])) {
                $transaccion = "";
                $pago_a_generar = "";
                $cuenta_encontrada = true;
                $cuentas = [];
                $id_cuenta = "";
                $cta_cargo = Cuenta::query()->where('numero', $pago['cuenta_cargo'])->where('id_tipo_cuentas_obra', '=', 1)->first();
                $factura = Factura::query()->where('id_transaccion', '=', $pago['id_transaccion'])->first();
                $solicitud = Solicitud::query()->where('id_transaccion', '=', $pago['id_transaccion'])->first();

                if($cta_cargo == null)
                {
                    $cta_cargo = Cuenta::query()->where('id_tipo_cuentas_obra', '=', 1)->get();
                    $cuenta_encontrada = false;
                    foreach ($cta_cargo as $cargo)
                    {
                        $cuentas[] = array('id' => $cargo->id_cuenta,
                            'numero'=> $cargo->numero,
                            'abreviatura'=> $cargo->abreviatura,
                            'nombre' => $cargo->empresa->razon_social,
                            'id_empresa' => $cargo->empresa->id_empresa
                        );
                    }
                }else{
                    $id_cuenta = $cta_cargo->id_cuenta;
                    $cuentas = array('id' => $cta_cargo->id_cuenta,
                        'numero'=> $cta_cargo->numero,
                        'abreviatura'=> $cta_cargo->abreviatura,
                        'nombre' => $cta_cargo->empresa->razon_social,
                        'id_empresa' => $cta_cargo->empresa->id_empresa
                    );
                }



                if ($factura == null && $solicitud != null) // Solicitud de Pago Anticipado
                {
                    $transaccion = $solicitud;
                    $pago_a_generar = $this->datosPago($documento->IDTipoDocumento);
                }

                if ($solicitud == null && $factura != null) // Factura
                {
                    $transaccion = $factura;
                    $pago_a_generar = $this->datosPago($documento->IDTipoDocumento);
                }

                $registros[] = array(
                    'id_documento' => $documento->IDDocumento,
                    'id_transaccion' => $transaccion ? $transaccion->id_transaccion : null,
                    'folio_transaccion' => $transaccion ? $transaccion->numero_folio : null,
                    'fecha_factura' => $pago['fecha_factura'],
                    'referencia_factura' => $transaccion ? $transaccion->referencia : null,
                    'monto_factura' => $transaccion ? (float) $transaccion->monto : null,
                    'moneda_factura' =>  $transaccion ? $transaccion->moneda ? $transaccion->moneda->nombre : null : null,
                    'id_moneda' =>  $transaccion ? $transaccion->id_moneda : null,
                    'cuenta_encontrada' => $cuenta_encontrada,
                    'id_cuenta_cargo' => $id_cuenta,
                    'cuenta_cargo' =>  $cuentas,
                    'fecha_pago' => $pago['fecha_pago'],
                    'referencia_pago' => $pago['referencia_pago'],
                    'tipo_cambio' => $pago['tipo_cambio'],
                    'monto_pagado' => $pago['monto_pagado'],
                    'validar_monto' => $this->validarMontos($transaccion ? $transaccion->monto : 0, $pago['monto_pagado']),
                    'id_transaccion_tipo' => $transaccion ? $documento->tipoDocumento->TipoDocumento : null,
                    'pago_a_generar' => $pago_a_generar ? $pago_a_generar['pago_a_generar'] : "",
                    'aplicacion_manual' => $pago_a_generar ? $pago_a_generar['aplicacion_manual'] : "",
                    'estado' => $transaccion ? $this->validarEstatusPago($transaccion) : ['id' => 0, 'estado' =>0, 'descripcion' => 'No encontrada'],
                    'beneficiario' => $transaccion ? $documento->Destinatario : null,
                    'referencia_docto' => $transaccion ? $documento->Referencia :  null,
                    'origen_docto' => $transaccion ? $documento->origenDocumento->OrigenDocumento : null,
                );
            }
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
                    if (count($linea) > 1) {
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
                            "monto_pagado" => $this->getAmount(str_replace("\r\n", "", $linea[10]))
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
            if($dato['estado']['descripcion'] == 'Pagable'){
                $pagables++;
                $monto_pagar += $dato['monto_pagado'];
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

    public function validarEstatusPago($transaccion)
    {
        $registrado = $this->validarRegistroPrevio($transaccion->id_transaccion);
        if($registrado == null) {
            if ($transaccion->tipo_transaccion == 65) {
                $transaccion_antecedente = OrdenPago::query()->withoutGlobalScopes()
                    ->where('id_referente', '=', $transaccion->id_transaccion)
                    ->where('tipo_transaccion', '=', 68)
                    ->where('estado', '!=', -2)
                    ->where('id_obra', '=', Context::getIdObra())->first();

                if ($transaccion != null && $transaccion_antecedente != null) {
                    $pago = Transaccion::query()->where('numero_folio', '=', $transaccion_antecedente->numero_folio)
                        ->where('tipo_transaccion', '=', 82)
                        ->where('estado', '!=', -2)->first();

                    return array(
                        'id' => $pago->id_transaccion, 'estado' => 2, 'descripcion' => 'Pagada'
                    );
                }
            }
            if ($transaccion->tipo_transaccion == 72) {
                $pago = Transaccion::query()->where('id_antecedente', '=', $transaccion->id_transaccion)
                    ->where('tipo_transaccion', '=', 82)
                    ->where('estado', '!=', -2)->first();

                if ($pago != null) {
                    return array(
                        'id' => $pago->id_transaccion, 'estado' => 2, 'descripcion' => 'Pagada'
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
        if($monto_pago == 0.0 || ($monto_factura+1 <= $monto_pago || $monto_factura-1 <= $monto_pago))
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
        if(LayoutPagoPartida::query()->where('id_transaccion', $transaccion)->first() != null)
        {
            return array(
                'id' => 0, 'estado' => -1, 'descripcion' => 'Registrado Previamente'
            );
        }
    }

    public function create(array $data)
    {
        return $this->model->registar($data);
    }

}