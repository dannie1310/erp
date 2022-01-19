<?php


namespace App\Services\CADECO\PortalProveedor;


use App\Imports\CotizacionImport;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\SolicitudAutorizacionAvance;
use App\PDF\PortalProveedores\SolicitudAvanceFormato;
use App\Repositories\CADECO\SolicitudAutorizacionAvanceRepository as Repository;
use App\Utils\ValidacionSistema;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;

class SolicitudAutorizacionAvanceService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudAutorizacionAvanceService constructor.
     * @param SolicitudAutorizacionAvance $model
     */
    public function __construct(SolicitudAutorizacionAvance $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->solicitudes();
    }

    public function store($data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function proveedorConceptos($id, $base)
    {
        return $this->repository->subcontratoAEstimar($id, $base);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function delete($data, $id)
    {
        return $this->repository->eliminar($id, $data['data']);
    }

    public function pdfSolicitudAvanceFormato($id, $data)
    {
        $pdf = new SolicitudAvanceFormato($id, $data['db']);
        return $pdf;
    }

    public function registrarRetencionIva($data, $id)
    {
        return $this->repository->registrarIVARetenido($id, $data);
    }

    public function descargaLayout($id, $base)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', '7200');
        return $this->repository->descargaLayout($id, $base);
    }

    public function cargaLayout($file, $id, $name, $base)
    {
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $file_xls = $this->getFileXls($file, $name);
        $celdas = $this->getDatosPartidas($file_xls);
        $this->verifica = new ValidacionSistema();
        $subcontrato = $this->repository->findSubcontratoProveedor($id, $base);

        $x = 8;
        $partidas = array();
        $partidas_no_validas = array();
        if (count($celdas[0]) != 15) {
            abort(400, 'Archivo XLS no compatible');
        }

        $cadena_validacion = $this->verifica->desencripta($celdas[0][0]);
        $cadena_validacion_exp = explode("|", $cadena_validacion);

        $base_datos = $cadena_validacion_exp[0];
        $id_obra = $cadena_validacion_exp[1];
        $id_validar = $cadena_validacion_exp[2];

        if ($base_datos != $base || $id_obra != $subcontrato->id_obra || $id != $id_validar)
        {
            abort(400, 'El archivo  XLS no corresponde al subcontrato ' . $subcontrato->numero_folio_format);
        }
        $fecha_est = is_numeric($celdas[2][2])?$this->convertToDate($celdas[2][2]):$this->validateDate($celdas[2][2], 'Solicitud');
        $fecha_est_ini = is_numeric($celdas[3][2])?$this->convertToDate($celdas[3][2]):$this->validateDate($celdas[3][2], 'Inicio de Solicitud');
        $fecha_est_fin = is_numeric($celdas[4][2])?$this->convertToDate($celdas[4][2]):$this->validateDate($celdas[4][2], 'Fin de Solicitud');
        if( strtotime($fecha_est_ini) > strtotime($fecha_est_fin) ){
            abort(400, 'La fecha de inicio en posterior a la fecha de finalización.');
        }

        $partidas_invalidas = false;
        while ($x < count($celdas) - 3) {
            if (!is_null($celdas[$x][13])) {
                $decodificado = intval(preg_replace('/[^0-9]+/', '', $this->verifica->desencripta($celdas[$x][14])), 10);
                $item =  ItemSubcontrato::where('id_transaccion',$subcontrato->id_transaccion)->where('id_item', $decodificado)->first();
                if (!$item) {
                    abort(400, 'El archivo  XLS no corresponde al subcontrato ' . $subcontrato->numero_folio_format);
                }
                $contrato = Contrato::where('id_transaccion', '=', $subcontrato->id_antecedente)->where("id_concepto", "=", $item->id_concepto)->first();
                if ($contrato == null) {
                    $contrato = Contrato::where('id_transaccion', '=', $subcontrato->id_antecedente)->where("nivel", "=", $item->nivel)->first();
                }
                $vol_saldo = (float)str_replace(',', '', $celdas[$x][7]);
                $datos_partida = $item->partidasEstimadas(NULL, $subcontrato->id_antecedente, $contrato);
                $datos_partida['no_partida'] = $celdas[$x][0];
                $datos_partida['item_antecedente'] = $datos_partida['id_concepto'];
                if(is_numeric($celdas[$x][9]) && $celdas[$x][9] > 0 && $vol_saldo > 0 &&  $celdas[$x][9] <= $vol_saldo) {
                    $datos_partida['cantidad'] = $celdas[$x][9];
                    $datos_partida['porcentaje_estimado'] = $celdas[$x][9] * 100 / $celdas[$x][5];
                    $datos_partida['importe'] = $celdas[$x][9] * $celdas[$x][6];
                    $datos_partida['cantidad_valida'] = true;
                    $partidas[] = $datos_partida;

                }else if(!is_numeric($celdas[$x][9]) && $celdas[$x][9] != null){
                    $datos_partida['cantidad'] = 'N/V';
                    $datos_partida['porcentaje_estimado'] = 'N/V';
                    $datos_partida['importe'] = 'N/V';
                    $datos_partida['cantidad_valida'] = false;
                    $partidas_invalidas = true;
                    $partidas_no_validas[] = $datos_partida;

                }else if(is_numeric($celdas[$x][9]) && $celdas[$x][9] != null && $celdas[$x][9] > $vol_saldo){
                    $datos_partida['cantidad'] = $celdas[$x][9];
                    $datos_partida['porcentaje_estimado'] = $celdas[$x][9] * 100 / $celdas[$x][5];
                    $datos_partida['importe'] = $celdas[$x][9] * $celdas[$x][6];
                    $datos_partida['cantidad_valida'] = false;
                    $partidas_invalidas = true;
                    $partidas_no_validas[] = $datos_partida;
                }
            }
            $x++;
        }

        $partidas_filtradas = count($partidas_no_validas) > 0 ? $partidas_no_validas:$partidas;
        $observaciones = $celdas[$x + 2][3] == null ? '': (string)$celdas[$x + 2][3];
        $respuesta = [
            'id' => $subcontrato->getKey(),
            'contratista' => $celdas[0][7],
            'fecha_estimacion' => $fecha_est,
            'fecha_inicio_estimacion' => $fecha_est_ini,
            'fecha_fin_estimacion' => $fecha_est_fin,
            'observaciones' => $observaciones,
            'partidas_invalidas' => $partidas_invalidas,
            'referencia' => 'XLY',
            'partidas' => $partidas_filtradas
        ];
        return $respuesta;
    }

    private function getFileXls($file, $name)
    {
        $path = $this->generaDirectorios($name);
        $exp = explode("base64,", $file);
        $data = base64_decode($exp[1]);
        $file_xls = public_path($path["path_xls"]);
        $env = file_put_contents($file_xls, $data);
        return $file_xls;
    }

    private function generaDirectorios($name)
    {
        $name = str_replace('.xlsx', '-', $name) . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/portalProveedor/solicitudAutorizacionAvance/";
        $path_xls = $dir_xls . $name;
        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return [
            'path_xls' => $path_xls,
            'dir_xls' => $dir_xls
        ];
    }

    private function getDatosPartidas($file_xls)
    {
        $rows = Excel::toArray(new CotizacionImport, $file_xls);
        unlink($file_xls);
        return $rows[0];
    }

    private function convertToDate($date){
        $date = date('Y-m-d',(($date - 25569) * 86400));
        $date = new DateTime($date);
        $date->modify('+1 day');
        return $date->format('d/m/Y');
    }

    function validateDate($date, $type)
    {
        $format = 'd/m/Y';
        $d = DateTime::createFromFormat($format, $date);
        if($d && $d->format($format) === $date){
            return $date;
        }
        abort(403, "La fecha de $type es inválida");
    }
}
