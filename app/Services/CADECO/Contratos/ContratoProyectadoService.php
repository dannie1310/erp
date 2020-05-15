<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO\Contratos;


use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contrato;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SolicitudEdicionImport;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Contratos\AreaSubcontratante;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;

class ContratoProyectadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ContratoProyectadoService constructor.
     * @param ContratoProyectado $model
     */
    public function __construct(ContratoProyectado $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function find($id)
    {
        return $this->repository->where('id_transaccion', '=', $id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data){
        try {
            DB::connection('cadeco')->beginTransaction();
            $fecha_cp = strtotime($data['fecha']);
            $fecha_cump = strtotime($data['cumplimineto']);
            $fecha_vencim = strtotime($data['vencimiento']);

            $contrato_proyectado = $this->repository->create([
                'fecha' => date('Y-m-d',$fecha_cp),
                'cumplimiento' => date('Y-m-d',$fecha_cump) ,
                'vencimiento' => date('Y-m-d',$fecha_vencim) ,
                'referencia' => $data['referencia'],
            ]);
            $contrato_proyectado = $this->repository->show($contrato_proyectado->id_transaccion);
            $contrato_proyectado->areaSubcontratante()->create([
                'id_transaccion' => $contrato_proyectado->id_transaccion,
                'id_area_subcontratante' => $data['id_area_subcontratante'],
            ]);

            $nivel_anterior = 0;
            $nivel_contrato_anterior = '';
            foreach($data['contratos'] as $key => $contrato){
                $nivel = '';
                if($nivel_contrato_anterior == ''){
                    $nivel = '000.';
                    $nivel_contrato_anterior = $nivel;
                    $nivel_anterior = $contrato['nivel'];
                }else{
                    if($nivel_anterior + 1 == $contrato['nivel']){
                        $cant = Contrato::where('nivel', 'LIKE', $nivel_contrato_anterior.'___.')->where('id_transaccion', '=', $contrato_proyectado->id_transaccion)->count();
                        $nivel = $nivel_contrato_anterior . str_pad($cant, 3, 0, 0) . '.';
                        $nivel_contrato_anterior = $nivel;
                        $nivel_anterior = $contrato['nivel'];
                    }else{
                        $cant = Contrato::where('nivel', 'LIKE', substr($nivel_contrato_anterior, 0, (($contrato['nivel'] - 1) * 4)) . '___.')->where('id_transaccion', '=', $contrato_proyectado->id_transaccion)->count();
                        $nivel = substr($nivel_contrato_anterior, 0, (($contrato['nivel'] - 1) * 4)) . str_pad($cant, 3, 0, 0) . '.';
                        $nivel_contrato_anterior = $nivel;
                        $nivel_anterior = $contrato['nivel'];
                    }

                }
                $datos = array();
                $datos['id_transaccion'] = $contrato_proyectado->id_transaccion;
                $datos['nivel'] = $nivel;
                $datos['descripcion'] = $contrato['descripcion'];
                $datos['clave'] = $contrato['clave'];

                if($contrato['es_hoja']){
                    $datos['id_destino'] = $contrato['destino'];
                    $datos['unidad'] = $contrato['unidad'];
                    $datos['cantidad_original'] = $contrato['cantidad'];
                    $datos['cantidad_presupuestada'] = $contrato['cantidad'];
                }

                $contrato_proyectado->conceptos()->create($datos);
            }

            DB::connection('cadeco')->commit();

            return $contrato_proyectado;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->update([
            'fecha' => $data['fecha_date'],
            'cumplimiento' => $data['cumplimiento'],
            'vencimiento' => $data['vencimiento'],
            'referencia' => strtoupper($data['referencia'])
        ]);
    }

    public function paginate($data)
    {
        $cp_area = new AreaSubcontratante();
        $cp = $this->repository;

        if(isset($data['id_area_subcontratante'])){
            $area = TipoAreaSubcontratante::where([['descripcion', 'LIKE', '%'.request('id_area_subcontratante').'%']])->get();

            foreach ($area as $e){
                if(isset($e->id)){
                    $cp_areas = $cp_area::where([['id_area_subcontratante', '=', $e->id]])->get();
                    foreach ($cp_areas as $et){
                        $cp = $cp->whereOr([['id_transaccion', '=', $et->id_transaccion]]);
                    }
                }
            }

        }
        return $this->repository->paginate();
    }

    public function getLayoutData($data){
        $file_xls = $this->getFileXLS($data->nombre_archivo, $data->pagos);
        $partidas = $this->getDatosPartidas($file_xls);

        $index_padre = 0;
        $nivel_anterior = 0;
        $contratos = array();

        foreach($partidas as $key => $partida){
            if(!$partida['descripcion'] || !$partida['nivel']){continue;}

            $destino = '';
            $destino_path = '';
            if($partida['destino'] && $concepto = Concepto::where('clave_concepto', '=', $partida['destino'])->first()){
                if($concepto->es_agrupador){
                    $path = explode('->', $concepto->path);
                    $destino = $concepto->id_concepto;
                    $destino_path = $path[count($path) - 2] . ' -> ' . $concepto->descripcion;
                }
            }
            $contratos[$key] = [
                    'clave' => $partida['clave'],
                    'descripcion' => $partida['descripcion'],
                    'unidad' => $partida['unidad'],
                    'cantidad' => $partida['cantidad'],
                    'destino' => $destino,
                    'destino_path' => $destino_path,
                    'nivel' => (int) $partida['nivel'],
                    'es_hoja' => $partida['cantidad']?true:false,
                    'cantidad_hijos' => 0,
                ];
            if($key == 0){

                $index_padre = $key;
                $nivel_anterior = $partida['nivel'];
                continue;
            }
            if($nivel_anterior + 1 == $partida['nivel']){
                $contratos[$key - 1]['es_hoja'] = false;
                $contratos[$key - 1]['cantidad'] = '';
                $contratos[$key - 1]['unidad'] = '';
                $contratos[$key - 1]['destino'] = '';
                $contratos[$key - 1]['destino_path'] = '';
                $contratos[$key - 1]['cantidad_hijos'] = $contratos[$key - 1]['cantidad_hijos'] + 1;

                $index_padre = $key - 1;
                $nivel_anterior = $partida['nivel'];
                continue;
            }

            if($nivel_anterior == $partida['nivel']){
                $contratos[$index_padre]['cantidad_hijos'] = $contratos[$index_padre]['cantidad_hijos'] + 1;

                continue;
            }

            if($nivel_anterior < $partida['nivel']){
                $index_base = $key - 1;
                while($contratos[$index_base]['nivel'] >= $partida['nivel']){$index_base--;}
                $contratos[$index_base]['cantidad_hijos'] = $contratos[$index_base]['cantidad_hijos'] + 1;
            }

        }

        return $contratos;
    }

    private function getDatosPartidas($file_xls)
    {
        $rows = Excel::toArray(new SolicitudEdicionImport, $file_xls);
        $partidas = [];
        foreach ($rows[0] as $key => $row) {
            $partidas[$key] = [
                'clave' => $row[0],
                'descripcion' => $row[1],
                'nivel' => $row[2],
                'unidad' => $row[3],
                'cantidad' => $row[4],
                'destino' => array_key_exists(5, $row)?$row[5]:null,
            ];
        }
        return $partidas;
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
        $dir_xls = "uploads/contratos/contrato_proyectado/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public  function actualiza($data, $id)
    {
        $area =  $data['id_area'];

        $transaccion = $this->repository->show($id);
        $transaccion_area = $transaccion->areasSubcontratantes;
        if(count($transaccion_area) > 0){
            $solicitud = AreaSubcontratante::find($id);
            $solicitud = $solicitud->actualiza($id, $data['id_area']);
            $transaccion->refresh();
            return $transaccion;

        }else{
            try {
                DB::connection('cadeco')->beginTransaction();
                $datos = [
                    'id_area_subcontratante' => $area,
                    'id_transaccion' => $id,
                ];
                $solicitud = AreaSubcontratante::create($datos);

                DB::connection('cadeco')->commit();
                $transaccion->refresh();
                return $transaccion ;
            } catch (\Exception $e) {
                DB::connection('cadeco')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        }
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }

    public function pdf($id)
    {
        return $this->repository->show($id)->pdf();
    }
}
