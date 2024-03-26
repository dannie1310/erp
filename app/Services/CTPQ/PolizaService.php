<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:41 AM
 */

namespace App\Services\CTPQ;
use App\Jobs\ProcessAsociacionCFDI;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDI;
use Chumper\Zipper\Zipper;
use App\Imports\PolizaImport;
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\Empresa;
use App\Models\CTPQ\TipoPoliza;
use App\PDF\CTPQ\PolizaFormatoT1;

use App\PDF\CTPQ\PolizaFormatoT1A;
use App\PDF\CTPQ\PolizaFormatoT1B;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Repositories\CTPQ\PolizaRepository as Repository;
use Maatwebsite\Excel\Facades\Excel;

class PolizaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PolizaService constructor.
     * @param Poliza $model
     */
    public function __construct(Poliza $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show(array $data, $id)
    {
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $empresa = Empresa::find($empresaLocal->IdEmpresaContpaq);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(array $data, $id)
    {
        $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $data["empresa"] = $empresa->AliasBDD;
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        $poliza = $this->repository->show($id);
        if($poliza->Ejercicio == 2015){
            abort(500,"No se pueden editar pólizas del año 2015");
        }
        return $this->repository->update($data, $id);
    }

    public function paginate($data)
    {
        $poliza = $this->repository;
        if(isset($data["id_empresa"])) {
            try {
                $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);

                $empresa = Empresa::find($empresaLocal->IdEmpresaContpaq);
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            } catch (\Exception $e) {
                abort(500, "Error de lectura a la base de datos: " . Config::get('database.connections.cntpq.database') . ". \n \n Favor de contactar a soporte a aplicaciones.");
                throw $e;
            }

            if (isset($data['ejercicio'])) {
                if ($data['ejercicio'] != "") {
                    $poliza->where([['Ejercicio', '=', $data['ejercicio']]]);
                }
            }

            if (isset($data['periodo'])) {
                if ($data['periodo'] != "") {
                    $poliza->where([['Periodo', '=', $data['periodo']]]);
                }
            }

            if (isset($data['folio'])) {
                if ($data['folio'] != '') {
                    $poliza = $poliza->where([['Folio', '=', request('folio')]]);
                }
            }

            if (isset($data['concepto'])) {
                if ($data['concepto'] != "") {
                    $poliza = $poliza->where([['Concepto', 'like', '%' . $data['concepto'] . '%']]);
                }
            }

            if (isset($data['cargos'])) {
                if ($data['cargos'] != "") {
                    $cargos_str = str_replace("$", "", $data['cargos']);
                    $cargos_str = str_replace(",", "", $cargos_str);
                    $poliza = $poliza->where([['Cargos', '=', $cargos_str]]);
                }
            }

            if (isset($data['tipopol'])) {
                if ($data['tipopol'] != '') {
                    $tipo = TipoPoliza::where('Nombre', 'like', '%' . ucfirst(request('tipopol')) . '%')->first();
                    if ($tipo) {
                        $poliza = $poliza->where([['TipoPol', '=', $tipo->Id]]);
                    } else {
                        $poliza = $poliza->where([['TipoPol', '=', 0]]);
                    }
                }
            }
        }else{
            abort(500, "No hay una empresa de ContPaq asociada a la obra del SAO actual.\n \n Favor de contactar a soporte a aplicaciones.");

        }
       return $poliza->paginate($data);

    }

    public function pdf($data, $id)
    {
        $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $pdf = new PolizaFormatoT1A($this->show($data->all(), $id), $empresa);
        return $pdf->create();
    }

    public function pdfCaidaB($data, $id)
    {
        $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $pdf = new PolizaFormatoT1B($this->show($data, $id), $empresa);
        return $pdf->create();
    }

    public function descargaZip($data){
        Storage::disk('polizas_pdf')->delete(Storage::disk('polizas_pdf')->allFiles());
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;

        $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
        foreach($this->busqueda($data)->all() as $i => $poliza){
            if($data["caida"] == 1){
                $pdf = new PolizaFormatoT1A($poliza, $empresa);
            }
            if($data["caida"] == 2){
                $pdf = new PolizaFormatoT1B($poliza, $empresa);
            }
            $pdf->create(config('filesystems.disks.polizas_pdf.root'));
        }

        $zip_name = 'Polizas '.date("Ymdhis") . '.zip';
        $zipper = new Zipper;
        $files = glob(config('filesystems.disks.polizas_pdf.root').'/*');
        $zipper->make(config('filesystems.disks.polizas_zip.root'). '/' . $zip_name)->add($files)->close();

        Storage::disk('polizas_pdf')->delete(Storage::disk('polizas_pdf')->allFiles());
        return Storage::disk('polizas_zip')->download($zip_name);

    }

    private function busqueda($data){
        try {
            $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            $poliza = $this->repository;

            if (isset($data['ejercicio'])) {
                if ($data['ejercicio'] != "") {
                    $poliza->where([['Ejercicio', '=', $data['ejercicio']]]);
                }
            }

            if (isset($data['periodo'])) {
                if ($data['periodo'] != "") {
                    $poliza->where([['Periodo', '=', $data['periodo']]]);
                }
            }

            if (isset($data['folio'])) {
                if($data['folio'] != '') {
                    $poliza = $poliza->where([['Folio', '=', request('folio')]]);
                }
            }

            if (isset($data['concepto']))
            {
                if ($data['concepto'] != "") {
                    $poliza = $poliza->where([['Concepto','like', '%'.$data['concepto'].'%']]);
                }
            }

            if (isset($data['tipo'])) {
                if($data['tipo'] != '') {
                    $tipo = TipoPoliza::where('Nombre', 'like', '%'.ucfirst(request('tipo')).'%')->first();
                    if($tipo) {
                        $poliza = $poliza->where([['TipoPol', '=', $tipo->Id]]);
                    }else{
                        $poliza = $poliza->where([['TipoPol', '=', 0]]);
                    }
                }
            }
           return $poliza;
        }catch (\Exception $e) {
            abort(500,"Error de lectura a la base de datos: ".Config::get('database.connections.cntpq.database').". \n \n Favor de contactar a soporte a aplicaciones.");
            throw $e;
        }
    }

    public function busquedaExcel($data){
        try {
            Storage::disk('polizas_pdf')->delete(Storage::disk('polizas_pdf')->allFiles());
            ini_set('memory_limit', -1) ;
            ini_set('max_execution_time', '7200') ;

            $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            $file = $this->getFileXLS($data['name'], $data['file']);
            $repo = $this->repository;
            $partidas = $this->getPartidas($file);
            $polizas = array();
            $cantidad = 0;
            foreach($partidas as $i => $partida){
                $modelo = Poliza::query();
                $partida['ejercicio'] > 0?$modelo->where('Ejercicio', '=',$partida['ejercicio']):'';
                $partida['periodo'] > 0?$modelo->where('Periodo', '=',$partida['periodo']):'';
                $partida['folio'] > 0?$modelo->where('Folio', '=',$partida['folio']):'';
                $partida['tipo'] > 0?$modelo->where('TipoPol', '=',$partida['tipo']):'';
                $resp = $modelo->get();
                foreach ($resp as $key => $val) {
                    if($data["caida"] == 1){
                        $pdf = new PolizaFormatoT1A($val, $empresa);
                    }
                    if($data["caida"] == 2){
                        $pdf = new PolizaFormatoT1B($val, $empresa);
                    }
                    $pdf->create(config('filesystems.disks.polizas_pdf.root'));
                    $cantidad++;
                }
            }
        }catch (\Exception $e) {
            abort(500,"Error de lectura a la base de datos: ".Config::get('database.connections.cntpq.database').". \n \n Favor de contactar a soporte a aplicaciones.");
            throw $e;
        }
        if($cantidad == 0){abort(500, "No hay pólizas con los datos de búsqueda.");}
        try{
            $zip_name = 'Polizas '.date("Ymdhis") . '.zip';
            $zipper = new Zipper;
            $files = glob(config('filesystems.disks.polizas_pdf.root').'/*');
            $zipper->make(config('filesystems.disks.polizas_zip.root'). '/' . $zip_name)->add($files)->close();

            Storage::disk('polizas_pdf')->delete(Storage::disk('polizas_pdf')->allFiles());
            return $zip_name;

        }catch (\Exception $e) {
            abort(500, "No se pudo generar correctamente el ZIP con las pólizas solicitadas.");
            throw $e;
        }
    }

    private function getFileXLS($nombre_archivo, $archivo_xls)
    {
        $paths = $this->generaDirectorios($nombre_archivo);
        $exp = explode("base64,", $archivo_xls);
        $data = base64_decode($exp[1]);
        $file_xls = public_path($paths["path_xls"]);
        file_put_contents($file_xls, $data);
        return $file_xls;
    }

    private function generaDirectorios($nombre_archivo)
    {
        $nombre = str_replace('.xlsx', '-', $nombre_archivo) . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/CTPQ/poliza/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }

    private function getPartidas($file)
    {
        $partidas = Excel::toArray(new PolizaImport, $file);
        $data = array();
        $ejercicios = array();
        $periodos = array();
        $tipo = array();
        $folios = array();
        foreach ($partidas[0] as $key => $p) {
            if ($key > 0) {
                $data[$key]['ejercicio'] = (int)$p[0];
                $data[$key]['periodo'] = (int)$p[1];
                $data[$key]['tipo'] = (int)$p[2];
                $data[$key]['folio'] = (int)$p[3];
                // if (array_search((int)$p[0], $ejercicios) == false) {
                //     $ejercicios[$key] = (int)$p[0];
                // }
                // if (array_search((int)$p[1], $periodos) == false) {
                //     $periodos[$key] = (int)$p[1];
                // }
                // if (array_search((int)$p[2], $tipo) == false) {
                //     $tipo[$key] = (int)$p[2];
                // }
                // if (array_search((int)$p[3], $folios) == false) {
                //     $folios[$key] = (int)$p[3];
                // }

            }
        }

        return $data;
        // return [
        //     'ejercicios' => $ejercicios,
        //     'periodos' => $periodos,
        //     'tipo' => $tipo,
        //     'folios' => $folios
        // ];
    }

    public function getZip($data){
        // dd($data);
        return Storage::disk('polizas_zip')->download($data['nombreZip']);
    }

    public function setAsociarCFDI($data)
    {
        return $this->repository->asociarCFDI($data);
    }

    public function setDesasociarCFDI($data)
    {
        return $this->repository->desasociarCFDI($data);
    }

    public function asociarCFDI()
    {
        $solicitud =SolicitudAsociacionCFDI::getSolicitudActiva();
        if(!$solicitud){
            $solicitud = $this->generaPeticionesDeAsociacion();
            $datos_solicitud = [
                "folio" =>$solicitud->id,
                "usuario_inicio" =>$solicitud->usuario->nombre_completo,
                "fecha_hora_inicio"=>$solicitud->fecha_hora_inicio_format,
                "mensaje" =>"Proceso de asociación generado éxitosamente, se le enviará un correo al finalizar",
                "icon" =>"success"
            ];
        } else {
            $datos_solicitud = [
                "folio" =>$solicitud->id,
                "usuario_inicio" =>$solicitud->usuario->nombre_completo,
                "fecha_hora_inicio"=>$solicitud->fecha_hora_inicio_format,
                "mensaje" =>"Existe un proceso de asociación de CFDI activo, favor de esperar",
                "icon" =>"warning"
            ];
        }
        return $datos_solicitud;
    }

    public function generaPeticionesDeAsociacion()
    {
        $solicitud = $this->repository->generaSolicitudAsociacion();
        $bases = $this->repository->getListaEmpresas();

        foreach($bases as $empresa=>$base){
            $data = [
                "id_solicitud_asociacion" => $solicitud->id,
                "base_datos" => $base,
                "nombre_empresa" => $empresa,
            ];
            $this->repository->generaPeticionesAsociacion($data);
        }
        $idistribucion = 0;
        foreach($solicitud->partidas as $partida){
            ProcessAsociacionCFDI::dispatch($partida)->onQueue("q".$idistribucion);
            //$partida->procesarAsociacionCFDI();
            $idistribucion ++;
            if($idistribucion==5){
                $idistribucion=0;
            }
        }
        return $solicitud;
    }

    public function listarPosiblesCFDI($parametros)
    {
        return $this->repository->show($parametros["params"]["id_poliza"])->posibles_cfdi;
    }
}
