<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\FinDimIngresoCliente;
use App\Models\REPSEG\FinDimIngresoEmpresa;
use App\Models\REPSEG\FinFacIngresoFactura;
use App\Models\REPSEG\GrlMoneda;
use App\Models\REPSEG\GrlProyecto;
use App\Repositories\REPSEG\FacturaRepository as Repository;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FacturaService constructor.
     * @param FinFacIngresoFactura $model
     */
    public function __construct(FinFacIngresoFactura $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['idproyecto']))
        {
            $proyectos = GrlProyecto::where([['proyecto', 'LIKE', '%'.$data['idproyecto'].'%']])->pluck("idproyecto");
            $this->repository->whereIn(['idproyecto',  $proyectos]);
        }

        if(isset($data['numero']))
        {
            $this->repository->where([['numero', 'LIKE', '%' . $data['numero'] . '%']]);
        }

        if (isset($data['fecha']))
        {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if (isset($data['idempresa']))
        {
            $empresas = FinDimIngresoEmpresa::where([['empresa', 'LIKE', '%'.$data['idempresa'].'%']])->pluck("idempresa");
            $this->repository->whereIn(['idempresa',  $empresas]);
        }

        if (isset($data['idcliente']))
        {
            $clientes = FinDimIngresoCliente::where([['cliente', 'LIKE', '%'.$data['idcliente'].'%']])->pluck("idcliente");
            $this->repository->whereIn(['idcliente',  $clientes]);
        }

        if(isset($data['descripcion']))
        {
            $this->repository->where([['descripcion', 'LIKE', '%'.$data['descripcion'].'%']]);
        }

        if (isset($data['idmoneda']))
        {
            $monedas = GrlMoneda::where([['moneda', 'LIKE', '%'.$data['idmoneda'].'%']])->pluck("idmoneda");
            $this->repository->whereIn(['idmoneda',  $monedas]);
        }

        if(isset($data['importe']))
        {
            $this->repository->where([['importe', 'LIKE', '%'.$data['importe'].'%']]);
        }

        if (isset($data['fecha_cobro']))
        {
            $this->repository->whereBetween( ['fecha_cobro', [ request( 'fecha_cobro' )." 00:00:00",request( 'fecha_cobro' )." 23:59:59"]] );
        }

        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function cancelar($data , $id)
    {
        return $this->repository->show($id)->cancelar($data['motivo']);
    }

    public function store($data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getLayoutData($data){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $file_xls = $this->getFileXLS($data->nombre_archivo, $data->pagos);
        $partidas = $this->getDatosPartidas($file_xls);

        $index_padre = 0;
        $nivel_anterior = 0;
        $contratos = array();
        $partidas_error = false;
        $partidas_errores = [];
        foreach($partidas as $key => $partida){
            if(!$partida['descripcion'] || !$partida['nivel']){continue;}

            $destino = '';
            $destino_path = '';
            $cantidad = 0;
            $tipo_error = [];

            if(is_numeric($partida["destino"])){
                if($partida['destino'] && $concepto = Concepto::where('clave_concepto', '=', "'" . $partida['destino'] . "'")->orWhere("id_concepto","=",$partida['destino'])->first()){
                    if($concepto->es_agrupador){
                        $path = explode('->', $concepto->path);
                        $destino = $concepto->id_concepto;
                        $destino_path =  $concepto->path_corta;
                    }
                }
            }else{
                if($partida['destino'] && $concepto = Concepto::where('clave_concepto', '=', $partida['destino'])->first()){
                    if($concepto->es_agrupador){
                        $path = explode('->', $concepto->path);
                        $destino = $concepto->id_concepto;
                        $destino_path =  $concepto->path_corta;
                    }
                }
            }

            if($partida['cantidad'] && !is_numeric($partida['cantidad'])){
                $cantidad = 'N/V';
                $partidas_errores[1] = 'Cantidad no válida';
                $tipo_error['cantidad'] = true;
            }else{
                $cantidad = $partida['cantidad'];
            }
            if(strlen($partida['descripcion']) > 255){
                $partidas_errores[0] = 'Descripción mayor a 255 caracteres';
                $tipo_error['descripcion'] = true;
            }

            $dsc_format = str_pad($partida['descripcion'], strlen($partida['descripcion']) + ($partida['nivel'] * 2), '_', STR_PAD_LEFT);
            $contratos[$key] = [
                'clave' => $partida['clave'],
                'descripcion' => $dsc_format,
                'descripcion_sin_formato' => $partida['descripcion'],
                'unidad' => $partida['unidad'],
                'cantidad' => $cantidad,
                'destino' => $destino,
                'destino_path' => $destino_path,
                'nivel' => (int) $partida['nivel'],
                'es_hoja' => $partida['cantidad']?true:false,
                'cantidad_hijos' => 0,
                'partida_valida' => true,
                'tipo_error' => [],
            ];
            if($contratos[$key]['es_hoja']){
                if($destino == ''){
                    $contratos[$key]['destino_path'] = 'N/V';
                    $tipo_error['destino'] = true;
                    $partidas_errores[2] = 'Destino no válido';
                }
                $contratos[$key]['partida_valida'] = count($tipo_error) > 0?false:true;
                $contratos[$key]['tipo_error'] = $tipo_error;
            }
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

        if(count($partidas_errores) > 0){
            $partidas_error = true;
        }
        return ['partidas_con_error' => $partidas_error, 'errores_partidas' => implode(', ', $partidas_errores), 'contratos' =>$contratos];
    }
}
