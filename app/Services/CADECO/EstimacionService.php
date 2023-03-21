<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO;


use DateTime;
use App\Facades\Context;
use Hamcrest\Type\IsNumeric;
use App\CSV\EstimacionLayout;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Contrato;
use App\Utils\ValidacionSistema;
use App\Imports\CotizacionImport;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Subcontrato;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\PDF\Contratos\EstimacionFormato;
use App\PDF\Contratos\OrdenPagoEstimacion;
use App\CSV\Contratos\EstimacionLayoutEdicion;
use App\Repositories\CADECO\EstimacionRepository as Repository;

class EstimacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EstimacionService constructor.
     */
    public function __construct(Estimacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function pdfOrdenPago($id)
    {
        $pdf = new OrdenPagoEstimacion($id);
       return $pdf;
    }

    public function store($data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio'])){
            $this->repository->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['monto'])){
            $this->repository->where([['monto', 'LIKE', '%'.$data['monto'].'%']]);
        }

        if(isset($data['numero_folio_sub'])){
            $subcontratos = Subcontrato::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio_sub'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente',  $subcontratos]);
        }

        if (isset($data['estado'])) {
            if (strpos('REGISTRADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 0]]);
            }
            else if (strpos('APROBADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 1]]);
            }else if (strpos('REVISADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 2]]);
            }
        }

        if(isset($data['referencia_sub'])){
            $contrato_proyectado = Subcontrato::query()->where([['referencia', 'LIKE', '%'.$data['referencia_sub'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente',  $contrato_proyectado]);
        }

        if(isset($data['contratista'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['contratista'].'%']])->pluck("id_empresa");
            $this->repository->whereIn(['id_empresa', $empresa]);
        }

        if(isset($data['consecutivo'])){
            $estimaciones = \App\Models\CADECO\SubcontratosEstimaciones\Estimacion::query()->where([['NumeroFolioConsecutivo', 'LIKE', '%'.$data['consecutivo'].'%']])->pluck("IDEstimacion");
            $this->repository->whereIn(['id_transaccion',  $estimaciones]);
        }
        return $this->repository->paginate($data);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public  function aprobar($id)
    {
        $estimacion = $this->repository->show($id);
        try {
            DB::connection('cadeco')->beginTransaction();
            $estimacion->aprobar();
            DB::connection('cadeco')->commit();
            $estimacion->refresh();
            return $estimacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public  function anticipo($data, $id)
    {
        $estimacion = $this->repository->show($id);
        return $estimacion->anticipoAmortizacion($data['campo']);
    }

    public function revertirAprobacion($id)
    {
        $estimacion = $this->repository->show($id);
        try {
            DB::connection('cadeco')->beginTransaction();
            $estimacion->revertirAprobacion();
            $estimacion->cancelarRetencion();
            DB::connection('cadeco')->commit();
            $estimacion->refresh();

            return $estimacion;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function pdfEstimacion($id)
    {
        $pdf = new EstimacionFormato($id);
        return $pdf;
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }

    public function registrarRetencionIva($data, $id)
    {
        $estimacion = $this->repository->show($id);
        return $estimacion->registrarIVARetenido($data);
    }

    public function registrarRetencionIsr($data, $id)
    {
        $estimacion = $this->repository->show($id);
        return $estimacion->registrarISRRetenido($data);
    }

    public function ordenado($id)
    {
        return $this->show($id)->subcontratoAEstimar();
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function descargaLayout($id)
    {
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        return $this->repository->descargaLayout($id);
    }

    public function cargaLayout($file, $id, $name)
    {
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $file_xls = $this->getFileXls($file, $name);
        $celdas = $this->getDatosPartidas($file_xls);
        $this->verifica = new ValidacionSistema();
        $subcontrato = Subcontrato::where('id_transaccion', $id)->first();

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

        if ($base_datos != Context::getDatabase() || $id_obra != Context::getIdObra() || $id != $id_validar)
        {
            abort(400, 'El archivo  XLS no corresponde al subcontrato ' . $subcontrato->numero_folio_format);
        }
        $fecha_est = is_numeric($celdas[2][2])?$this->convertToDate($celdas[2][2]):$this->validateDate($celdas[2][2], 'Estimación');
        $fecha_est_ini = is_numeric($celdas[3][2])?$this->convertToDate($celdas[3][2]):$this->validateDate($celdas[3][2], 'Inicio de Estimación');
        $fecha_est_fin = is_numeric($celdas[4][2])?$this->convertToDate($celdas[4][2]):$this->validateDate($celdas[4][2], 'Fin de Estimación');
        if( date_create_from_format('d/m/Y', $fecha_est_ini) > date_create_from_format('d/m/Y', $fecha_est_fin) ){
            abort(400, 'La fecha de inicio es posterior a la fecha de finalización.');
        }

        $partidas_invalidas = false;
        while ($x < count($celdas) - 3) {
            if (!is_null($celdas[$x][13])) {
                $decodificado = intval(preg_replace('/[^0-9]+/', '', $this->verifica->desencripta($celdas[$x][14])), 10);
                $item = $subcontrato->partidas->where('id_item', $decodificado)->first();
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
                $porcentaje = 0;
                if($celdas[$x][5] > 0){
                    $porcentaje = $celdas[$x][9] * 100 / $celdas[$x][5];
                }
                if(is_numeric($celdas[$x][9]) && $celdas[$x][9] > 0 && $vol_saldo > 0 &&  $celdas[$x][9] <= $vol_saldo) {
                    $datos_partida['cantidad'] = $celdas[$x][9];
                    $datos_partida['porcentaje_estimado'] = $porcentaje;
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
                    $datos_partida['porcentaje_estimado'] = $porcentaje;
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

    private function generaDirectorios($name)
    {
        $name = str_replace('.xlsx', '-', $name) . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/contratos/estimacion/";
        $path_xls = $dir_xls . $name;
        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return [
            'path_xls' => $path_xls,
            'dir_xls' => $dir_xls
        ];
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

    public function descargaLayoutEdicion($id){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $estimacion = $this->show($id)->subcontratoAEstimar();
        return Excel::download(new EstimacionLayoutEdicion($estimacion, $id), $estimacion['folio_consecutivo'].'.xlsx');
    }

    public function cargaLayoutEdicion($file, $id, $name){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $file_xls = $this->getFileXls($file, $name);
        $celdas = $this->getDatosPartidas($file_xls);
        $this->verifica = new ValidacionSistema();

        if (count($celdas[0]) != 15) {
            abort(400, 'Archivo XLS no compatible');
        }
        
        $cadena_validacion = $this->verifica->desencripta($celdas[0][0]);
        $cadena_validacion_exp = explode("|", $cadena_validacion);

        $base_datos = $cadena_validacion_exp[0];
        $id_obra = $cadena_validacion_exp[1];
        $id_validar = $cadena_validacion_exp[2];
        $estimacion = $this->show($id);

        if ($base_datos != Context::getDatabase() || $id_obra != Context::getIdObra() || $id != $id_validar)
        {
            abort(400, 'El archivo  XLS no corresponde a la estimacion  ' . $estimacion['folio_consecutivo']);
        }
        
        $fecha_est = is_numeric($celdas[2][2])?$this->convertToDate($celdas[2][2]):$this->validateDate($celdas[2][2], 'Estimación');
        $fecha_est_ini = is_numeric($celdas[3][2])?$this->convertToDate($celdas[3][2]):$this->validateDate($celdas[3][2], 'Inicio de Estimación');
        $fecha_est_fin = is_numeric($celdas[4][2])?$this->convertToDate($celdas[4][2]):$this->validateDate($celdas[4][2], 'Fin de Estimación');
        if( date_create_from_format('d/m/Y', $fecha_est_ini) > date_create_from_format('d/m/Y', $fecha_est_fin) ){
            abort(400, 'La fecha de inicio en posterior a la fecha de finalización.');
        }

        $est_data['fecha'] = $fecha_est;
        $est_data['fecha_inicial'] = $fecha_est_ini;
        $est_data['fecha_final'] = $fecha_est_fin;

        $est_data = $this->show($id)->subcontratoAEstimar();

        $x = 8;
        $partidas = array();
        $partidas_no_validas = array();
        $partidas_invalidas = false;
        $est_data['subcontrato']['partidas_validas'] = true;
        while ($x < count($celdas) - 3) {
            if (!is_null($celdas[$x][14])) {
                $decodificado = intval(preg_replace('/[^0-9]+/', '', $this->verifica->desencripta($celdas[$x][14])), 10);
                if($est_data['subcontrato']['partidas'][$celdas[$x][1]]['id'] != $decodificado){
                    abort(400, 'El archivo  XLS no corresponde al subcontrato ' . $estimacion->numero_folio_format);
                }
                $est_data['subcontrato']['partidas'][$celdas[$x][1]]['numero'] = true;
                $est_data['subcontrato']['partidas'][$celdas[$x][1]]['partida_valida'] = true;
                $est_data['subcontrato']['partidas'][$celdas[$x][1]]['volumen_asignado_mayor'] = false;
                $vol_saldo = (float)str_replace(',', '', $celdas[$x][7]);
                if(is_numeric($celdas[$x][9]) && $celdas[$x][9] <= $vol_saldo) {
                    $porcentaje = 0;
                    if($est_data['subcontrato']['partidas'][$celdas[$x][1]]['cantidad_subcontrato'] > 0){
                        $porcentaje = $celdas[$x][9]*100/$est_data['subcontrato']['partidas'][$celdas[$x][1]]['cantidad_subcontrato'];
                    }
                    $est_data['subcontrato']['partidas'][$celdas[$x][1]]['cantidad_estimacion'] = $celdas[$x][9];
                    $est_data['subcontrato']['partidas'][$celdas[$x][1]]['importe_estimacion'] = $celdas[$x][9]*$est_data['subcontrato']['partidas'][$celdas[$x][1]]['precio_unitario_subcontrato'];
                    $est_data['subcontrato']['partidas'][$celdas[$x][1]]['porcentaje_estimado'] = $porcentaje;
                }else if(is_numeric($celdas[$x][9]) && $celdas[$x][9] > $vol_saldo){
                    $est_data['subcontrato']['partidas'][$celdas[$x][1]]['cantidad_estimacion'] = $celdas[$x][9];
                    $est_data['subcontrato']['partidas'][$celdas[$x][1]]['partida_valida'] = false;
                    $est_data['subcontrato']['partidas'][$celdas[$x][1]]['volumen_asignado_mayor'] = true;
                    $est_data['subcontrato']['partidas_validas'] = false;
                }else if(!is_numeric($celdas[$x][9])){
                    $est_data['subcontrato']['partidas'][$celdas[$x][1]]['cantidad_estimacion'] = 'N/V';
                    $est_data['subcontrato']['partidas'][$celdas[$x][1]]['partida_valida'] = false;
                    $est_data['subcontrato']['partidas_validas'] = false;
                }
            }
            $x++;
        }
        $observaciones = $celdas[$x + 2][3] == null ? '': (string)$celdas[$x + 2][3];
        $est_data['observaciones'] = $observaciones;

        return $est_data;
    }
}
