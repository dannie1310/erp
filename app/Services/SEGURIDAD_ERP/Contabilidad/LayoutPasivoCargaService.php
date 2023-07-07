<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Exports\Contabilidad\ListaEmpresasExport;
use App\Exports\FinanzasGlobal\SolicitudesPagoAplicadasExport;
use App\Facades\Context;
use App\Imports\PasivoImport;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCarga;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCargaRepository;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ListaEmpresaRepository as Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Utils\ValidacionSistema;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LayoutPasivoCargaService{

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

    public function procesaLayoutPasivos($data){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $partidas_no_validas = false;
        $file_xls = $this->getFileXls($data['name'], $data['file']);
        $rows = Excel::toArray(new PasivoImport, $file_xls);
        unlink($file_xls);
        $celdas = $rows[0];
        if(count($celdas) == 0 ) {
            DB::connection('seguridad')->rollBack();
            abort(400, 'Error al cargar archivo debe contar con partidas');
        }
        try {
            DB::connection('seguridad')->beginTransaction();
            $guardar_pasivo = $this->repository->create([
                "nombre_archivo" => $data['name'],
                "usuario_cargo" => auth()->id(),
                "estado" => 1,
            ]);
            if ($guardar_pasivo == null) {
                abort(400, 'El archivo  XLS no corresponde.');
            }

            foreach ($celdas as $key => $pasivo) {
                if ($key > 0 && $pasivo[0] != null && (is_numeric($pasivo[11]) && is_numeric($pasivo[12])))
                {
                   if(($pasivo[1] == null || $pasivo[1] == '')
                        || ($pasivo[3] == null || $pasivo[3] == '')
                        || ($pasivo[6] == null || $pasivo[6] == '')
                        || ($pasivo[7] == null || $pasivo[7] == '')
                        || ($pasivo[8] == null || $pasivo[8] == '')
                        || ($pasivo[12] == null || $pasivo[12] == ''))
                   {
                       abort(404, 'Faltan datos obligatorios para poder realizar la carga.');
                   }

                    $empresa = Empresa::where('AliasBDD', $pasivo[1])->first();
                    if ($empresa == null) {
                        abort(400, 'No se encuentra la empresa en bases.');

                    }
                    $guardar_pasivo->partidas()->create([
                        "id_carga" => $guardar_pasivo->getKey(),
                        "obra" => $pasivo[0],
                        "bbdd_contpaq" => $pasivo[1],
                        "rfc_empresa" => $empresa->empresaSAT->rfc,
                        "empresa" => $empresa->empresaSAT->razon_social,
                        "rfc_proveedor" => $pasivo[3],
                        "proveedor" => $pasivo[4],
                        "concepto" => $pasivo[5],
                        "folio_factura" => $pasivo[6],
                        "fecha_factura" => $pasivo[7],
                        "importe_factura" => $pasivo[8],
                        "moneda_factura" => $pasivo[9],
                        "tc_factura" => $pasivo[10],
                        "importe_mxn" => $pasivo[11],
                        "saldo" => $pasivo[12],
                        "uuid" => 0
                    ]);
                }
            }
            DB::connection('seguridad')->commit();
            return $guardar_pasivo;
        } catch (\Exception $e){
            DB::connection('seguridad')->rollBack();
            abort(400, 'Error al cargar archivo'.$e);
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
        $nombre = $nombre_archivo . "_" . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/contabilidadGeneral/layoutPasivo/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }
}
