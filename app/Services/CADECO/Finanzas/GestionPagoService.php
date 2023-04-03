<?php


namespace App\Services\CADECO\Finanzas;


use DateTime;
use stdClass;
use App\Facades\Context;
use Zend\Validator\Date;
use App\Models\CADECO\Pago;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\Solicitud;
use App\Repositories\Repository;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\Transaccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MODULOSSAO\BaseDatosObra;
use App\Models\MODULOSSAO\UnificacionObra;
use App\Models\CADECO\PagoACuentaPorAplicar;
use App\Models\MODULOSSAO\ControlRemesas\Remesa;
use App\Models\CADECO\Finanzas\BitacoraSantander;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\MODULOSSAO\ControlRemesas\DocumentoProcesado;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;

class GestionPagoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DistribucionRecursoRemesaService constructor.
     * @param Repository $repository
     */
    public function __construct(DistribucionRecursoRemesa $model)
    {
        $this->repository = new Repository($model);
    }

    public function bitacoraPago($data, $pago, $transacciones = null){
        $aplicacion_manual = false;
        $tipo = 'S';
        switch ((int)$data->IDTipoDocumento){
            case 9:
            case 10:
            case 11:
                $pago_a_generar = 'Pago';
                $tipo = 'F';
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
            'id_distribucion_recurso' => $data->partidaVigente->id_distribucion_recurso,
            'id_transaccion' => $data->transaccion? $data->transaccion->id_transaccion:null,
            'id_transaccion_tipo' => $data->tipoDocumento->TipoDocumento,
            'pago_a_generar' => $pago_a_generar,
            'aplicacion_manual' => $aplicacion_manual,
            'estado' => $data->partidaVigente->estatus,
            'pagable' => $data->partidaVigente->pagable,
            'concepto' => $data->Concepto?utf8_encode($data->Concepto):'',
            'beneficiario' => $data->Destinatario,
            'monto_format' => '$ ' . number_format($pago['monto'], 2),
            'monto' => $pago['monto'],
            'cuenta_cargo' => ['id_cuenta_cargo' => $data->partidaVigente->cuentaCargo->id_cuenta,
                                'numero'=> $data->partidaVigente->cuentaCargo->numero,
                                'abreviatura'=> $data->partidaVigente->cuentaCargo->abreviatura,
                                'nombre' => $data->partidaVigente->cuentaCargo->empresa->razon_social,
                                'id_empresa' => $data->partidaVigente->cuentaCargo->empresa->id_empresa,
                                'id_moneda' => $data->partidaVigente->cuentaCargo->id_moneda],
            'cuenta_abono' => ['id_cuenta_abono' => $data->partidaVigente->cuentaAbono->id,
                                'numero'=> $data->partidaVigente->cuentaAbono->cuenta_clabe,
                                'abreviatura'=> $data->partidaVigente->cuentaAbono->banco->ctg_banco->nombre_corto,
                                'nombre' => $data->partidaVigente->cuentaAbono->empresa->razon_social,
                                'id_empresa' => $data->partidaVigente->cuentaAbono->empresa->id_empresa],
            'referencia' => $pago['referencia'],
            'referencia_docto' => $data->transaccion? $data->transaccion->referencia:$data->Referencia,
            'folio' => $data->transaccion?$tipo . $data->transaccion->numero_folio_format:$data->Referencia,
            'saldo' => $data->transaccion?$data->transaccion->saldo_format:'N/D',
            'origen_docto' => $data->origenDocumento->OrigenDocumento,
            'fecha_pago' => $pago['fecha'],
            'select_transacciones' => $transacciones
        );
    }

    public function getAmount($money)
    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

        return (float) str_replace(',', '.', $removedThousendSeparator);
    }

    public function getTxtData($file){
        try{
            $myfile = fopen($file, "r") or die("Unable to open file!");
            $content = array();
            while(!feof($myfile)) {
                $linea = explode(";",fgets($myfile));
                if(count($linea) > 1 && $linea[8] == 'Aceptado' && $linea[4] > 1) {
                    $content[] = array(
                        "fecha" => $linea[0],
                        "hora" => $linea[1],
                        "concepto" => str_replace('  ', '', $linea[2]),
                        "cuenta_cargo" =>  str_replace(' ', '', $linea[3]),
                        "cuenta_abono" =>  str_replace(' ', '', $linea[4]),
                        "monto" => $this->getAmount($linea[5]),
                        "referencia" => $linea[6],
                        "usuario" => $linea[7],
                        "estatus" => $linea[8],
                        "origen" => $linea[9]
                    );
                }
            }
            fclose($myfile);
            return $content;
        }catch (\Exception $e){
            throw New \Exception('Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    public function guardar_bitacora($bitacora){
        if (config('filesystems.disks.portal_carga.root') == storage_path())
        {
            abort(403, 'No existe el directorio destino: SANTANDER_PORTAL_STORAGE_CARGA. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }
        $nombre = hash_file('md5', $bitacora);
        $file_bitacora = fopen($bitacora, "r") or die("Unable to open file!");
        Storage::disk('portal_carga')->put($nombre . '.txt', $file_bitacora);
    }

    public function registrarPagos_bis($pagos){
        try {
            DB::connection('cadeco')->beginTransaction();
            // TODO registro de bitacora santander en sistema
            $file_fingerprint = hash_file('md5', $pagos->file_interbancario);
            if(BitacoraSantander::query()->where('hash_file_bitacora', '=', $file_fingerprint)->first()){
                abort(403, 'Archivo de bitácora procesado previamente');
            }

            $archivo_bitacora = BitacoraSantander::query()->create(['nombre_bitacora' => $pagos->resumen['nombre_bitacora'] ,
                'monto_bitacora' => $pagos->resumen['monto_a_pagar'],
                'hash_file_bitacora' => $file_fingerprint]);
            $partida_remesa = null;

            foreach ($pagos->bitacora as $pago) {
                if($pago['pagable']) {

                    if ($pago['id_documento'] && $pago['id_distribucion_recurso']) {
//                        $dist_remesa = DistribucionRecursoRemesa::find($pago['id_distribucion_recurso'])->first();
//                        DistribucionRecursoRemesa::find($pago['id_distribucion_recurso'])->remesaValidaEstado();

                        $partida_remesa = DistribucionRecursoRemesaPartida::where('id_distribucion_recurso', '=', $pago['id_distribucion_recurso'])->where('id_documento', '=', $pago['id_documento'])->first();
                        if($partida_remesa->pagable) {
                            $fecha = DateTime::createFromFormat('d/m/Y', $pago['fecha_pago']);
                            if ($partida_remesa->documento->transaccion) {
                                $transaccion = $partida_remesa->documento->transaccion;
                                $pago_remesa = null;
                                $data = [
                                    'monto_pagado_transaccion' => $partida_remesa->documento->getImporteTotalProcesadoAttribute(),
                                    'id_cuenta_cargo' => $partida_remesa->cuentaCargo->id_cuenta,
                                    'fecha_pago' => $fecha->format('Y-m-d'),
                                    'monto_pagado' => $partida_remesa->documento->getImporteTotalProcesadoAttribute(),
                                    'referencia_pago' => $pago['referencia'],
                                    'id_moneda_cuenta_cargo' => $partida_remesa->cuentaCargo->id_moneda,
                                    'tipo_cambio' => 1

                                ];
                                switch ($partida_remesa->documento->transaccion->tipo_transaccion) {
                                    case 65:
                                        $pago_remesa = Factura::find($transaccion->id_transaccion)->generaOrdenPago($data);
                                        break;
                                    case 72:
                                        $pago_remesa = Solicitud::find($transaccion->id_transaccion)->generaPago($data);
                                        if(!$pago_remesa){
                                            abort(400, "Hubo un error al generar el pago con referencia: ". $pago['referencia'] . " tipo de solicitud no soportado");
                                        }
                                        break;
                                }
                            }
                            else {
                                $data = array(
                                    "id_cuenta" => $partida_remesa->id_cuenta_cargo,
                                    "id_empresa" => $partida_remesa->documento->IDDestinatario,
                                    "id_moneda" => $partida_remesa->documento->IDMoneda,
                                    "fecha" => $fecha->format('Y-m-d'),
                                    "cumplimiento" => $fecha->format('Y-m-d'),
                                    "vencimiento" => $fecha->format('Y-m-d'),
                                    "monto" => -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute()),
                                    "saldo" => -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute()),
                                    "referencia" => $pago['referencia'],
                                    "destino" => $partida_remesa->documento->Destinatario,
                                    "observaciones" => utf8_encode($partida_remesa->documento->Observaciones),
                                    "tipo_cambio" => $partida_remesa->documento->moneda->tipo_cambio
                                );
                                $pago_remesa = PagoACuentaPorAplicar::query()->create($data);
                            }
                            $distribucion = DistribucionRecursoRemesa::query()->find($pago['id_distribucion_recurso']);
                            $distribucion->estado = 3;
                            $distribucion->save();

                            $partida_remesa->estado = 2;
                            $partida_remesa->id_transaccion_pago = $pago_remesa->id_transaccion;
                            $partida_remesa->folio_partida_bancaria = $pago['referencia'];
                            $partida_remesa->save();

                            $distribucion_layout = DistribucionRecursoRemesaLayout::query()->where('id_distribucion_recurso', '=', $pago['id_distribucion_recurso'])->first();
                            $distribucion_layout->usuario_carga = auth()->id();
                            $distribucion_layout->fecha_hora_carga = date('Y-m-d');
                            $distribucion_layout->folio_confirmacion_bancaria = date('Y-m-d');
                            $distribucion_layout->save();
                        }
                    } else {
                        $empresa = Empresa::query()->create(
                            ['tipo_empresa' => 64,
                                'razon_social' => $pago['cuenta_abono']['numero'],
                                'rfc' => '']
                        );

//                        $cuenta = CuentaBancariaEmpresa::query()->create([
//                            'id_empresa' => $empresa->id_empresa,
//                            'id_banco' => $pago['cuenta_cargo']['id_empresa'],
//                            'cuenta_clabe' => $pago['cuenta_abono']['numero'],
//                            'sucursal' => '',
//                            'tipo' => '',
//                            'plaza' => '',
//                            'id_moneda' => 1
//                        ]);
                        $fecha = DateTime::createFromFormat('d/m/Y', $pago['fecha_pago']);
                        $data = array(
                            "id_cuenta" => $pago['cuenta_cargo']['id_cuenta_cargo'],
                            "id_empresa" => $empresa->id_empresa,
                            "id_moneda" => 1,
                            "fecha" => $fecha->format('Y-m-d'),
                            "cumplimiento" => $fecha->format('Y-m-d'),
                            "vencimiento" => $fecha->format('Y-m-d'),
                            "monto" => -1 * abs($pago['monto']),
                            "saldo" => -1 * abs($pago['monto']),
                            "referencia" => $pago['referencia'],
                            "destino" => $empresa->razon_social,
                            "observaciones" => $pago['concepto'],
                            "tipo_cambio" => 1
                        );

                        $pago_remesa = PagoACuentaPorAplicar::query()->create($data);
                    }
                    $archivo_bitacora->partidas()->create([
                        'id_distribucion_recursos_rem_partida' => $partida_remesa?$partida_remesa->id:$partida_remesa,
                        'id_transaccion_pago' => $pago_remesa->id_transaccion,
                        'monto_pagado' => $pago['monto'],
                        'referencia_pago' => $pago['referencia'],
                        'cuenta_abono' => $pago['cuenta_abono']['numero'],
                        'id_cuenta_abono' => $pago['cuenta_abono']['id_cuenta_abono']?$pago['cuenta_abono']['id_cuenta_abono']:null,
                        'cuenta_cargo' => $pago['cuenta_cargo']['numero'],
                        'id_cuenta_cargo' => $pago['cuenta_cargo']['id_cuenta_cargo']
                    ]);
                }
            }
            $archivo_bitacora->estado = 1;
            $archivo_bitacora->save();
            $this->guardar_bitacora($pagos->file_interbancario);
            DB::connection('cadeco')->commit();
            return $pagos;

        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $pago;
        }
    }
    public function registrarPagos($pagos){
        try {
            DB::connection('cadeco')->beginTransaction();
            // TODO registro de bitacora santander en sistema
            $file_fingerprint = hash_file('md5', $pagos->file_interbancario);
            if(BitacoraSantander::query()->where('hash_file_bitacora', '=', $file_fingerprint)->first()){
                abort(403, 'Archivo de bitácora procesado previamente');
            }

            $archivo_bitacora = BitacoraSantander::query()->create(['nombre_bitacora' => $pagos->resumen['nombre_bitacora'] ,
                'monto_bitacora' => $pagos->resumen['monto_a_pagar'],
                'hash_file_bitacora' => $file_fingerprint]);
            $partida_remesa = null;

            foreach ($pagos->bitacora as $pago) {
                if(!$pago['pagable']) {
                    continue;
                }
                $pago_remesa = null;
                $fecha = DateTime::createFromFormat('d/m/Y', $pago['fecha_pago']);
                if($pago['id_documento'] && $pago['id_distribucion_recurso']){
                    $partida_remesa = DistribucionRecursoRemesaPartida::where('id_distribucion_recurso', '=', $pago['id_distribucion_recurso'])
                                        ->where('id_documento', '=', $pago['id_documento'])->partidaPagable()->first();
                    if ($partida_remesa->documento->transaccion) {
                        $transaccion = $partida_remesa->documento->transaccion;
                        $data = [
                            'monto_pagado_transaccion' => $partida_remesa->documento->getImporteTotalProcesadoAttribute(),
                            'id_cuenta_cargo' => $partida_remesa->cuentaCargo->id_cuenta,
                            'fecha_pago' => $fecha->format('Y-m-d'),
                            'monto_pagado' => $partida_remesa->documento->getImporteTotalProcesadoAttribute(),
                            'referencia_pago' => $pago['referencia'],
                            'id_moneda_cuenta_cargo' => $partida_remesa->cuentaCargo->id_moneda,
                            'tipo_cambio' => 1
                        ];
                        switch ($partida_remesa->documento->transaccion->tipo_transaccion) {
                            case 65:
                                $pago_remesa = Factura::find($transaccion->id_transaccion)->generaOrdenPago($data);
                                break;
                            case 72:
                                $pago_remesa = Solicitud::find($transaccion->id_transaccion)->generaPago($data);
                                if(!$pago_remesa){
                                    abort(400, "Hubo un error al generar el pago con referencia: ". $pago['referencia'] . " tipo de solicitud no soportado");
                                }
                                break;
                        }
                    }else {
                        $data = array(
                            "id_cuenta" => $partida_remesa->id_cuenta_cargo,
                            "id_empresa" => $partida_remesa->documento->IDDestinatario,
                            "id_moneda" => $partida_remesa->documento->IDMoneda,
                            "fecha" => $fecha->format('Y-m-d'),
                            "cumplimiento" => $fecha->format('Y-m-d'),
                            "vencimiento" => $fecha->format('Y-m-d'),
                            "monto" => -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute()),
                            "saldo" => -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute()),
                            "referencia" => $pago['referencia'],
                            "destino" => $partida_remesa->documento->Destinatario,
                            "observaciones" => utf8_encode($partida_remesa->documento->Observaciones),
                            "tipo_cambio" => $partida_remesa->documento->moneda->tipo_cambio
                        );
                        $pago_remesa = PagoACuentaPorAplicar::query()->create($data);
                    }
                    $distribucion = DistribucionRecursoRemesa::query()->find($pago['id_distribucion_recurso']);
                    $distribucion->estado = 3;
                    $distribucion->save();

                    $partida_remesa->estado = 2;
                    $partida_remesa->id_transaccion_pago = $pago_remesa->id_transaccion;
                    $partida_remesa->folio_partida_bancaria = $pago['referencia'];
                    $partida_remesa->save();

                    $distribucion_layout = DistribucionRecursoRemesaLayout::query()->where('id_distribucion_recurso', '=', $pago['id_distribucion_recurso'])->first();
                    $distribucion_layout->usuario_carga = auth()->id();
                    $distribucion_layout->fecha_hora_carga = date('Y-m-d');
                    $distribucion_layout->folio_confirmacion_bancaria = date('Y-m-d');
                    $distribucion_layout->save();

                }

                else if($pago['id_transaccion'] != null && $pago['id_transaccion'] >0){
                    $transaccion = Transaccion::find($pago['id_transaccion']);
                    $data = [
                        'monto_pagado_transaccion' => $pago['monto'],
                        'id_cuenta_cargo' => $pago['cuenta_cargo']['id_cuenta_cargo'],
                        'fecha_pago' => $fecha->format('Y-m-d'),
                        'monto_pagado' => $pago['monto'],
                        'referencia_pago' => $pago['referencia'],
                        'id_moneda_cuenta_cargo' => $pago['cuenta_cargo']['id_moneda'],
                        'tipo_cambio' => 1
                    ];
                    switch ($transaccion->tipo_transaccion) {
                        case 65:
                            $pago_remesa = Factura::find($transaccion->id_transaccion)->generaOrdenPago($data);
                            break;
                        case 72:
                            $pago_remesa = Solicitud::find($transaccion->id_transaccion)->generaPago($data);
                            if(!$pago_remesa){
                                abort(400, "Hubo un error al generar el pago con referencia: ". $pago['referencia'] . " tipo de solicitud no soportado");
                            }
                            break;
                    }
                }

                else{
                    $data = array(
                        "id_cuenta" => $pago['cuenta_cargo']['id_cuenta_cargo'],
                        "id_empresa" => $pago['cuenta_abono']['id_empresa'],
                        "id_moneda" => $pago['cuenta_cargo']['id_moneda'],
                        "fecha" => $fecha->format('Y-m-d'),
                        "cumplimiento" => $fecha->format('Y-m-d'),
                        "vencimiento" => $fecha->format('Y-m-d'),
                        "monto" => -1 * abs($pago['monto']),
                        "saldo" => -1 * abs($pago['monto']),
                        "referencia" => $pago['referencia'],
                        "destino" => $pago['cuenta_abono']['nombre'],
                        "observaciones" => $pago['concepto'],
                        "tipo_cambio" => 1
                    );
                    $pago_remesa = PagoACuentaPorAplicar::query()->create($data);
                }

                $archivo_bitacora->partidas()->create([
                    'id_distribucion_recursos_rem_partida' => $partida_remesa?$partida_remesa->id:$partida_remesa,
                    'id_transaccion_pago' => $pago_remesa->id_transaccion,
                    'monto_pagado' => $pago['monto'],
                    'referencia_pago' => $pago['referencia'],
                    'cuenta_abono' => $pago['cuenta_abono']['numero'],
                    'id_cuenta_abono' => $pago['cuenta_abono']['id_cuenta_abono']?$pago['cuenta_abono']['id_cuenta_abono']:null,
                    'cuenta_cargo' => $pago['cuenta_cargo']['numero'],
                    'id_cuenta_cargo' => $pago['cuenta_cargo']['id_cuenta_cargo']
                ]);

            }

            $archivo_bitacora->estado = 1;
            $archivo_bitacora->save();
            $this->guardar_bitacora($pagos->file_interbancario);
            DB::connection('cadeco')->commit();
            return $pagos;

        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $pago;
        }
    }

    public function resumenBitacora($data, $nombre){
        $pagables = 0;
        $monto_pagar = 0;
        foreach ($data as $dato){
            if($dato['pagable']){
                $pagables++;
                $monto_pagar += $dato['monto'];
            }
        }
        return array(
            'pagables' => $pagables,
            'monto_a_pagar' => $monto_pagar,
            'nombre_bitacora' => explode('.',$nombre)[0]
        );
    }

    public function validarBitacora($bitacora, $bitacora_nombre, $id_dispersion){
        try{
            $file_fingerprint = hash_file('md5', $bitacora);
            if(BitacoraSantander::where('hash_file_bitacora', '=', $file_fingerprint)->first()){
                abort(403, 'Archivo de bitácora procesado previamente.');
            }

            $cod_operacion = ['FUE001', 'FUE002', 'FUE003', 'FUE542', 'FUE543', 'FUE707','FUA543'];
            $myfile = fopen($bitacora, "r") or die("Unable to open file!");
            $content = array();
            while(!feof($myfile)) {
                $content[] = fgets($myfile);
            }
            fclose($myfile);
            $data = array();
            if(count(explode(";",$content[0])) == 12){
                foreach($content as $line){
                    $linea = explode(";",$line);
                    if(count($linea) > 1 && $linea[8] == 'Aceptado' && $linea[4] > 1) {
                        $data[] = array(
                            "fecha" => $linea[0],
                            "hora" => $linea[1],
                            "concepto" => str_replace('  ', '', $linea[2]),
                            "cuenta_cargo" =>  str_replace(' ', '', $linea[3]),
                            "cuenta_abono" =>  str_replace(' ', '', $linea[4]),
                            "monto" => $this->getAmount($linea[5]),
                            "referencia" => $linea[6],
                            "usuario" => $linea[7],
                            "estatus" => $linea[8],
                            "origen" => $linea[9]
                        );
                    }
                }
                return $this->validarBitacoraV1($data, $bitacora_nombre, $id_dispersion);
            }
            if(count(explode("|",$content[0])) == 11){
                foreach($content as $line){
                    $linea = explode("|",$line);
                    if(in_array($linea[1], $cod_operacion) && ($linea[8] == 'Aceptada' || $linea[8] == 'Ejecutada')) {
                        $fecha_format = explode(" ",$linea[0])[0];
                        $fecha_format = str_replace('-', '/', $fecha_format);
                        $data[] = array(
                            "fecha" => $fecha_format,
                            "codigo" => $linea[1],
                            "evento" => $linea[2],
                            "cta_cargo" => $linea[3],
                            "cta_abono" => $linea[4],
                            "monto" =>  $this->getAmount($linea[5]),
                            "referencia" =>  $linea[6],
                            "usuario" => $linea[7],
                            "estatus" => $linea[8],
                            "descripcion" => $linea[9],
                            "ip_user" => $linea[10],
                        );
                    }
                }

                return $this->validarBitacoraV2($data, $bitacora_nombre, $id_dispersion);
            }

            return array(
                'data' => [],
                'resumen' => []
            );
        }catch (\Exception $e){
            throw New \Exception('Error al procesar el archivo: ' . $e->getMessage());
        }

    }

    public function validarBitacoraV2($bitacora, $bitacora_nombre, $id_dispersion){
        $dispersion = DistribucionRecursoRemesa::where('id', '=',$id_dispersion)->first();
        $proyectos = UnificacionObra::where('IDBaseDatos', '=',BaseDatosObra::first()->IDBaseDatos)->where('id_obra', '=', Context::getIdObra())->pluck('IDProyecto');

        $remesas = Remesa::whereIn('IDProyecto', $proyectos)->liberada()
                        ->where('Anio', '=', $dispersion->remesaLiberada->remesa->Anio)
                        ->where('NumeroSemana', $dispersion->remesaLiberada->remesa->NumeroSemana)->pluck('IDRemesa');

        $disp_remesas = $dispersion->whereIn('id_remesa', $remesas)->pluck('id');

        $dist_partidas = DistribucionRecursoRemesaPartida::transaccionPago()->partidaVigente()->partidaPagable()
                            ->whereIn('id_distribucion_recurso',$disp_remesas)->get();


        $registros_bitacora = array();
        $doctos_repetidos = [];
        foreach ($bitacora as $key => $pago){
            $transaccion_pagada = Transaccion::query()->where('referencia', '=', $pago['referencia'])->where('monto', '=', -1 * abs($pago['monto']))->first();
            if($transaccion_pagada){
                $cuenta_abono = null;
                $empresa = null;
                if($transaccion_pagada->empresa){
                    $cuenta_abono = $transaccion_pagada->empresa->cuentasBancarias[0];
                    $empresa = $transaccion_pagada->empresa;
                }else if($transaccion_pagada->fondoFijo){
                    $empresa = $transaccion_pagada->fondoFijo->empresa;
                    $cuenta_abono = $transaccion_pagada->fondoFijo->empresa->cuentasBancarias[0];
                }

                $cta_cargo = Cuenta::find($transaccion_pagada->id_cuenta);
                $registros_bitacora[] = array(
                    'id_documento' => null,
                    'id_distribucion_recurso' => null,
                    'id_transaccion' => null,
                    'id_transaccion_tipo' => '   N/A   ',
                    'pago_a_generar' => 'N/A',
                    'aplicacion_manual' => true,
                    'estado' => ['id' => 0, 'estado' => 3, 'descripcion' => 'Pagada'],
                    'pagable' => false,
                    'concepto' => utf8_encode($transaccion_pagada->observaciones),
                    'beneficiario' => $empresa->razon_social,
                    'monto_format' => '$ ' . number_format($pago['monto'], 2),
                    'monto' => $pago['monto'],
                    'cuenta_cargo' => ['id_cuenta_cargo' => $cta_cargo->id_cuenta,
                        'numero' => $cta_cargo->numero,
                        'abreviatura' => $cta_cargo->abreviatura,
                        'nombre' => $cta_cargo->empresa->razon_social,
                        'id_empresa' => $cta_cargo->empresa->id_empresa,
                        'id_moneda' => $cta_cargo->id_moneda],
                    'cuenta_abono' => [
                        'id_cuenta_abono' => $cuenta_abono?$cuenta_abono->id:null,
                        'numero' => $cuenta_abono?$cuenta_abono->cuenta_clabe:null,
                        'abreviatura' => $cuenta_abono?$cuenta_abono->cuenta_clabe:null,
                        'nombre' => $cuenta_abono?$cuenta_abono->empresa->razon_social:'',
                        'id_empresa' => $cuenta_abono?$cuenta_abono->empresa->id_empresa:''],
                    'referencia' => $pago['referencia'],
                    'referencia_docto' => '   N/A   ',
                    'folio' => 'N/P',
                    'saldo' => 'N/P',
                    'origen_docto' => '   N/A   ',
                    'fecha_pago' => $pago['fecha'],
                    'select_transacciones' => null
                );
                continue;
            }

            if($dist_partidas->count() > 0){
                $val = false;
                foreach($dist_partidas as $dist_partida){
                    $documento = $dist_partida->documento->documentoProcesado->where('IDProceso', '=', 4)->first();
                    $index = array_keys($doctos_repetidos, $documento->IDDocumento);
                    if(count($index) > 0) continue;
                    if(($documento->MontoAutorizadoPrimerEnvio + $documento->MontoAutorizadoSegundoEnvio) == $pago['monto']){
                        $doctos_repetidos[] = $documento->IDDocumento;
                        $registros_bitacora[] = $this->bitacoraPago($dist_partida->documento, $pago);
                        $val = true;
                        break;
                    }
                }
                if($val)continue;
            }

        }
        return array(
            'data' => $registros_bitacora,
            'resumen' => $this->resumenBitacora($registros_bitacora, $bitacora_nombre)
        );
    }

    public function validarBitacoraV1($bitacora, $bitacora_nombre, $id_dispersion){

        $dispersion = DistribucionRecursoRemesa::where('id', '=',$id_dispersion)->first();

        $proyectos = UnificacionObra::where('IDBaseDatos', '=',BaseDatosObra::first()->IDBaseDatos)->where('id_obra', '=', Context::getIdObra())->pluck('IDProyecto');

        $remesas = Remesa::whereIn('IDProyecto', $proyectos)->liberada()
                        ->where('Anio', '=', $dispersion->remesaLiberada->remesa->Anio)
                        ->where('NumeroSemana', $dispersion->remesaLiberada->remesa->NumeroSemana)->pluck('IDRemesa');

        $disp_remesas = $dispersion->whereIn('id_remesa', $remesas)->pluck('id');

        $registros_bitacora = array();
        $doctos_repetidos = [];
        foreach ($bitacora as $key => $pago){

            if($c_cargo = Cuenta::where('numero', $pago['cuenta_abono'])->first()){
                continue;
            }

            if(!$cta_cargo = Cuenta::where('numero', $pago['cuenta_cargo'])->pagadora()->first()) {
                continue;
            }

            if (strlen($pago['concepto']) > 10 && is_numeric(substr($pago['concepto'], 1, 9))) {
                if ($documento = Documento::where('IDDocumento', '=', substr($pago['concepto'], 1, 9))->first()) {
                    $registros_bitacora[] = $this->bitacoraPago($documento, $pago);
                    continue;
                }
            }

            $cuenta_abono = CuentaBancariaEmpresa::query()->where('cuenta_clabe', '=', $pago['cuenta_abono'])->first();
            $cuenta_abono?'':abort(403, 'El número de cuenta "' . $pago['cuenta_abono'] . '" no está registrado.' );

            $transaccion_pagada = Transaccion::query()->where('referencia', '=', $pago['referencia'])->where('monto', '=', -1 * abs($pago['monto']))->first();
            if($transaccion_pagada){
                $registros_bitacora[] = array(
                    'id_documento' => null,
                    'id_distribucion_recurso' => null,
                    'id_transaccion' => null,
                    'id_transaccion_tipo' => '   N/A   ',
                    'pago_a_generar' => 'N/A',
                    'aplicacion_manual' => true,
                    'estado' => ['id' => 0, 'estado' => 3, 'descripcion' => 'Pagada'],
                    'pagable' => false,
                    'concepto' => utf8_encode($pago['concepto']),
                    'beneficiario' => $pago['cuenta_abono'],
                    'monto_format' => '$ ' . number_format($pago['monto'], 2),
                    'monto' => $pago['monto'],
                    'cuenta_cargo' => ['id_cuenta_cargo' => $cta_cargo->id_cuenta,
                        'numero' => $cta_cargo->numero,
                        'abreviatura' => $cta_cargo->abreviatura,
                        'nombre' => $cta_cargo->empresa->razon_social,
                        'id_empresa' => $cta_cargo->empresa->id_empresa,
                        'id_moneda' => $cta_cargo->id_moneda],
                    'cuenta_abono' => [
                        'id_cuenta_abono' => $cuenta_abono?$cuenta_abono->id:null,
                        'numero' => $cuenta_abono?$cuenta_abono->cuenta_clabe:null,
                        'abreviatura' => $pago['cuenta_abono'],
                        'nombre' => $cuenta_abono?$cuenta_abono->empresa->razon_social:'',
                        'id_empresa' => $cuenta_abono?$cuenta_abono->empresa->id_empresa:''],
                    'referencia' => $pago['referencia'],
                    'referencia_docto' => '   N/A   ',
                    'folio' => 'N/P',
                    'saldo' => 'N/P',
                    'origen_docto' => '   N/A   ',
                    'fecha_pago' => $pago['fecha'],
                    'select_transacciones' => null
                );
                continue;
            }

            $dist_partidas = DistribucionRecursoRemesaPartida::transaccionPago()->partidaVigente()->partidaPagable()
                                ->where('id_cuenta_abono', '=', $cuenta_abono->id)
                                ->whereIn('id_distribucion_recurso',$disp_remesas)->get();

            if($dist_partidas->count() > 0){
                $val = false;
                foreach($dist_partidas as $dist_partida){
                    $documento = $dist_partida->documento->documentoProcesado->where('IDProceso', '=', 4)->first();
                    $index = array_keys($doctos_repetidos, $documento->IDDocumento);
                    if(count($index) > 0) continue;
                    if(($documento->MontoAutorizadoPrimerEnvio + $documento->MontoAutorizadoSegundoEnvio) == $pago['monto']){
                        $doctos_repetidos[] = $documento->IDDocumento;
                        $registros_bitacora[] = $this->bitacoraPago($dist_partida->documento, $pago);
                        $val = true;
                        break;
                    }
                }
                if($val)continue;
            }

            $documentos = DistribucionRecursoRemesaPartida::where('id_distribucion_recurso', '=', $id_dispersion)->pluck('id_documento');
            $transacciones_dispersion_partidas = Documento::whereIn('IDDocumento',$documentos)->whereNotNull('IDTransaccionCDC')->pluck('IDTransaccionCDC');

            $transacciones_empresa = Transaccion::whereIn('tipo_transaccion', [65,72])->whereNotIn('id_transaccion',$transacciones_dispersion_partidas)
                ->where('saldo', '>=', $pago['monto'])->where('saldo', '>', 0)
                ->whereRaw('(id_empresa = '. $cuenta_abono->id_empresa . ' or id_referente = ' . $cuenta_abono->id_empresa . ')')
                ->get();

            if($transacciones_empresa->count() > 0){
                $registros_bitacora[] = array(
                    'id_documento' => null,
                    'id_distribucion_recurso' => null,
                    'id_transaccion' => '',
                    'id_transaccion_tipo' => '   N/A   ',
                    'pago_a_generar' => '',
                    'aplicacion_manual' => true,
                    'estado' => ['id' => 0, 'estado' => -3, 'descripcion' => '   N/A   '],
                    'pagable' => true,
                    'concepto' => utf8_encode($pago['concepto']),
                    'beneficiario' => $pago['cuenta_abono'],
                    'monto_format' => '$ ' . number_format($pago['monto'], 2),
                    'monto' => $pago['monto'],
                    'cuenta_cargo' => ['id_cuenta_cargo' => $cta_cargo->id_cuenta,
                        'numero' => $cta_cargo->numero,
                        'abreviatura' => $cta_cargo->abreviatura,
                        'nombre' => $cta_cargo->empresa->razon_social,
                        'id_empresa' => $cta_cargo->empresa->id_empresa,
                        'id_moneda' => $cta_cargo->id_moneda],
                    'cuenta_abono' => [
                        'id_cuenta_abono' => $cuenta_abono?$cuenta_abono->id:null,
                        'numero' => $cuenta_abono?$cuenta_abono->cuenta_clabe:$pago['cuenta_abono'],
                        'abreviatura' => $pago['cuenta_abono'],
                        'nombre' => $cuenta_abono?$cuenta_abono->empresa->razon_social:'',
                        'id_empresa' => $cuenta_abono?$cuenta_abono->empresa->id_empresa:''],
                    'referencia' => $pago['referencia'],
                    'referencia_docto' => '   N/A   ',
                    'folio' => 'N/P',
                    'saldo' => 'N/P',
                    'origen_docto' => '   N/A   ',
                    'fecha_pago' => $pago['fecha'],
                    'select_transacciones' => $this->transaccion_resumen($transacciones_empresa)
                );
                continue;
            }

            $registros_bitacora[] = array(
                'id_documento' => null,
                'id_distribucion_recurso' => null,
                'id_transaccion' => null,
                'id_transaccion_tipo' => '   N/A   ',
                'pago_a_generar' => 'Pago a Cuenta (Requiere Aplicación Manual)',
                'aplicacion_manual' => true,
                'estado' => ['id' => 0, 'estado' => -3, 'descripcion' => '   N/A   '],
                'pagable' => true,
                'concepto' => utf8_encode($pago['concepto']),
                'beneficiario' => $pago['cuenta_abono'],
                'monto_format' => '$ ' . number_format($pago['monto'], 2),
                'monto' => $pago['monto'],
                'cuenta_cargo' => ['id_cuenta_cargo' => $cta_cargo->id_cuenta,
                    'numero' => $cta_cargo->numero,
                    'abreviatura' => $cta_cargo->abreviatura,
                    'nombre' => $cta_cargo->empresa->razon_social,
                    'id_empresa' => $cta_cargo->empresa->id_empresa,
                    'id_moneda' => $cta_cargo->id_moneda],
                'cuenta_abono' => [
                    'id_cuenta_abono' => $cuenta_abono?$cuenta_abono->id:null,
                    'numero' => $cuenta_abono?$cuenta_abono->cuenta_clabe:$pago['cuenta_abono'],
                    'abreviatura' => $pago['cuenta_abono'],
                    'nombre' => $cuenta_abono?$cuenta_abono->empresa->razon_social:'',
                    'id_empresa' => $cuenta_abono?$cuenta_abono->empresa->id_empresa:''
                ],
                'referencia' => $pago['referencia'],
                'referencia_docto' => '   N/A   ',
                'folio' => 'N/P',
                'saldo' => 'N/P',
                'origen_docto' => '   N/A   ',
                'fecha_pago' => $pago['fecha'],
                'select_transacciones' => null
            );
        }
        return array(
            'data' => $registros_bitacora,
            'resumen' => $this->resumenBitacora($registros_bitacora, $bitacora_nombre)
        );
    }
    public function validarBitacora_bis($bitacora, $bitacora_nombre, $id_dispersion){
        $file_fingerprint = hash_file('md5', $bitacora);
        if(BitacoraSantander::query()->where('hash_file_bitacora', '=', $file_fingerprint)->first()){
            abort(403, 'Archivo de bitácora procesado previamente.');
        }

        $registros_bitacora = array();
        $doctos_repetidos = [];
        foreach ($this->getTxtData($bitacora) as $key => $pago){
            $c_cargo = Cuenta::query()->where('numero', $pago['cuenta_abono'])->first();
            if(!$c_cargo){
                $cta_cargo = Cuenta::query()->where('numero', $pago['cuenta_cargo'])->pagadora()->first();
                if($cta_cargo) {
                    if (strlen($pago['concepto']) > 10 && is_numeric(substr($pago['concepto'], 1, 9))) {
                        $documento = Documento::where('IDDocumento', '=', substr($pago['concepto'], 1, 9))->first();
                        if ($documento) {
                            $registros_bitacora[] = $this->bitacoraPago($documento, $pago);
                        }
                    } else {
                        $cuenta_abono = CuentaBancariaEmpresa::query()->where('cuenta_clabe', '=', $pago['cuenta_abono'])->first();
                        $cuenta_abono?'':abort(403, 'El número de cuenta "' . $pago['cuenta_abono'] . '" no está registrado.' );
                        $documentos = DocumentoProcesado::procesoAutorizado()->whereRaw('(MontoAutorizadoPrimerEnvio + MontoAutorizadoSegundoEnvio) = '. $pago['monto'])->get();
                        $dist_part = DistribucionRecursoRemesaPartida::query()->transaccionPago()->partidaVigente()
                            ->where('id_cuenta_abono', '=', $cuenta_abono->id)
                            ->whereIn('id_documento', $documentos->pluck('IDDocumento'))
                            ->whereNotIn('id_documento', array_values($doctos_repetidos))->get();
                        if(count($dist_part) == 0){
                            $transaccion_pagada = Transaccion::query()->where('referencia', '=', $pago['referencia'])->where('monto', '=', -1 * abs($pago['monto']))->first();
                            if($transaccion_pagada){
                                $registros_bitacora[] = array(
                                    'id_documento' => null,
                                    'id_distribucion_recurso' => null,
                                    'id_transaccion' => null,
                                    'id_transaccion_tipo' => '   N/A   ',
                                    'pago_a_generar' => 'N/A',
                                    'aplicacion_manual' => true,
                                    'estado' => ['id' => 0, 'estado' => 3, 'descripcion' => 'Pagada'],
                                    'pagable' => false,
                                    'concepto' => $pago['concepto'],
                                    'beneficiario' => $pago['cuenta_abono'],
                                    'monto' => $pago['monto'],
                                    'cuenta_cargo' => ['id_cuenta_cargo' => $cta_cargo->id_cuenta,
                                        'numero' => $cta_cargo->numero,
                                        'abreviatura' => $cta_cargo->abreviatura,
                                        'nombre' => $cta_cargo->empresa->razon_social,
                                        'id_empresa' => $cta_cargo->empresa->id_empresa],
                                    'cuenta_abono' => [
                                        'id_cuenta_abono' => $cuenta_abono?$cuenta_abono->id:null,
                                        'numero' => $cuenta_abono?$cuenta_abono->cuenta_clabe:null,
                                        'abreviatura' => $pago['cuenta_abono'],
                                        'nombre' => ''],
                                    'referencia' => $pago['referencia'],
                                    'referencia_docto' => '   N/A   ',
                                    'origen_docto' => '   N/A   ',
                                    'fecha_pago' => $pago['fecha']
                                );
                            }else {
                                $registros_bitacora[] = array(
                                    'id_documento' => null,
                                    'id_distribucion_recurso' => null,
                                    'id_transaccion' => null,
                                    'id_transaccion_tipo' => '   N/A   ',
                                    'pago_a_generar' => 'Pago a Cuenta (Requiere Aplicación Manual)',
                                    'aplicacion_manual' => true,
                                    'estado' => ['id' => 0, 'estado' => -3, 'descripcion' => '   N/A   '],
                                    'pagable' => true,
                                    'concepto' => $pago['concepto'],
                                    'beneficiario' => $pago['cuenta_abono'],
                                    'monto' => $pago['monto'],
                                    'cuenta_cargo' => ['id_cuenta_cargo' => $cta_cargo->id_cuenta,
                                        'numero' => $cta_cargo->numero,
                                        'abreviatura' => $cta_cargo->abreviatura,
                                        'nombre' => $cta_cargo->empresa->razon_social,
                                        'id_empresa' => $cta_cargo->empresa->id_empresa],
                                    'cuenta_abono' => [
                                        'id_cuenta_abono' => $cuenta_abono?$cuenta_abono->id:null,
                                        'numero' => $cuenta_abono?$cuenta_abono->cuenta_clabe:$pago['cuenta_abono'],
                                        'abreviatura' => $pago['cuenta_abono'],
                                        'nombre' => ''],
                                    'referencia' => $pago['referencia'],
                                    'referencia_docto' => '   N/A   ',
                                    'origen_docto' => '   N/A   ',
                                    'fecha_pago' => $pago['fecha']
                                );
                            }
                        }else{
                            if (count($dist_part) == 1) {
                                $registros_bitacora[] = $this->bitacoraPago($dist_part[0]->documento, $pago);
                            } else {
                                /**
                                 * Se valida si los documentos cuentan con facturas pendientes de pago
                                 */
                                $pendiente_pago = $this->pendientePago($dist_part);
                                if(!is_null($pendiente_pago)) {
                                    $doctos_repetidos[$pendiente_pago->IDDocumento] = $pendiente_pago->IDDocumento;
                                    $registros_bitacora[] = $this->bitacoraPago($pendiente_pago, $pago);
                                }else {
                                    $registros_bitacora[] = array(
                                        'id_documento' => null,
                                        'id_distribucion_recurso' => null,
                                        'id_transaccion' => null,
                                        'id_transaccion_tipo' => '   N/A   ',
                                        'pago_a_generar' => 'N/A',
                                        'aplicacion_manual' => true,
                                        'estado' => ['id' => 0, 'estado' => 3, 'descripcion' => 'Pagada'],
                                        'pagable' => false,
                                        'concepto' => $pago['concepto'],
                                        'beneficiario' => $pago['cuenta_abono'],
                                        'monto' => $pago['monto'],
                                        'cuenta_cargo' => ['id_cuenta_cargo' => $cta_cargo->id_cuenta,
                                            'numero' => $cta_cargo->numero,
                                            'abreviatura' => $cta_cargo->abreviatura,
                                            'nombre' => $cta_cargo->empresa->razon_social,
                                            'id_empresa' => $cta_cargo->empresa->id_empresa],
                                        'cuenta_abono' => [
                                            'id_cuenta_abono' => $cuenta_abono ? $cuenta_abono->id : null,
                                            'numero' => $cuenta_abono ? $cuenta_abono->cuenta_clabe : null,
                                            'abreviatura' => $pago['cuenta_abono'],
                                            'nombre' => ''],
                                        'referencia' => $pago['referencia'],
                                        'referencia_docto' => '   N/A   ',
                                        'origen_docto' => '   N/A   ',
                                        'fecha_pago' => $pago['fecha']
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        return array(
            'data' => $registros_bitacora,
            'resumen' => $this->resumenBitacora($registros_bitacora, $bitacora_nombre)
        );
    }

    private function pendientePago($pendientes_pago)
    {
        foreach ($pendientes_pago as $dato)
        {
            $factura = Factura::pendientePago()->find($dato->documento->IDTransaccionCDC);
            if(!is_null($factura)){
                return $dato->documento;
            }
        }
    }

    private function transaccion_resumen($transacciones){
        $data_transacciones = [];
        foreach ($transacciones as $transaccion){
            $tipo = $transaccion->tipo_transaccion == 65 ? 'F': 'S';
            $data_transacciones[] = [
                'id' => $transaccion->id_transaccion,
                'folio' => $tipo . $transaccion->numero_folio_format,
                'monto' => $transaccion->monto_format,
                'saldo' => $transaccion->saldo_format,
                'fecha' => $transaccion->fecha_format,
                'referencia' => $transaccion->referencia,
            ];
        }
        return $data_transacciones;
    }
}
