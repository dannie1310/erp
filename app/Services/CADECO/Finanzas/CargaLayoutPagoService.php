<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Finanzas\LayoutPago;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Repositories\Repository;

class CargaLayoutPagoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(LayoutPago $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function validarLayout($layout)
    {
        $file_fingerprint = hash_file('md5', $layout);
        /*if(BitacoraSantander::query()->where('hash_file_bitacora', '=', $file_fingerprint)->first()){
            abort(403, 'Archivo de bitácora procesado previamente.');
        }*/

        $registros= array();
        $doctos_repetidos = [];

        foreach ($this->getCSVData($layout) as $key => $pago) {
            $documento = Documento::query()->where('IDTransaccionCDC', '=', $pago['id_transaccion'])->first();
            if($documento != null) {
                $transaccion = "";
                $pago_a_generar = "";
                $aplicacion_manual = "";
                $cta_cargo = Cuenta::query()->where('numero', $pago['cuenta_cargo'])->where('id_tipo_cuentas_obra', '=', 1)->first();
                $factura = Factura::query()->where('id_transaccion', '=', $pago['id_transaccion'])->first();
                $solicitud = SolicitudPagoAnticipado::query()->where('id_transaccion', '=', $pago['id_transaccion'])->first();

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
                    'referencia_factura' => $transaccion ? $transaccion->referencia : null,
                    'monto_factura' => $transaccion ? $transaccion->monto : null,
                    'moneda_factura' =>  $transaccion ? $transaccion->moneda : null,
                    'cuenta_cargo' =>  $pago['cuenta_cargo'],
                    'fecha_pago' => $pago['fecha_pago'],
                    'referencia_pago' => $pago['referencia_pago'],
                    'tipo_cambio' => $pago['tipo_cambio'],
                    'monto_pagado' => $pago['monto_pagado'],
                    'id_transaccion_tipo' => $documento->tipoDocumento->TipoDocumento,
                    'pago_a_generar' => $pago_a_generar ? $pago_a_generar['pago_a_generar'] : "",
                    'aplicacion_manual' => $pago_a_generar ? $pago_a_generar['aplicacion_manual'] : "",
                    'estado' => '',
                    'beneficiario' => $documento->Destinatario,
                    'referencia_docto' => $documento->Referencia,
                    'origen_docto' => $documento->origenDocumento->OrigenDocumento,
                );
            }
        }

        return array(
            'data' => $registros,
            'resumen' => $this->resumenLayout($registros)
        );
    }

    public function getCSVData($file){
        try{
            $myfile = fopen($file, "r") or die("Unable to open file!");
            $content = array();
            while(!feof($myfile)) {
                $linea = explode(",",fgets($myfile));
                if(count($linea) > 1) {
                    $content[] = array(
                        "id_transaccion" => $linea[0],
                        "referencia_factura" => $linea[1],
                        "monto_factura" => $linea[2],
                        "moneda_factura" =>  $linea[3],
                        "cuenta_cargo" =>  $linea[4],
                        "fecha_pago" => $linea[5],
                        "referencia_pago" => $linea[6],
                        "tipo_cambio" => $linea[7],
                        "monto_pagado" => str_replace("\r\n","",$linea[8])
                    );
                }
            }
            fclose($myfile);
            return $content;
        }catch (\Exception $e){
            throw New \Exception('Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    public function resumenLayout($data){
        $pagables = 0;
        $monto_pagar = 0;
        /*foreach ($data as $dato){
            if($dato['pagable']){
                $pagables++;
                $monto_pagar += $dato['monto_pagado'];
            }
        }*/
        return array(
            'pagables' => $pagables,
            'monto_a_pagar' => $monto_pagar,
            'nombre' => explode('.',"AAAAAA")[0]
        );
    }

    public function datosPago($data){
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
}