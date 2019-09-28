<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Services\CADECO\Finanzas;


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
            //dd($documento);
            if($documento != null) {
                $factura = Factura::query()->where('id_transaccion', '=', $pago['id_transaccion'])->first();
                $solicitud = SolicitudPagoAnticipado::query()->where('id_transaccion', '=', $pago['id_transaccion'])->first();

                if ($factura == null && $solicitud != null) // Solicitud de Pago Anticipado
                {

                }

                if ($solicitud == null && $factura != null) // Factura
                {

                }
                $registros[] = $this->datosPago($documento, $pago);
            }
//             dd($factura, $solicitud);
  //         $registros =  $pago;
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
                        "tipo_campo" => $linea[7],
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

    public function datosPago($data, $pago){
        $aplicacion_manual = false;
        switch ((int)$data->IDTipoDocumento){
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
            'id_documento' => $data->IDDocumento,
            'id_transaccion' => $data->transaccion? $data->transaccion->id_transaccion:null,
            'id_transaccion_tipo' => $data->tipoDocumento->TipoDocumento,
            'pago_a_generar' => $pago_a_generar,
            'aplicacion_manual' => $aplicacion_manual,
            'estado' => '',
            'beneficiario' => $data->Destinatario,
            'monto' => $pago['monto_pagado'],
            'cuenta_cargo' => ['id_cuenta_cargo' => '21', 'numero'=> '22', 'abreviatura'=> '22q', 'nombre' => 'CuentaBeEm', 'id_empresa' => '3sdsf'],
            'referencia' => $pago['referencia_pago'],
            'referencia_docto' => $data->Referencia,
            'origen_docto' => $data->origenDocumento->OrigenDocumento,
        );
    }
}