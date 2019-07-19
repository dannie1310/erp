<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 11:37 AM
 */

namespace App\Services\CADECO\Finanzas;


use App\Facades\Context;
use App\LAYOUT\DistribucionRecursoRemesaManual;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\Transaccion;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Repositories\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DistribucionRecursoRemesaService
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

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store(array $data)
    {
        $documentos = $data['documentos'];
        $partida = [];
        try {
            DB::connection('cadeco')->beginTransaction();

            $distribucion = [
                'id_remesa' => $data['id_remesa'],
                'monto_autorizado' => $data['monto_total_remesa'],
                'monto_distribuido' => $data['total_selecionado']
            ];
            $d = DistribucionRecursoRemesa::query()->create($distribucion);

            foreach ($documentos as $documento) {
                if (!empty($documento['selected']) && $documento['selected'] == true) {
                    $partida = [
                        'id_distribucion_recurso' => $d->id,
                        'id_documento' => $documento['id'],
                        'id_cuenta_abono' => $documento['id_cuenta_abono'],
                        'id_cuenta_cargo' => $documento['id_cuenta_cargo'],
                        'id_moneda' => $documento['moneda']['id']
                    ];
                    DistribucionRecursoRemesaPartida::query()->create($partida);
                }
            }

            DB::connection('cadeco')->commit();
            return $d;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function layoutDistribucionRemesa($id)
    {
        $layout = new \App\LAYOUT\DistribucionRecursoRemesa($id);
        return $layout->create();
    }

    public function layoutDistribucionRemesaManual($id){
        $layout = new DistribucionRecursoRemesaManual($id);
        return $layout->create();
    }

    public function cargaLayout(Request $request, $id){
        $data = array();
        $file = $request->file('file');
        $nombre = $request->file('file')->getClientOriginalName();
        if(pathinfo($nombre, PATHINFO_EXTENSION) != 'out'){
            abort(400, 'Archivo Invalido');
        }
        $split = explode('.', $nombre);

        $data = $this->getOutData($file);

        if(count($data) <= 2){
            abort(400, 'Archivo sin partidas');
        }

        $this->procesarCargaLayout($data);

        dd($data);
    }

    public function procesarCargaLayout($data){
        $sentido = substr($data[0], 14, 1);
        $val_bloque = substr($data[0], 33, 2);
        $id_distribucion = substr($data[1], 422, 30);
        if($sentido != 'S'){
            abort(400, 'Archivo de entrada no valido.');
        }

        if($val_bloque != '00'){
            abort(400, 'Archivo de entrada rechazado por el banco');
        }

        $dist_recurso = DistribucionRecursoRemesa::find($id_distribucion)->remesaValidaEstado();

        try{
            DB::connection('cadeco')->beginTransaction();

            for ($i = 1; $i < count($data) -1; $i++){
                $id_documento = substr($data[$i], 228, 40);
                $val_partida= substr($data[$i], 400, 2);

                $dist_recurso_partida = DistribucionRecursoRemesaPartida::where('id_distribucion_recurso', '=', $dist_recurso->id)->where('id_documento', '=', $id_documento)->first()->partidaValidaEstado();

                if($val_partida== 0){
                    $documento = Documento::with('tipoDocumento')->where('IDRemesa', '=', $dist_recurso->id_remesa)->where('IDDocumento', '=', $id_documento)->first();
                    $transaccion = Transaccion::find($documento->IDTransaccionCDC);
                    if($transaccion){

                    }

                    dd($transaccion);
                }
                dd($val_partida== 0);
            }




            /** @var  $dist_layout_registro, Actualizacion del registro del layout */
            $dist_layout_registro = DistribucionRecursoRemesaLayout::where('id_distrubucion_recurso', '=', $id_distribucion)->first();
            $dist_layout_registro->usuario_carga = auth()->id();
            $dist_layout_registro->fecha_hora_carga = date('Y-m-d h:i:s');
            $dist_layout_registro->folio_confirmacion_bancaria = $val_bloque;
            $dist_layout_registro->save();

            dd($dist_recurso, $sentido, $val_bloque, $id_distribucion);
            dd('stop');
            DB::connection('cadeco')->commit();
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }



    }

    public function getOutData($docFile){
        $myfile = fopen($docFile, "r") or die("Unable to open file!");
        $content = array();
        while(!feof($myfile)) {
            $linea = str_replace("\n","",fgets($myfile));
            $content[] = $linea;
        }
        fclose($myfile);
        return $content;
    }

    public function cancelar($id){
        try{
            DB::connection('cadeco')->beginTransaction();
            $resp = $this->repository->show($id)->cancelar();
            DB::connection('cadeco')->commit();
            return $resp;
            }catch (\Exception $e){
        DB::connection('cadeco')->rollBack();
        abort(400, $e->getMessage());
        throw $e;
        }
    }

    public function autorizar($id){
        try{
            DB::connection('cadeco')->beginTransaction();
            $resp = $this->repository->show($id)->autorizar();
            DB::connection('cadeco')->commit();
            return $resp;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function cargaLayoutManual(Request $request, $id){
        $file_mismo_banco = $request->file_mismo_banco;
        $file_interbancario = $request->file_interbancario;
        $interbancario = array();
        $mismo_banco = array();
        $pagos = [];

        if($file_mismo_banco != "null") {
            $mismo_banco = $this->getCsvData($file_mismo_banco);
        }
        if($file_interbancario != "null") {
            $interbancario = $this->getDocData($file_interbancario);
        }
        $pagos = array_merge($mismo_banco, $interbancario);

        return $this->registrarPagos($pagos, $id);
    }

    public function registrarPagos($pagos, $id){
        try {
            DB::connection('cadeco')->beginTransaction();
            DistribucionRecursoRemesa::find($id)->remesaValidaEstado();
            $remesa = DistribucionRecursoRemesa::with('partida')->find($id);
            if($remesa->partida->count() != count($pagos)){abort(403, "El archivo de entrada no contiene las mismas partidas de remesa.");}

            foreach ($pagos as $pago) {
                $partida_remesa = DistribucionRecursoRemesaPartida::where('id_distribucion_recurso', '=', $id)->where('id_documento', '=', $pago['documento'])->first();
                if(!$partida_remesa) abort(403, "El archivo de entrada no corresponde a la distribución seleccionada .");
                if($partida_remesa->estado != 1) abort(403, "El archivo de entrada contiene partidas con estado: " . $partida_remesa->estatus->descripcion);
                $data = array(
                    //"id_cuenta" => $partida_remesa->id_cuenta_cargo,
                    "id_empresa" => $partida_remesa->documento->IDDestinatario,
                    "id_moneda" => $partida_remesa->documento->IDMoneda,
                    "monto" => -1 * abs($partida_remesa->documento->MontoTotalSolicitado),
                    //"saldo" => -1 * abs($partida_remesa->documento->MontoTotalSolicitado),
                    "referencia" => $partida_remesa->cuentaAbono->cuenta_clabe,
                    //"destino" => $partida_remesa->documento->Destinatario,
                    //"observaciones" => $partida_remesa->documento->Observaciones
                );
                //dd($partida_remesa->documento);
                if ($transaccion = Transaccion::find($partida_remesa->documento->IDTransaccionCDC)) {
                    $pago_remesa = null;
                    switch ($transaccion->tipo_transaccion) {
                        case 65:
                                 // se registra un pago
                            $data["id_antecedente"] = $transaccion->id_antecedente;
                            $data["id_referente"] = $transaccion->id_transaccion;
                            $o_pago = OrdenPago::query()->create($data);
                            $o_pago = OrdenPago::query()->where('id_transaccion', '=', $o_pago->id_transaccion)->first();
                            unset($data["id_antecedente"]);
                            unset($data["id_referente"]);
                            $data["numero_folio"] = $o_pago->numero_folio;
                            $data["estado"] = 2;
                            $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                            $data["destino"] = $partida_remesa->documento->Destinatario;
                            $data["observaciones"] = $partida_remesa->documento->Observaciones;
                            $pago_remesa = Pago::query()->create($data);

                            break;
                        case 72:
                            if($partida_remesa->documento->IDTipoDocumento == 12){
                                unset($data["id_empresa"]);
                                $data["id_antecedente"] = $transaccion->id_transaccion;
                                $data["id_referente"] = $transaccion->id_referente;
                                $data["estado"] = 1;
                                $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                                $data["saldo"] = -1 * abs($partida_remesa->documento->MontoTotalSolicitado);
                                $data["referencia"] = $pago['clave_bancaria'];
                                $data["destino"] = $partida_remesa->documento->Destinatario;
                                $data["observaciones"] = $partida_remesa->documento->Observaciones;
                                $pago_remesa = PagoVario::query()->create($data);


                            }else{
                                $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                                $data["saldo"] = -1 * abs($partida_remesa->documento->MontoTotalSolicitado);
                                $data["destino"] = $partida_remesa->documento->Destinatario;
                                $data["observaciones"] = $partida_remesa->documento->Observaciones;

                                $pago_remesa = PagoACuenta::query()->create($data);
                            }
                            break;
                        default:
                            $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                            $data["saldo"] = -1 * abs($partida_remesa->documento->MontoTotalSolicitado);
                            $data["destino"] = $partida_remesa->documento->Destinatario;
                            $data["observaciones"] = $partida_remesa->documento->Observaciones;

                            $pago_remesa = PagoACuenta::query()->create($data);
                            break;
                    }
                    $transaccion->estado = 2;
                    $transaccion->save();

                } else {
                    $data["id_cuenta"] = $partida_remesa->id_cuenta_cargo;
                    $data["saldo"] = -1 * abs($partida_remesa->documento->MontoTotalSolicitado);
                    $data["destino"] = $partida_remesa->documento->Destinatario;
                    $data["observaciones"] = $partida_remesa->documento->Observaciones;

                    $pago_remesa = PagoACuenta::query()->create($data);
                }
                $partida_remesa->estado = 2;
                $partida_remesa->id_transaccion_pago = $pago_remesa->id_transaccion;
                $partida_remesa->folio_partida_bancaria = $pago['clave_bancaria'];
                $partida_remesa->save();

            }

            $distribucion = DistribucionRecursoRemesa::query()->find($id);
            $distribucion->estado = 3;
            $distribucion->save();
            $distribucion_layout = DistribucionRecursoRemesaLayout::query()->where('id_distrubucion_recurso', '=', $id)->first();
            $distribucion_layout->usuario_carga = auth()->id();
            $distribucion_layout->fecha_hora_carga = date('Y-m-d');
            $distribucion_layout->folio_confirmacion_bancaria = date('Y-m-d');
            $distribucion_layout->save();
            DB::connection('cadeco')->commit();
            return $distribucion;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, "Error archivos de entrada invalidos.");
            throw $e;
        }
    }

    public function getDocData($docFile){
        $myfile = fopen($docFile, "r") or die("Unable to open file!");
        $content = array();
        while(!feof($myfile)) {
            $linea = str_replace("\n","",fgets($myfile));
            $content[] = array(
                "cuenta_cargo"      => str_replace("  ","",substr($linea, 0, 16)),
                "cuenta_abono"      => str_replace("  ","",substr($linea, 17, 19)),
                "nombre_corto"      => str_replace("  ","",substr($linea, 36, 5)),
                "razon_social"      => str_replace("  ","",substr($linea, 41, 40)),
                "monto"             => str_replace("  ","",substr($linea, 81, 19)),
                "clave"             => str_replace("  ","",substr($linea, 101, 4)),
                "fecha_aplicacion"  => '',
                "documento"         => str_replace("  ","",substr($linea, 106, 9)),
                "concepto"          => str_replace("  ","",substr($linea, 115, 120)),
                "clave_bancaria"    => '',
                "control"           => str_replace("  ","",substr($linea, 225, 7)),
                "control2"          => str_replace("  ","",substr($linea, 232, 8)),
            );
        }
        fclose($myfile);
        return $content;
    }

    public function getCsvData($csvFile){

        $file =  fopen($csvFile, "r") or die("Unable to open file!");
        $all_data = array();
        $encabezados = 0;
        while ( $data = fgetcsv($file, '', ",") ){
            if($encabezados > 0){
                $all_data[] = array(
                    "cuenta_cargo"      => str_replace("\t","",$data[0]),
                    "cuenta_abono"      => str_replace("\t","",$data[1]),
                    "nombre_corto"      => '',
                    "razon_social"      => '',
                    "monto"             => str_replace("\t","",$data[2].$data[3]),
                    "clave"             => '',
                    "fecha_aplicacion"  => str_replace("\t","",$data[4]),
                    "documento"         => substr($data[5], 1, 9),
                    "concepto"          => str_replace("\t","",$data[5]),
                    "clave_bancaria"    => str_replace("\t","",$data[7]),
                    "control"           => '',
                    "control2"          => ''

                );
            }
            $encabezados++;
        }
        fclose($file);
        return $all_data;
    }
}
