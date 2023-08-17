<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\CSV\CotizacionLayout;
use App\Exports\Contabilidad\LayoutPasivosErroresExport;
use App\Exports\Contabilidad\LayoutPasivosIFSExport;
use App\Imports\PasivoImport;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCarga;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCargaRepository;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ListaEmpresaRepository as Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LayoutPasivoCargaService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param LayoutPasivoCarga $model
     */
    public function __construct(LayoutPasivoCarga $model)
    {
        $this->repository = new LayoutPasivoCargaRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function asociarCFDI($id_pasivo)
    {
        return $this->repository->asociarCFDI($id_pasivo);
    }

    public function listarPosiblesCFDI($id_pasivo)
    {
        return $this->repository->listarPosiblesCFDI($id_pasivo);
    }

    public function procesaLayoutPasivos($data)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '7200');
        $file_xls = $this->getFileXls($data['name'], $data['file']);
        $excelToArray = Excel::toArray(new PasivoImport, $file_xls);

        $rows = $excelToArray[0];

        $hash = explode(".",basename($file_xls));

        if(is_array($this->validaLayout($rows))) {
            return response()->json([
                'message' => 'Error',
                'hash_file'=>$hash[0]
            ], 466);
        }
        try {
            DB::connection('seguridad')->beginTransaction();
            $guardar_pasivo = $this->repository->create([
                "nombre_archivo" => $data['name'],
                "usuario_cargo" => auth()->id(),
                "estado" => 1,
            ]);
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, 'Error al registrar la carga del archivo. \n' . $e);
        }


        foreach ($rows as $key_row => $pasivo) {
            $con_datos = false;
            foreach ($pasivo as $key_cell => $valor){
                $pasivos_salida[$key_row][$key_cell]["valor"] = $valor;
                if($valor != "" && $valor != null)
                {
                    $con_datos = true;
                }

            }
            if ($key_row > 0 && $con_datos) {

                $empresa = Empresa::where('AliasBDD', "=", $pasivo[1])->first();

                try {
                    $fecha = Date::excelToDateTimeObject($pasivo[8]);
                    $fecha = (date_format($fecha, "Y/m/d"));
                } catch (\Exception $e) {
                    try {
                        $fecha = Carbon::createFromFormat('d/m/Y', $pasivo[8]);
                        $fecha = (date_format($fecha, "Y/m/d"));
                    } catch (\Exception $e) {
                        DB::connection('seguridad')->rollBack();
                        abort(400, 'Error en el formato de fecha de la partida ' . ($key_row));
                    }
                }

                $importe_mxn = $pasivo[11] > 0 ? $pasivo[9] * $pasivo[11] : $pasivo[12];
                $saldo_mxn = $pasivo[14] > 0 ? $pasivo[13] * $pasivo[14] : $pasivo[15];
                $importe_mxn_con_tc_saldo = $pasivo[14] > 0 ? $pasivo[9] * $pasivo[14] : $pasivo[12];
                $inconsistencia_saldo = ($importe_mxn_con_tc_saldo - $saldo_mxn) < -1 ? 1 : 0;

                $guardar_pasivo->partidas()->create([
                    "obra" => $empresa->Descripcion != '' ? $empresa->Descripcion : $pasivo[0],
                    "bbdd_contpaq" => $pasivo[1],
                    "rfc_empresa" => $empresa->empresaSAT->rfc,
                    "empresa" => $empresa->empresaSAT->razon_social,
                    "rfc_proveedor" => $pasivo[4],
                    "proveedor" => $pasivo[5],
                    "concepto" => $pasivo[6],
                    "folio_factura" => $pasivo[7],
                    "fecha_factura" => $fecha,
                    "importe_factura" => $pasivo[9],
                    "moneda_factura" => $pasivo[10],
                    "tc_factura" => $pasivo[11],
                    "importe_mxn" => $importe_mxn,
                    "saldo" => $pasivo[13],
                    "tc_saldo" => $pasivo[14],
                    "saldo_mxn" => $saldo_mxn,
                    "inconsistencia_saldo" => $inconsistencia_saldo
                ]);
            }
        }

        DB::connection('seguridad')->commit();
        return $guardar_pasivo;

    }

    public function descargarLayoutErrores($hash)
    {
        $file_xls = "uploads/contabilidadGeneral/layoutPasivo/".$hash.".xlsx";
        $excelToArray = Excel::toArray(new PasivoImport, $file_xls);
        $rows = $excelToArray[0];
        $pasivos_salida = $this->validaLayout($rows);

        return Excel::download(new LayoutPasivosErroresExport($pasivos_salida), $hash.'_errores.xlsx');

    }

    private function validaLayout($rows)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '7200');


        if (count($rows) == 0) {
            abort(400, 'Error al cargar archivo, debe contar con al menos una partida');
        }

        $pasivos_salida = [];
        $partidas_no_validas = false;

        foreach ($rows as $key_row => $pasivo) {
            $con_datos = false;
            foreach ($pasivo as $key_cell => $valor){
                $pasivos_salida[$key_row][$key_cell]["valor"] = $valor;
                if($valor != "" && $valor != null)
                {
                    $con_datos = true;
                }

            }
            if($key_row > 0 && $con_datos)
            {
                if($pasivo[1] == null || $pasivo[1] == ''){
                    $partidas_no_validas = true;
                    $pasivos_salida[$key_row][1]["error"] = "El valor de la base de datos contpaq no fue ingresado";
                }else{
                    $empresa = Empresa::where('AliasBDD', "=", $pasivo[1])->first();
                    if ($empresa == null) {
                        $partidas_no_validas = true;
                        $pasivos_salida[$key_row][1]["error"] = "La base de datos contpaq ingresada no existe, favor de corregir.";
                    }
                }

                if($pasivo[8] == null || $pasivo[8] == ''){
                    $partidas_no_validas = true;
                    $pasivos_salida[$key_row][8]["error"] = "El valor de la fecha no fue ingresado";
                }else{
                    try {
                        $fecha = Date::excelToDateTimeObject($pasivo[8]);
                        $fecha = (date_format($fecha, "Y/m/d"));
                    } catch (\Exception $e) {
                        try{
                            $fecha = Carbon::createFromFormat('d/m/Y', $pasivo[8]);
                            $fecha = (date_format($fecha, "Y/m/d"));
                        }
                        catch (\Exception $e) {
                            $partidas_no_validas = true;
                            $pasivos_salida[$key_row][8]["error"] = "El formato de la fecha es erróneo debe ser dd/mm/yy o dd/mm/yyyy, p. ej. 12/01/22 o 12/01/2022.";
                        }
                    }
                }

                if($pasivo[9] == null || $pasivo[9] == ''){
                    $partidas_no_validas = true;
                    $pasivos_salida[$key_row][9]["error"] = "El valor del importe no fue ingresado";
                }else{
                    if(!is_numeric($pasivo[9])){
                        $partidas_no_validas = true;
                        $pasivos_salida[$key_row][9]["error"] = "El valor del importe no es numérico, verifique que no se haya ingresado una formula";
                    }
                }

                if($pasivo[11] == null || $pasivo[11] == ''){
                    $partidas_no_validas = true;
                    $pasivos_salida[$key_row][11]["error"] = "El valor del tipo de cambio no fue ingresado";
                }else{
                    if(!is_numeric($pasivo[11])){
                        $partidas_no_validas = true;
                        $pasivos_salida[$key_row][11]["error"] = "El valor del tipo de cambio no es numérico, verifique que no se haya ingresado una formula";
                    }
                }

                if($pasivo[12] == null || $pasivo[12] == ''){
                    $partidas_no_validas = true;
                    $pasivos_salida[$key_row][12]["error"] = "El valor del importe en pesos mexicanos no fue ingresado";
                }else{
                    if(!is_numeric($pasivo[12])){
                        $partidas_no_validas = true;
                        $pasivos_salida[$key_row][12]["error"] = "El valor del importe en pesos mexicanos no es numérico, verifique que no se haya ingresado una formula";
                    }
                }

                if($pasivo[13] == null || $pasivo[13] == ''){
                    $partidas_no_validas = true;
                    $pasivos_salida[$key_row][13]["error"] = "El valor del saldo no fue ingresado";
                }else{
                    if(!is_numeric($pasivo[13])){
                        $partidas_no_validas = true;
                        $pasivos_salida[$key_row][13]["error"] = "El valor del saldo no es numérico, verifique que no se haya ingresado una formula";
                    }
                }

                if($pasivo[14] == null || $pasivo[14] == ''){
                    $partidas_no_validas = true;
                    $pasivos_salida[$key_row][14]["error"] = "El valor del tipo de cambio para calcular el saldo no fue ingresado";
                }else{
                    if(!is_numeric($pasivo[14])){
                        $partidas_no_validas = true;
                        $pasivos_salida[$key_row][14]["error"] = "El valor del tipo de cambio para calcular el saldo no es numérico, verifique que no se haya ingresado una formula";
                    }
                }

                if($pasivo[15] == null || $pasivo[15] == ''){
                    $partidas_no_validas = true;
                    $pasivos_salida[$key_row][15]["error"] = "El valor del saldo en pesos mexicanos no fue ingresado";
                }else{
                    if(!is_numeric($pasivo[15])){
                        $partidas_no_validas = true;
                        $pasivos_salida[$key_row][15]["error"] = "El valor del saldo en pesos mexicanos no es numérico, verifique que no se haya ingresado una formula";
                    }
                }
            }
        }

        if($partidas_no_validas && count($pasivos_salida)>0)
        {
            return $pasivos_salida;
        }

        return true;
    }

    private function getFileXLS($nombre_archivo, $archivo_xls)
    {
        $paths = $this->generaDirectorios($nombre_archivo);
        $exp = explode("base64,", $archivo_xls);
        $data = base64_decode($exp[1]);
        $file_xls = public_path($paths["path_xls"]);
        $nombre_explode = explode(".", $nombre_archivo);
        $extension = end($nombre_explode);
        file_put_contents($file_xls, $data);
        $hashfile = hash_file('sha1', $file_xls)."_".date("Ymdhisu");
        $hashfile_path = $paths['dir_xls'].$hashfile.".".$extension;
        if(copy($file_xls, $hashfile_path))
        {
            unlink($file_xls);
        }
        return $hashfile_path;
    }

    private function generaDirectorios($nombre_archivo)
    {
        $nombre_archivo = pathinfo($nombre_archivo, PATHINFO_FILENAME);
        $nombre = $nombre_archivo . "_" . date("Ymd_his") . ".xlsx";
        $dir_xls = "uploads/contabilidadGeneral/layoutPasivo/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }

    public function validaDescargarLayoutIFS($id)
    {
        $respuesta_coincidencia_con_cfdi = true;
        $respuesta_inconsistencia_saldo = true;

        $cantidad_pasivos_falta_coincidencia = LayoutPasivoPartida::where("id_carga", "=", $id)
            ->where(
                function ($q){
                    $q->orWhere("coincide_rfc_proveedor", "=", 0)
                        ->orWhere("coincide_rfc_empresa", "=", 0)
                        ->orWhere("coincide_folio", "=", 0)
                        ->orWhere("coincide_fecha", "=", 0)
                        ->orWhere("coincide_importe", "=", 0)
                        ->orWhere("coincide_moneda", "=", 0);
                }
            )
            ->get()
            ->count();


        if ($cantidad_pasivos_falta_coincidencia > 0) {
            $respuesta_coincidencia_con_cfdi = false;
            //return ["respuesta" => false];
            //abort(403, "Algunos pasivos de la carga tienen diferencia en los datos respecto al CFDI que le corresponde, favor de corregir.");
        }

        $cantidad_pasivos_inconsistencia_saldo = LayoutPasivoPartida::where("id_carga", "=", $id)
            ->where("inconsistencia_saldo", "=", 1)
            ->count();

        if ($cantidad_pasivos_inconsistencia_saldo > 0) {
            $respuesta_inconsistencia_saldo = false;
            //return ["respuesta" => false];
            //abort(403, "Algunos pasivos de la carga tienen un saldo mayor al monto de la factura, favor de corregir");
        }

        return ["respuesta_inconsistencia_saldo" => $respuesta_inconsistencia_saldo,
            "respuesta_coincidencia_con_cfdi" => $respuesta_coincidencia_con_cfdi];;
    }

    public function descargarLayoutIFS($id)
    {
        $cantidad_pasivos_falta_coincidencia = LayoutPasivoPartida::where("id_carga", "=", $id)
            ->where(
                function ($q){
                    $q->orWhere("coincide_rfc_proveedor", "=", 0)
                        ->orWhere("coincide_rfc_empresa", "=", 0)
                        ->orWhere("coincide_folio", "=", 0)
                        ->orWhere("coincide_fecha", "=", 0)
                        ->orWhere("coincide_importe", "=", 0)
                        ->orWhere("coincide_moneda", "=", 0);
                }
            )
            ->get()
            ->count();

        $cantidad_pasivos_inconsistencia_saldo = LayoutPasivoPartida::where("id_carga", "=", $id)
            ->where("inconsistencia_saldo", "=", 1)
            ->get()
            ->count();

        if ($cantidad_pasivos_falta_coincidencia == 0 && $cantidad_pasivos_inconsistencia_saldo == 0) {
            $lista_pasivos = LayoutPasivoPartida::where("id_carga", "=", $id)->get();
            return Excel::download(new LayoutPasivosIFSExport($lista_pasivos), 'pasivos_ifs' . "_" . date('dmY_His') . '.xlsx');
        }
    }
}
