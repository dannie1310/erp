<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Finanzas\BitacoraSantander;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\Transaccion;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\MODULOSSAO\ControlRemesas\DocumentoProcesado;
use App\Repositories\Repository;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Zend\Validator\Date;

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

    public function bitacoraPago($data, $pago){
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
            'id_distribucion_recurso' => $data->partidaVigente->id_distribucion_recurso,
            'id_transaccion' => $data->transaccion? $data->transaccion->id_transaccion:null,
            'id_transaccion_tipo' => $data->tipoDocumento->TipoDocumento,
            'pago_a_generar' => $pago_a_generar,
            'aplicacion_manual' => $aplicacion_manual,
            'estado' => $data->partidaVigente->estatus,
            'pagable' => $data->partidaVigente->pagable,
            'concepto' => $data->Concepto,
            'beneficiario' => $data->Destinatario,
            'monto' => $pago['monto'],
            'cuenta_cargo' => ['id_cuenta_cargo' => $data->partidaVigente->cuentaCargo->id_cuenta, 'numero'=> $data->partidaVigente->cuentaCargo->numero, 'abreviatura'=> $data->partidaVigente->cuentaCargo->abreviatura, 'nombre' => $data->partidaVigente->cuentaCargo->empresa->razon_social, 'id_empresa' => $data->partidaVigente->cuentaCargo->empresa->id_empresa],
            'cuenta_abono' => ['id_cuenta_abono' => $data->partidaVigente->cuentaAbono->id, 'numero'=> $data->partidaVigente->cuentaAbono->cuenta_clabe, 'abreviatura'=> $data->partidaVigente->cuentaAbono->banco->ctg_banco->nombre_corto, 'nombre' => $data->partidaVigente->cuentaAbono->banco->razon_social],
            'referencia' => $pago['referencia'],
            'referencia_docto' => $data->Referencia,
            'origen_docto' => $data->origenDocumento->OrigenDocumento,
            'fecha_pago' => $pago['fecha']
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
        $nombre = hash_file('md5', $bitacora);
        $file_bitacora = fopen($bitacora, "r") or die("Unable to open file!");
        Storage::disk('portal_carga')->put($nombre . '.txt', $file_bitacora);
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
                if($pago['pagable']) {

                    if ($pago['id_documento'] && $pago['id_distribucion_recurso']) {
//                        $dist_remesa = DistribucionRecursoRemesa::find($pago['id_distribucion_recurso'])->first();
//                        DistribucionRecursoRemesa::find($pago['id_distribucion_recurso'])->remesaValidaEstado();

                        $partida_remesa = DistribucionRecursoRemesaPartida::where('id_distribucion_recurso', '=', $pago['id_distribucion_recurso'])->where('id_documento', '=', $pago['id_documento'])->first();
                        if($partida_remesa->pagable) {
                            $fecha = DateTime::createFromFormat('d/m/Y', $pago['fecha_pago']);
                            $data = array(
                                //"id_cuenta" => $partida_remesa->id_cuenta_cargo,
                                "id_empresa" => $partida_remesa->documento->IDDestinatario,
                                "id_moneda" => $partida_remesa->documento->IDMoneda,
                                "fecha" => $fecha->format('Y-m-d'),
                                "cumplimiento" => $fecha->format('Y-m-d'),
                                "vencimiento" => $fecha->format('Y-m-d'),
                                "monto" => -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute()),
                                //"saldo" => -1 * abs($partida_remesa->documento->MontoTotalSolicitado),
                                "referencia" => $pago['referencia'],
                                //"destino" => $partida_remesa->documento->Destinatario,
                                //"observaciones" => $partida_remesa->documento->Observaciones
                            );
                            if ($partida_remesa->documento->transaccion) {
                                $transaccion = $partida_remesa->documento->transaccion;
                                $pago_remesa = null;
                                switch ($partida_remesa->documento->transaccion->tipo_transaccion) {
                                    case 65:
                                        // se registra un pago
                                        $data["id_antecedente"] = $transaccion->id_antecedente;
                                        $data["id_referente"] = $transaccion->id_transaccion;
                                        unset($data["referencia"]);
                                        $o_pago = OrdenPago::create($data);
                                        $o_pago = OrdenPago::query()->where('id_transaccion', '=', $o_pago->id_transaccion)->first();
                                        unset($data["id_antecedente"]);
                                        unset($data["id_referente"]);
                                        $data["numero_folio"] = $o_pago->numero_folio;
                                        $data["referencia"] = $pago['referencia'];
                                        $data["estado"] = 2;
                                        $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                                        $data["destino"] = $partida_remesa->documento->Destinatario;
                                        $data["observaciones"] = $partida_remesa->documento->Observaciones;
                                        $pago_remesa = Pago::query()->create($data);

                                        break;
                                    case 72:
                                        if ($partida_remesa->documento->IDTipoDocumento == 12) {
                                            unset($data["id_empresa"]);
                                            $data["id_antecedente"] = $transaccion->id_transaccion;
                                            $data["id_referente"] = $transaccion->id_referente;
                                            $data["estado"] = 1;
                                            $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                                            $data["saldo"] = -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute());
                                            $data["destino"] = $partida_remesa->documento->Destinatario;
                                            $data["observaciones"] = $partida_remesa->documento->Observaciones;
                                            $pago_remesa = PagoVario::query()->create($data);


                                        } else {
                                            $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                                            $data["saldo"] = -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute());
                                            $data["destino"] = $partida_remesa->documento->Destinatario;
                                            $data["observaciones"] = $partida_remesa->documento->Observaciones;

                                            $pago_remesa = PagoACuenta::query()->create($data);
                                        }
                                        break;
                                    default:
                                        $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                                        $data["saldo"] = -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute());
                                        $data["destino"] = $partida_remesa->documento->Destinatario;
                                        $data["observaciones"] = $partida_remesa->documento->Observaciones;

                                        $pago_remesa = PagoACuenta::query()->create($data);
                                        break;
                                }
                                $saldo_transaccion = abs($transaccion->saldo - $partida_remesa->documento->getImporteTotalProcesadoAttribute());

                                $transaccion->estado = $saldo_transaccion > 0.99?1:2;
                                $transaccion->saldo = $saldo_transaccion;
                                $transaccion->save();

                            } else {
                                $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                                $data["saldo"] = -1 * abs($partida_remesa->documento->getImporteTotalProcesadoAttribute());
                                $data["destino"] = $partida_remesa->documento->Destinatario;
                                $data["observaciones"] = $partida_remesa->documento->Observaciones;

                                $pago_remesa = PagoACuenta::query()->create($data);
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
                            "observaciones" => $pago['concepto']
                        );
                        $pago_remesa = PagoACuenta::query()->create($data);
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

    public function validarBitacora($bitacora, $bitacora_nombre){
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
                                $doctos_repetidos[$dist_part[0]->id_documento] = $dist_part[0]->id_documento;
                                $registros_bitacora[] = $this->bitacoraPago($dist_part[0]->documento, $pago);
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
}
