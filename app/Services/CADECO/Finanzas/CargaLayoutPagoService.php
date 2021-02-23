<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\CSV\PagoLayout;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Finanzas\LayoutPago;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\Transaccion;
use App\Repositories\CADECO\Finanzas\LayoutPago\Repository;
use App\Utils\Util;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;
use mysql_xdevapi\Exception;
use Illuminate\Support\Facades\Storage;

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

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function procesaLayoutPagos($layout_pagos){
        $arreglo_para_vista_pagos_layout = [];
        $cuentas_cargo = $this->repository->getCuentasCargo();
        $fechas_validacion = $this->getFechasValidacion();
        $this->repository->validarArchivo($layout_pagos);
        try{
            $arreglo_contenido_archivo = $this->getCSVData($layout_pagos);
            $this->validaNumeroColumnasArreglo($arreglo_contenido_archivo,12);
            $arreglo_para_vista_pagos_layout = $this->getArreglo($arreglo_contenido_archivo);
        }catch (\Exception $e){
            abort(403, $e->getMessage());
        }
        $salida = array(
            'data' => $arreglo_para_vista_pagos_layout,
            'cuentas_cargo' => $cuentas_cargo,
            'fechas_validacion' => $fechas_validacion,
            'resumen' => $this->resumenLayout($arreglo_para_vista_pagos_layout),
            'id_moneda_obra' => $this->getIdMonedaObra()
        );
        return  $salida;
    }

    private function getIdMonedaObra(){
        return  $this->repository->getIdMonedaObra();
    }

    private function getfechasValidacion(){
        $hoy_str = date('Y-m-d');
        $hoy = new DateTime();
        $hace_4Y_str = date("Y-m-d",strtotime($hoy_str."- 4 years"));
        $hace_4Y = DateTime::createFromFormat('Y-m-d', $hace_4Y_str);
        return array(
            "from"=>$hoy->format('Y-m-d H:i:s'),
            "to"=>$hace_4Y->format('Y-m-d H:i:s'),
        );
    }

    public function resumenLayout($data)
    {
        $pagables = 0;
        $monto_pagar = 0;

        foreach ($data as $dato){
            if ($dato['estado']['estado'] == 1 || $dato['estado']['estado'] == 10) {
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

    public function store(array $data)
    {
        $pagos = $data['pagos'];
        $pagos = $this->validaTC($pagos);
        $datos = [
            'pagos' => $pagos,
            'resumen' => $data['resumen'],
            'file_pagos' => $data['file_pagos'],
            'nombre_archivo' => $data['file_pagos_name']
        ];
        return $this->repository->create($datos);
    }

    private function validaTC($pagos){
        $pagos_validados = array();
        $message = "";
        foreach($pagos as $i=>$pago){
            if($pago["cuenta_cargo_obj"]["id_cuenta"]>0){
                //obtener moneda de la cuenta cargo TODO: Esto tal vez se podría hacer desde el componente vue
                /*$cuenta_cargo = $this->repository->getCuentaCargoPorID($pago["id_cuenta_cargo"]);
                $pago["id_moneda_cuenta_cargo"] = $cuenta_cargo["id_moneda"];*/
                if( $pago["id_moneda_transaccion"] != $pago["cuenta_cargo_obj"]["id_moneda"] && !($pago["tipo_cambio"]>1)){
                    $message.="El tipo de cambio de la partida: ". ($i+1). " debe ser diferente a 1"."\n";
                }elseif( $pago["id_moneda_transaccion"] == $pago["cuenta_cargo_obj"]["id_moneda"] ){
                    $pago["tipo_cambio"] =1;
                }
            }
            $pagos_validados[] = $pago;
        }
        if($message != ""){
            abort(400,$message);
        }
        return $pagos_validados;
    }

    private function validaMontoPagadoDocumento($pagos){
        $pagos_validados = array();
        $moneda_obra = $this->getIdMonedaObra();
        foreach($pagos as $i=>$pago){
            if($pago["cuenta_cargo_obj"]->id_moneda == $moneda_obra){
                $pago["monto_pagado_documento"] = number_format($pago["monto_pagado"] / $pago["tipo_cambio"],2,".","");
            }
            else{
                $pago["monto_pagado_documento"] = number_format($pago["monto_pagado"] * $pago["tipo_cambio"],2,".","");
            }

            $pagos_validados[] = $pago;
        }
        return $pagos_validados;
    }

    public function autorizar($id)
    {
        return $this->repository->show($id)->autorizar();
    }

    public function descargarLayout()
    {
        if (config('filesystems.disks.layout_pagos_descarga.root') == storage_path())
        {
            dd('No existe el directorio destino: STORAGE_LAYOUT_PAGOS. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }

        Storage::disk('layout_pagos_descarga')->delete(Storage::disk('layout_pagos_descarga')->allFiles());
        $nombre_archivo = 'LayoutRegistroPagos_' . date('dmYY_His') . '.csv';
        (new PagoLayout())->store($nombre_archivo, 'layout_pagos_descarga');
        return Storage::disk('layout_pagos_descarga')->download($nombre_archivo);
    }

    private function getArreglo($arreglo){
        $contenido = array();
        foreach ($arreglo as $i=>$partida){
            if($i > 0) {
                $contenido[] = $this->complementaPartida($partida);
            }
        }
        $contenido = $this->validaTC($contenido);
        $contenido = $this->validaMontoPagadoDocumento($contenido);
        return $contenido;
    }

    private function complementaPartida($partida){
        $transaccion_pagable = $this->repository->getTransaccionPagable($partida[0]);
        if(isset($transaccion_pagable->empresa->efo->estado_badge))
        {
            $alert = $transaccion_pagable->empresa->efo->estado_badge;
        }
        else{
            $alert = '';
        }
        $cuenta_cargo = $this->repository->getCuentaCargo($partida[7]);
        $fecha_pago = $this->validaFechaPago($partida[8]);
        $monto_pagado = $this->limpiaCadena($partida[11]);
        $datos_documento =[];
        if($transaccion_pagable){
            $datos_documento = array(
                "id_transaccion" => $transaccion_pagable->id_transaccion,
                "fecha_documento" => $transaccion_pagable->fecha_format,
                "vencimiento_documento" => $transaccion_pagable->vencimiento_format,
                "beneficiario" => $transaccion_pagable->beneficiario,
                "referencia_documento" => $transaccion_pagable->referencia_pagable,
                "monto_documento" => $transaccion_pagable->monto,
                "monto_documento_format" => $transaccion_pagable->monto_format,
                "saldo_documento" => $transaccion_pagable->saldo_pagable,
                "saldo_documento_format" => $transaccion_pagable->saldo_pagable_format,
                "moneda_documento" => $transaccion_pagable->moneda->nombre,
                "id_moneda_transaccion" => $transaccion_pagable->moneda->id_moneda,
                'id_documento_remesa' => $transaccion_pagable->id_documento_remesa,
                'monto_autorizado_remesa' => $transaccion_pagable->monto_autorizado_remesa,
                'alert_icon' => $alert
            );
        }
        $datos_pago = array(
            "cuenta_cargo_obj" => $cuenta_cargo, # IMPORTA
            /*"cuenta_cargo" => $partida[6], # IMPORTA*/
            "fecha_pago" => $fecha_pago["fecha"], # IMPORTA
            "fecha_pago_s" => $fecha_pago["fecha_hora"], # IMPORTA
            "referencia_pago" => $partida[9], # IMPORTA
            "monto_pagado" => $monto_pagado, # IMPORTA

            "estado" => $this->getEstadoDocumento($transaccion_pagable, $monto_pagado),
            "id_cuenta_cargo" => $cuenta_cargo["id_cuenta"],
            /*"id_moneda_cuenta_cargo" =>  $cuenta_cargo["id_moneda"],*/
            "tipo_cambio" => $partida[10], # IMPORTA

        );
        $partida_completa = array_merge($datos_pago,$datos_documento);

        return $partida_completa;
    }
    private function getEstadoDocumento($transaccion, $monto_pagado){
        if($transaccion){
            if($transaccion->saldo_pagable > 1){
                if($transaccion->monto_autorizado_remesa>=$monto_pagado){
                    $estado_array =  array(
                        'estado' => 1, 'descripcion' => 'Pagable', 'clase_badge' => 'badge badge-success'
                    );
                } else {
                    $estado_array =  array(
                        'estado' => 10, 'descripcion' => 'Pagable N/A', 'clase_badge' => 'badge badge-success'
                    );
                }
            } else{
                $estado_array =  array(
                    'estado' => 2, 'descripcion' => 'Pagada', 'clase_badge' => 'badge badge-warning'
                );
            }
        }else{
            $estado_array =  array(
                'estado' => 0, 'descripcion' => 'No Encontrada', 'clase_badge' => 'badge badge-danger'
            );

        }
        return $estado_array;
    }

    private function getCSVData($file)
    {
        try{
            $archivo_layout = fopen($file, "r") or die("No es posible abrir el archivo");
            $contenido = array();
            while(!feof($archivo_layout)) {
                $fg = fgets($archivo_layout);
                $contenido[] = explode(",",$fg);
            }
            fclose($archivo_layout);
            return $contenido;
        }catch (\Exception $e){
            throw New \Exception("Error al leer el archivo: " . $e->getMessage());
        }
    }

    private function validaNumeroColumnasArreglo($arreglo,$no_columnas){
        $mensaje_abort = "";
        $i_errores = 1;
        $i = 0;
        foreach ($arreglo as $partida_arreglo){
            if(count($partida_arreglo)!=$no_columnas){
                $mensaje_abort.="\n(".$i_errores.") Error en estructura de la línea ". ($i+1)."";
                $i_errores++;
            }
            $i++;
        }
        if($mensaje_abort != ""){
            abort(403, $mensaje_abort."\n\nVerifique que no haya saltos de línea y que no existan (,) adicionales");
        }
    }

    private function limpiaCadena($string)    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $string);
        $cleanString = str_replace("\r\n", "", $cleanString);
        return (float) str_replace(',', '.', $cleanString);
    }

    private function validaFechaPago($fecha_pago){
        $fecha_pago = DateTime::createFromFormat('d/m/Y', $fecha_pago);
        if($fecha_pago)
        {
            $hoy_str = date('Y-m-d');
            $hoy = new DateTime();
            $hace_2Y_str = date("Y-m-d",strtotime($hoy_str."- 2 years"));
            $hace_2Y = DateTime::createFromFormat('Y-m-d', $hace_2Y_str);
            if($fecha_pago>$hoy || $fecha_pago<$hace_2Y){
                $fecha_pago = $hoy;
            }
            $fechas = array(
                "fecha_hora"=> $fecha_pago->format('Y-m-d H:i:s'),
                "fecha"=>$fecha_pago->format('Y-m-d')
            );
        } else {
            $fechas = array(
                "fecha_hora"=> "",
                "fecha"=>""
            );
        }

        return $fechas;
    }
}
